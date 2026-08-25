<?php

namespace App\Services;

use App\Models\FieldJob;
use App\Models\FieldJobItem;
use App\Models\FieldJobSite;
use App\Models\FieldJobStage;
use App\Models\Invoice;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FieldJobSynchronizer
{
    public function sync(Invoice $invoice, ?int $createdBy = null): Collection
    {
        if ($invoice->status === Invoice::STATUS_DRAFT) {
            return collect();
        }

        return DB::transaction(function () use ($invoice, $createdBy) {
            $invoice = Invoice::query()->with(['client', 'locations.items', 'items'])->lockForUpdate()->findOrFail($invoice->id);
            $sourceLocations = $invoice->locations->isNotEmpty()
                ? $invoice->locations
                : collect([(object) [
                    'id' => null, 'name' => $invoice->location_event,
                    'event_start_date' => $invoice->event_date, 'event_end_date' => $invoice->event_end_date,
                    'loading_date' => $invoice->loading_date, 'teardown_date' => $invoice->bongkaran_date,
                    'work_flow' => $invoice->work_flow, 'sort_order' => 0, 'items' => $invoice->items,
                ]]);

            $jobs = FieldJob::query()->where('invoice_id', $invoice->id)
                ->with('sites')->lockForUpdate()->orderBy('id')->get();
            $knownSites = $jobs->flatMap->sites->values();
            $claimedJobIds = collect();
            $activeSiteByJob = collect();

            foreach ($sourceLocations as $source) {
                $site = $knownSites->first(fn (FieldJobSite $candidate) => $source->id && (int) $candidate->invoice_location_id === (int) $source->id
                );
                $site ??= $knownSites->first(fn (FieldJobSite $candidate) => ! $activeSiteByJob->contains($candidate->id)
                    && filled($source->name)
                    && $candidate->name === $source->name
                );

                $job = $site && ! $claimedJobIds->contains($site->field_job_id)
                    ? $jobs->firstWhere('id', $site->field_job_id)
                    : null;
                $job ??= $jobs->first(fn (FieldJob $candidate) => ! $claimedJobIds->contains($candidate->id)
                    && filled($source->name)
                    && $candidate->location === $source->name
                );
                $job ??= $jobs->first(fn (FieldJob $candidate) => ! $claimedJobIds->contains($candidate->id));
                $job ??= $this->createJob($invoice, $createdBy);

                $claimedJobIds->push($job->id);
                if ($site && (int) $site->field_job_id !== (int) $job->id) {
                    $this->moveSite($site, $job);
                }
                $site ??= $job->sites()->firstOrNew(['invoice_location_id' => $source->id]);

                $job->update([
                    'client_name' => $invoice->client?->name ?: $job->client_name,
                    'event_name' => $invoice->event_name,
                    'location' => $source->name ?: $invoice->location_event,
                    'event_date' => $invoice->event_date,
                    'loading_date' => $source->loading_date ?: $invoice->loading_date,
                    'teardown_date' => $source->teardown_date ?: $invoice->bongkaran_date,
                    'notes' => $invoice->operational_notes,
                    'status' => $job->status === FieldJob::STATUS_CANCELLED ? FieldJob::STATUS_PENDING : $job->status,
                ]);

                $site->fill([
                    'field_job_id' => $job->id,
                    'invoice_location_id' => $source->id,
                    'name' => $source->name,
                    'event_start_date' => $source->event_start_date,
                    'event_end_date' => $source->event_end_date,
                    'loading_date' => $source->loading_date,
                    'teardown_date' => $source->teardown_date,
                    'work_flow' => $source->work_flow ?: Invoice::FLOW_INSTALL_TEARDOWN,
                    'sort_order' => $source->sort_order,
                ])->save();
                $activeSiteByJob->put($job->id, $site->id);

                $site->items()->delete();
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
                    if (! $stage->exists) {
                        $stage->status = FieldJobStage::STATUS_PENDING;
                    }
                    $stage->save();
                }
                $job->stages()->where('field_job_site_id', $site->id)->whereNotIn('type', $requiredTypes)->update(['is_active' => false]);
            }

            FieldJob::query()->where('invoice_id', $invoice->id)->with('sites')->orderBy('id')->get()
                ->each(function (FieldJob $job) use ($invoice, $createdBy, $activeSiteByJob): void {
                    $keepSiteId = $activeSiteByJob->get($job->id) ?: $job->sites->first()?->id;
                    $job->sites->where('id', '!=', $keepSiteId)->each(function (FieldJobSite $site) use ($invoice, $createdBy): void {
                        $splitJob = $this->createJob($invoice, $createdBy);
                        $splitJob->update([
                            'location' => $site->name,
                            'event_date' => $invoice->event_date,
                            'loading_date' => $site->loading_date,
                            'teardown_date' => $site->teardown_date,
                            'status' => FieldJob::STATUS_CANCELLED,
                        ]);
                        $this->moveSite($site, $splitJob);
                        $splitJob->stages()->update(['is_active' => false]);
                    });
                });

            $allJobs = FieldJob::query()->where('invoice_id', $invoice->id)->orderBy('id')->get();
            foreach ($allJobs as $job) {
                if ($invoice->status === Invoice::STATUS_VOID || ! $claimedJobIds->contains($job->id)) {
                    $job->update(['status' => FieldJob::STATUS_CANCELLED]);
                    $job->stages()->update(['is_active' => false]);
                } else {
                    $job->refresh()->recalculateStatus();
                }
            }

            return FieldJob::query()->where('invoice_id', $invoice->id)
                ->with(['items', 'stages', 'sites'])->orderBy('id')->get();
        });
    }

    private function createJob(Invoice $invoice, ?int $createdBy): FieldJob
    {
        return FieldJob::create([
            'invoice_id' => $invoice->id,
            'job_number' => FieldJob::nextNumber(),
            'client_name' => $invoice->client?->name ?: 'Client',
            'event_name' => $invoice->event_name,
            'event_date' => $invoice->event_date,
            'notes' => $invoice->operational_notes,
            'status' => FieldJob::STATUS_PENDING,
            'created_by' => $createdBy ?: $invoice->issued_by ?: $invoice->created_by,
        ]);
    }

    private function moveSite(FieldJobSite $site, FieldJob $job): void
    {
        $site->forceFill(['field_job_id' => $job->id])->save();
        FieldJobItem::query()->where('field_job_site_id', $site->id)->update(['field_job_id' => $job->id]);
        FieldJobStage::query()->where('field_job_site_id', $site->id)->update(['field_job_id' => $job->id]);
    }
}
