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
            $invoice = Invoice::query()->with(['client', 'items'])->lockForUpdate()->findOrFail($invoice->id);
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
            $workFlow = $invoice->work_flow ?: Invoice::FLOW_INSTALL_TEARDOWN;
            foreach ($invoice->items as $item) {
                $job->items()->create([
                    'invoice_item_id' => $item->id,
                    'item_name' => $item->item_name,
                    'qty' => $item->qty,
                    'length' => $item->length,
                    'work_flow' => $workFlow,
                ]);
            }

            $requiredTypes = collect(match ($workFlow) {
                Invoice::FLOW_INSTALL_ONLY => [FieldJobStage::TYPE_INSTALL],
                Invoice::FLOW_ONE_WAY => [FieldJobStage::TYPE_ONE_WAY],
                default => [FieldJobStage::TYPE_INSTALL, FieldJobStage::TYPE_TEARDOWN],
            });

            $schedules = [
                FieldJobStage::TYPE_INSTALL => $invoice->loading_date ?: $invoice->event_date,
                FieldJobStage::TYPE_TEARDOWN => $invoice->bongkaran_date,
                FieldJobStage::TYPE_ONE_WAY => $invoice->loading_date ?: $invoice->event_date,
            ];

            foreach ($requiredTypes as $type) {
                $stage = $job->stages()->firstOrNew(['type' => $type]);
                $stage->scheduled_at = $schedules[$type] ?? null;
                $stage->is_active = true;
                if (! $stage->exists) {
                    $stage->status = FieldJobStage::STATUS_PENDING;
                }
                $stage->save();
            }

            $job->stages()->whereNotIn('type', $requiredTypes)->update(['is_active' => false]);
            $job->refresh()->recalculateStatus();

            return $job->fresh(['items', 'stages']);
        });
    }
}
