<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Quotation;
use Illuminate\Support\Facades\DB;

class ApproveQuotation
{
    public function handle(Quotation $quotation, int $userId): Invoice
    {
        return DB::transaction(function () use ($quotation, $userId) {
            $quotation = Quotation::query()->with(['locations.items', 'items'])->lockForUpdate()->findOrFail($quotation->id);

            if ($quotation->invoice) {
                return $quotation->invoice;
            }
            if (! in_array($quotation->status, [Quotation::STATUS_DRAFT, Quotation::STATUS_SENT], true)) {
                abort(422, 'Quotation tidak dapat disetujui dari status saat ini.');
            }

            $quotation->update([
                'status' => Quotation::STATUS_APPROVED,
                'approved_at' => now(),
                'approved_by' => $userId,
                'converted_to_invoice' => true,
            ]);

            $invoice = Invoice::create([
                'client_id' => $quotation->client_id,
                'bank_detail_id' => $quotation->bank_detail_id,
                'quotation_id' => $quotation->id,
                'event_name' => $quotation->event_name,
                'location_event' => $quotation->location_event,
                'event_date' => $quotation->event_date,
                'event_end_date' => $quotation->event_end_date,
                'loading_date' => $quotation->loading_date,
                'bongkaran_date' => $quotation->bongkaran_date,
                'work_flow' => Invoice::FLOW_INSTALL_TEARDOWN,
                'status' => Invoice::STATUS_DRAFT,
                'discount_percent' => $quotation->discount_percent,
                'discount_value' => $quotation->discount,
                'tax_percent' => $quotation->tax_percent,
                'tax_value' => $quotation->tax_value,
                'notes' => $quotation->description,
                'created_by' => $userId,
            ]);

            $locations = $quotation->locations->isNotEmpty()
                ? $quotation->locations
                : collect([(object) [
                    'id' => null, 'name' => $quotation->location_event,
                    'event_start_date' => $quotation->event_date, 'event_end_date' => $quotation->event_end_date,
                    'loading_date' => $quotation->loading_date, 'teardown_date' => $quotation->bongkaran_date,
                    'work_flow' => Invoice::FLOW_INSTALL_TEARDOWN, 'sort_order' => 0, 'items' => $quotation->items,
                ]]);

            foreach ($locations as $sourceLocation) {
                $invoiceLocation = $invoice->locations()->create([
                    'quotation_location_id' => $sourceLocation->id,
                    'name' => $sourceLocation->name,
                    'event_start_date' => $sourceLocation->event_start_date,
                    'event_end_date' => $sourceLocation->event_end_date,
                    'loading_date' => $sourceLocation->loading_date,
                    'teardown_date' => $sourceLocation->teardown_date,
                    'work_flow' => $sourceLocation->work_flow ?: Invoice::FLOW_INSTALL_TEARDOWN,
                    'sort_order' => $sourceLocation->sort_order,
                ]);
                foreach ($sourceLocation->items as $item) {
                    $invoiceLocation->items()->create([
                        'invoice_id' => $invoice->id,
                        'qty' => $item->qty, 'item_name' => $item->item_name, 'length' => $item->length,
                        'pricing_mode' => $item->pricing_mode, 'unit_price' => $item->unit_price,
                        'total' => $item->total, 'price_group' => $item->price_group,
                    ]);
                }
            }

            $invoice->recalcTotalsAndStatus();
            AuditTrail::record('quotation.approved', $quotation, [], ['invoice_id' => $invoice->id]);

            return $invoice;
        });
    }
}
