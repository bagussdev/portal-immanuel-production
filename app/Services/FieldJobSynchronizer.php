<?php

namespace App\Services;

use App\Models\FieldJob;
use App\Models\FieldJobStage;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class FieldJobSynchronizer
{
    public function sync(Invoice $invoice, ?int $createdBy = null): ?FieldJob
    {
        if ($invoice->status === Invoice::STATUS_DRAFT) {
            return null;
        }

        return DB::transaction(function () use ($invoice, $createdBy) {
            $invoice = Invoice::query()->with(['client', 'locations.items', 'items'])->lockForUpdate()->findOrFail($invoice->id);
            $job = FieldJob::query()->where('invoice_id', $invoice->id)->lockForUpdate()->first();

            if (! $job) {
                $job = FieldJob::create([
                    'invoice_id' => $invoice->id,
                    'job_number' => FieldJob::nextNumber(),
                    'client_name' => $invoice->client?->name ?: 'Client',
                    'status' => FieldJob::STATUS_PENDING,
                    'created_by' => $createdBy ?: $invoice->issued_by ?: $invoice->created_by,
                ]);
            }

            if ($invoice->status === Invoice::STATUS_VOID) {
                $job->update(['status' => FieldJob::STATUS_CANCELLED]);
                $job->stages()->update(['is_active' => false]);

                return $job->fresh();
            }

            $job->update([
                'client_name' => $invoice->client?->name ?: $job->client_name,
                'event_name' => $invoice->event_name,
                'location' => $invoice->location_event,
                'event_date' => $invoice->event_date,
                'loading_date' => $invoice->loading_date,
                'teardown_date' => $invoice->bongkaran_date,
                'notes' => $invoice->operational_notes,
                'status' => $job->status === FieldJob::STATUS_CANCELLED ? FieldJob::STATUS_PENDING : $job->status,
            ]);

            $job->items()->delete();
            $sourceLocations = $invoice->locations->isNotEmpty()
                ? $invoice->locations
                : collect([(object) [
                    'id' => null, 'name' => $invoice->location_event,
                    'event_start_date' => $invoice->event_date, 'event_end_date' => $invoice->event_end_date,
                    'loading_date' => $invoice->loading_date, 'teardown_date' => $invoice->bongkaran_date,
                    'work_flow' => $invoice->work_flow, 'sort_order' => 0, 'items' => $invoice->items,
                ]]);
            $activeSiteIds = [];

            foreach ($sourceLocations as $source) {
                $site = $job->sites()->firstOrNew(['invoice_location_id' => $source->id]);
                $site->fill([
                    'name' => $source->name,
                    'event_start_date' => $source->event_start_date,
                    'event_end_date' => $source->event_end_date,
                    'loading_date' => $source->loading_date,
                    'teardown_date' => $source->teardown_date,
                    'work_flow' => $source->work_flow ?: Invoice::FLOW_INSTALL_TEARDOWN,
                    'sort_order' => $source->sort_order,
                ])->save();
                $activeSiteIds[] = $site->id;

                foreach ($source->items as $item) {
                    $site->items()->create([
                        'field_job_id' => $job->id, 'invoice_item_id' => $item->id,
                        'item_name' => $item->item_name, 'qty' => $item->qty, 'length' => $item->length,
                        'work_flow' => $site->work_flow,
                    ]);
                }

                $requiredTypes = collect(match ($site->work_flow) {
                    Invoice::FLOW_INSTALL_ONLY => [FieldJobStage::TYPE_INSTALL],
                    Invoice::FLOW_ONE_WAY => [FieldJobStage::TYPE_ONE_WAY],
                    default => [FieldJobStage::TYPE_INSTALL, FieldJobStage::TYPE_TEARDOWN],
                });
                $schedules = [
                    FieldJobStage::TYPE_INSTALL => $site->loading_date ?: $site->event_start_date,
                    FieldJobStage::TYPE_TEARDOWN => $site->teardown_date,
                    FieldJobStage::TYPE_ONE_WAY => $site->loading_date ?: $site->event_start_date,
                ];
                foreach ($requiredTypes as $type) {
                    $stage = $job->stages()->firstOrNew(['field_job_site_id' => $site->id, 'type' => $type]);
                    $stage->scheduled_at = $schedules[$type] ?? null;
                    $stage->is_active = true;
                    if (! $stage->exists) $stage->status = FieldJobStage::STATUS_PENDING;
                    $stage->save();
                }
                $job->stages()->where('field_job_site_id', $site->id)->whereNotIn('type', $requiredTypes)->update(['is_active' => false]);
            }

            $staleSiteIds = $job->sites()->whereNotIn('id', $activeSiteIds)->pluck('id');
            $job->stages()->whereIn('field_job_site_id', $staleSiteIds)->update(['is_active' => false]);
            $job->sites()->whereIn('id', $staleSiteIds)->delete();
            $job->refresh()->recalculateStatus();

            return $job->fresh(['items', 'stages']);
        });
    }
}
