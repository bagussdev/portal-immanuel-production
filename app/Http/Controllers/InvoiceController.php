<?php

namespace App\Http\Controllers;

use App\Models\BankDetail;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Services\AuditTrail;
use App\Services\DocumentLocations;
use App\Services\DocumentTotals;
use App\Services\FieldJobSynchronizer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('invoicemenu');
        $search = trim((string) $request->input('search'));
        $status = $request->input('status');
        $history = $request->boolean('history');
        $sort = (string) $request->input('sort');
        $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';
        $sorts = [
            'number' => 'invoice_number',
            'client' => Client::select('name')->whereColumn('clients.id', 'invoices.client_id'),
            'event' => 'event_name',
            'document_date' => 'issue_date',
            'event_date' => 'event_date',
            'status' => 'status',
            'total' => 'grand_total',
            'paid' => 'total_paid',
            'balance' => 'balance_due',
        ];
        if (! array_key_exists($sort, $sorts)) {
            $sort = '';
        }
        $invoices = Invoice::with(['client', 'creator', 'quotation'])
            ->when($search, fn ($q) => $q->where(fn ($qq) => $qq
                ->where('invoice_number', 'like', "%{$search}%")
                ->orWhere('event_name', 'like', "%{$search}%")
                ->orWhereHas('client', fn ($client) => $client->where('name', 'like', "%{$search}%"))))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when(! $status && $history, fn ($q) => $q->where(function ($query) {
                $query->whereIn('status', [Invoice::STATUS_PAID, Invoice::STATUS_VOID])
                    ->orWhere(fn ($overpaid) => $overpaid->where('status', Invoice::STATUS_OVERPAID)->whereNotNull('resolved_at'));
            }))
            ->when(! $status && ! $history, fn ($q) => $q->where(function ($query) {
                $query->whereIn('status', [Invoice::STATUS_DRAFT, Invoice::STATUS_UNPAID, Invoice::STATUS_PARTIAL, Invoice::STATUS_OVERDUE])
                    ->orWhere(fn ($overpaid) => $overpaid->where('status', Invoice::STATUS_OVERPAID)->whereNull('resolved_at'));
            }))
            ->when($sort, fn ($query) => $query->orderBy($sorts[$sort], $direction))
            ->when(! $sort, fn ($query) => $query->latest())
            ->orderByDesc('id')
            ->paginate(min(max((int) $request->input('per_page', 10), 5), 100))
            ->withQueryString();

        return view('invoices.index', compact('invoices', 'search', 'status', 'history', 'sort', 'direction'));
    }

    public function changes()
    {
        $this->authorize('invoicemenu');

        return response()->json(['latest' => Invoice::max('updated_at')]);
    }

    public function rows(Request $request)
    {
        $this->authorize('invoicemenu');
        $ids = array_filter(array_map('intval', (array) $request->input('ids', [])));
        $invoices = Invoice::with(['client', 'creator', 'quotation'])->whereIn('id', $ids)->latest()->get();

        return view('invoices._rows', compact('invoices'));
    }

    public function create()
    {
        $this->authorize('createinvoice');

        return view('invoices.create', [
            'clients' => Client::orderBy('name')->get(),
            'bankDetails' => BankDetail::where('active', true)->orderBy('label')->get(),
            'invoice' => new Invoice(['status' => Invoice::STATUS_DRAFT]),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('createinvoice');
        $data = $this->validated($request);
        $invoice = DB::transaction(function () use ($data) {
            [$locations, $summary] = $this->prepareLocationsAndTotals($data);
            $invoice = Invoice::create($this->header($data, $summary, $this->resolveClientId($data['client_name'])) + [
                'status' => Invoice::STATUS_DRAFT,
                'created_by' => auth()->id(),
            ]);
            $this->saveLocations($invoice, $locations);
            $invoice->recalcTotalsAndStatus();
            AuditTrail::record('invoice.created', $invoice, [], $invoice->toArray());

            return $invoice;
        });

        return redirect()->route('invoices.edit', $invoice)->with('success', 'Invoice draft berhasil dibuat.');
    }

    public function show(Invoice $invoice)
    {
        $this->authorize('invoicemenu');
        $invoice->load(['client', 'bankDetail', 'creator', 'issuer', 'quotation', 'locations.items', 'items', 'fieldJob', 'payments.receiver', 'payments.voider']);

        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $this->authorize('editinvoice');
        if ($invoice->status === Invoice::STATUS_VOID) {
            return redirect()->route('invoices.show', $invoice)->with('error', 'Invoice VOID tidak dapat diedit.');
        }
        $invoice->load('locations.items');

        return view('invoices.edit', [
            'invoice' => $invoice,
            'clients' => Client::orderBy('name')->get(),
            'bankDetails' => BankDetail::where(fn ($query) => $query->where('active', true)
                ->when($invoice->bank_detail_id, fn ($query) => $query->orWhere('id', $invoice->bank_detail_id)))
                ->orderBy('label')->get(),
        ]);
    }

    public function update(Request $request, Invoice $invoice)
    {
        $this->authorize('editinvoice');
        if ($invoice->status === Invoice::STATUS_VOID) {
            return back()->with('error', 'Invoice VOID tidak dapat diedit.');
        }
        $data = $this->validated($request);
        DB::transaction(function () use ($invoice, $data) {
            $before = $invoice->toArray();
            [$locations, $summary] = $this->prepareLocationsAndTotals($data);
            $invoice->update($this->header($data, $summary, $this->resolveClientId($data['client_name'])));
            $invoice->items()->delete();
            $invoice->locations()->delete();
            $this->saveLocations($invoice, $locations);
            $invoice->recalcTotalsAndStatus();
            if ($invoice->status !== Invoice::STATUS_DRAFT) {
                app(FieldJobSynchronizer::class)->sync($invoice, auth()->id());
            }
            AuditTrail::record('invoice.updated', $invoice, $before, $invoice->fresh()->toArray(), $data['change_reason'] ?? null);
        });

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice dan sisa tagihan berhasil dihitung ulang.');
    }

    public function issue(Request $request, Invoice $invoice)
    {
        $this->authorize('issueinvoice');
        $data = $request->validate([
            'issue_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:issue_date'],
        ]);
        if ($invoice->status !== Invoice::STATUS_DRAFT) {
            return back()->with('info', 'Invoice sudah diterbitkan.');
        }

        DB::transaction(function () use ($invoice, $data) {
            $invoice->update([
                'invoice_number' => Invoice::nextNumber(),
                'issue_date' => $data['issue_date'],
                'due_date' => $data['due_date'] ?? $invoice->event_date,
                'issued_at' => now(),
                'issued_by' => auth()->id(),
                'status' => Invoice::STATUS_UNPAID,
            ]);
            $invoice->recalcTotalsAndStatus();
            app(FieldJobSynchronizer::class)->sync($invoice, auth()->id());
            AuditTrail::record('invoice.issued', $invoice, [], ['invoice_number' => $invoice->invoice_number]);
        });

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice berhasil diterbitkan.');
    }

    public function storePayment(Request $request, Invoice $invoice)
    {
        $this->authorize('adddp');
        if (in_array($invoice->status, [Invoice::STATUS_DRAFT, Invoice::STATUS_VOID], true)) {
            return back()->with('error', 'Pembayaran hanya dapat dicatat pada invoice yang sudah diterbitkan.');
        }
        $data = $request->validate([
            'paid_at' => ['required', 'date'],
            'amount' => ['required', 'string', 'max:30'],
            'method' => ['required', 'string', 'max:50'],
            'reference' => ['nullable', 'string', 'max:100'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);
        $amount = DocumentTotals::money($data['amount']);
        if ($amount <= 0) {
            return back()->withErrors(['amount' => 'Nominal pembayaran harus lebih dari nol.'])->withInput();
        }

        $path = $request->file('attachment')?->store('payment-proofs', 'local');
        $payment = $invoice->payments()->create([
            'paid_at' => $data['paid_at'], 'amount' => $amount, 'method' => $data['method'],
            'reference' => $data['reference'] ?? null, 'attachment' => $path,
            'notes' => $data['notes'] ?? null, 'received_by' => auth()->id(),
            'percent' => $invoice->grand_total > 0 ? round(($amount / $invoice->grand_total) * 100, 2) : null,
        ]);
        $invoice->refresh();
        $invoice->recalcTotalsAndStatus();
        AuditTrail::record('invoice.payment_added', $payment, [], $payment->toArray());

        return back()->with('success', 'Pembayaran berhasil dicatat. Pembayaran berikutnya tetap dapat ditambahkan.');
    }

    public function complete(Request $request, Invoice $invoice)
    {
        $this->authorize('adddp');
        if (in_array($invoice->status, [Invoice::STATUS_DRAFT, Invoice::STATUS_VOID, Invoice::STATUS_PAID], true)) {
            return back()->with('error', 'Invoice ini tidak memerlukan penyelesaian.');
        }

        $data = $request->validate([
            'paid_at' => ['nullable', 'date'],
            'method' => ['nullable', 'string', 'max:50'],
            'reference' => ['nullable', 'string', 'max:100'],
            'resolution_note' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($invoice, $data) {
            $invoice->refresh()->recalcTotalsAndStatus();
            if ((int) $invoice->balance_due > 0) {
                $payment = $invoice->payments()->create([
                    'paid_at' => $data['paid_at'] ?? today(),
                    'amount' => (int) $invoice->balance_due,
                    'method' => $data['method'] ?? 'Transfer',
                    'reference' => $data['reference'] ?? 'Pelunasan otomatis',
                    'notes' => $data['resolution_note'] ?? null,
                    'received_by' => auth()->id(),
                ]);
                AuditTrail::record('invoice.completed_payment', $payment, [], $payment->toArray());
                $invoice->refresh()->recalcTotalsAndStatus();
            } elseif ($invoice->status === Invoice::STATUS_OVERPAID) {
                $invoice->update([
                    'resolved_at' => now(), 'resolved_by' => auth()->id(),
                    'resolution_note' => $data['resolution_note'] ?? 'Lebih bayar telah diselesaikan.',
                ]);
                AuditTrail::record('invoice.overpayment_resolved', $invoice, [], $invoice->toArray());
            }
        });

        return back()->with('success', 'Invoice berhasil diselesaikan.');
    }

    public function voidPayment(Request $request, Invoice $invoice, InvoicePayment $payment)
    {
        $this->authorize('voidpayment');
        abort_unless((int) $payment->invoice_id === (int) $invoice->id, 404);
        $data = $request->validate(['reason' => ['required', 'string', 'max:500']]);
        if ($payment->voided_at) {
            return back()->with('info', 'Pembayaran sudah dibatalkan.');
        }
        $payment->update(['voided_at' => now(), 'voided_by' => auth()->id(), 'void_reason' => $data['reason']]);
        $invoice->refresh();
        $invoice->recalcTotalsAndStatus();
        AuditTrail::record('invoice.payment_voided', $payment, [], $payment->toArray(), $data['reason']);

        return back()->with('success', 'Pembayaran dibatalkan dan saldo dihitung ulang.');
    }

    public function paymentAttachment(Invoice $invoice, InvoicePayment $payment)
    {
        $this->authorize('invoicemenu');
        abort_unless((int) $payment->invoice_id === (int) $invoice->id && $payment->attachment, 404);
        abort_unless(Storage::disk('local')->exists($payment->attachment), 404);

        return Storage::disk('local')->download($payment->attachment);
    }

    public function exportPdf(Request $request, Invoice $invoice, ?string $filename = null)
    {
        $this->authorize('invoicemenu');
        $invoice->load(['client', 'bankDetail', 'locations.items', 'items', 'payments' => fn ($q) => $q->whereNull('voided_at')->orderBy('paid_at')]);
        $filename = $invoice->pdfFilename();
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));

        return $request->boolean('download') ? $pdf->download($filename) : $pdf->stream($filename);
    }

    public function void(Request $request, Invoice $invoice)
    {
        $this->authorize('voidinvoice');
        $data = $request->validate(['reason' => ['required', 'string', 'max:500']]);
        if ($invoice->status === Invoice::STATUS_VOID) {
            return back()->with('info', 'Invoice sudah VOID.');
        }
        $before = $invoice->toArray();
        $invoice->update([
            'status' => Invoice::STATUS_VOID, 'voided_at' => now(),
            'voided_by' => auth()->id(), 'void_reason' => $data['reason'],
        ]);
        app(FieldJobSynchronizer::class)->sync($invoice, auth()->id());
        AuditTrail::record('invoice.voided', $invoice, $before, $invoice->toArray(), $data['reason']);

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice ditandai VOID tanpa menghapus riwayat.');
    }

    public function destroy(Invoice $invoice)
    {
        $this->authorize('deleteinvoice');
        if (! in_array($invoice->status, [Invoice::STATUS_DRAFT, Invoice::STATUS_VOID], true)) {
            return back()->with('error', 'Invoice aktif harus di-VOID sebelum diarsipkan.');
        }
        $invoice->delete();
        AuditTrail::record('invoice.archived', $invoice);

        return redirect()->route('invoices.index')->with('success', 'Invoice dipindahkan ke arsip.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'client_name' => ['required', 'string', 'max:255'],
            'bank_detail_id' => ['nullable', 'integer', 'exists:bank_details,id'],
            'event_name' => ['nullable', 'string', 'max:255'],
            'location_event' => ['nullable', 'string', 'max:255'],
            'event_date' => ['nullable', 'date'],
            'event_end_date' => ['nullable', 'date', 'after_or_equal:event_date'],
            'loading_date' => ['nullable', 'date'],
            'bongkaran_date' => ['nullable', 'date', 'after_or_equal:loading_date'],
            'work_flow' => ['nullable', 'in:install_teardown,install_only,one_way'],
            'notes' => ['nullable', 'string', 'max:3000'],
            'operational_notes' => ['nullable', 'string', 'max:3000'],
            'discount_mode' => ['required', 'in:percent,amount'],
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount_value' => ['nullable', 'string', 'max:30'],
            'tax_mode' => ['required', 'in:percent,amount'],
            'tax_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'tax_value' => ['nullable', 'string', 'max:30'],
            'change_reason' => ['nullable', 'string', 'max:500'],
            'items' => ['nullable', 'array', 'min:1', 'required_without:locations'],
            'items.*.item_name' => ['required', 'string', 'max:255'],
            'items.*.qty' => ['required', 'numeric', 'min:0.01'],
            'items.*.length' => ['nullable', 'numeric', 'min:0'],
            'items.*.pricing_mode' => ['nullable', 'in:unit,total'],
            'items.*.unit_price' => ['nullable', 'string', 'max:30'],
            'items.*.line_total' => ['nullable', 'string', 'max:30'],
            'items.*.merge_price' => ['nullable', 'boolean'],
            'locations' => ['nullable', 'array', 'min:1'],
            'locations.*.name' => ['nullable', 'string', 'max:255'],
            'locations.*.event_start_date' => ['nullable', 'date'],
            'locations.*.event_end_date' => ['nullable', 'date', 'after_or_equal:locations.*.event_start_date'],
            'locations.*.loading_date' => ['nullable', 'date'],
            'locations.*.teardown_date' => ['nullable', 'date'],
            'locations.*.work_flow' => ['required_with:locations', 'in:install_teardown,install_only,one_way'],
            'locations.*.items' => ['required_with:locations', 'array', 'min:1'],
            'locations.*.items.*.item_name' => ['required', 'string', 'max:255'],
            'locations.*.items.*.qty' => ['required', 'numeric', 'min:0.01'],
            'locations.*.items.*.length' => ['nullable', 'numeric', 'min:0'],
            'locations.*.items.*.pricing_mode' => ['nullable', 'in:unit,total'],
            'locations.*.items.*.unit_price' => ['nullable', 'string', 'max:30'],
            'locations.*.items.*.line_total' => ['nullable', 'string', 'max:30'],
            'locations.*.items.*.merge_price' => ['nullable', 'boolean'],
        ]);
    }

    private function prepareLocationsAndTotals(array $data): array
    {
        [$locations, $subtotal] = DocumentLocations::prepare(DocumentLocations::normalize($data, Invoice::FLOW_INSTALL_TEARDOWN));
        $discountPercent = $data['discount_mode'] === 'percent' ? DocumentTotals::decimal($data['discount_percent'] ?? null) : null;
        $taxPercent = $data['tax_mode'] === 'percent' ? DocumentTotals::decimal($data['tax_percent'] ?? null) : null;
        $summary = DocumentTotals::summarize(
            $subtotal, $discountPercent, DocumentTotals::money($data['discount_value'] ?? null),
            $taxPercent, DocumentTotals::money($data['tax_value'] ?? null),
        );

        return [$locations, $summary];
    }

    private function header(array $data, array $summary, int $clientId): array
    {
        $first = DocumentLocations::normalize($data, Invoice::FLOW_INSTALL_TEARDOWN)[0];

        return [
            'client_id' => $clientId, 'bank_detail_id' => $data['bank_detail_id'] ?? null,
            'event_name' => $data['event_name'] ?? null,
            'location_event' => $first['name'] ?? null,
            'event_date' => $data['event_date'] ?? $first['event_start_date'] ?? null,
            'event_end_date' => $data['event_end_date'] ?? $first['event_end_date'] ?? null,
            'loading_date' => $first['loading_date'] ?? null, 'bongkaran_date' => $first['teardown_date'] ?? null,
            'work_flow' => $first['work_flow'] ?? Invoice::FLOW_INSTALL_TEARDOWN,
            'notes' => $data['notes'] ?? null, 'operational_notes' => $data['operational_notes'] ?? null,
            'subtotal' => $summary['subtotal'],
            'discount_percent' => $summary['discount_percent'], 'discount_value' => $summary['discount_value'],
            'tax_percent' => $summary['tax_percent'], 'tax_value' => $summary['tax_value'],
            'grand_total' => $summary['grand_total'],
        ];
    }

    private function saveLocations(Invoice $invoice, array $locations): void
    {
        foreach ($locations as $data) {
            $items = $data['items'];
            unset($data['items']);
            $location = $invoice->locations()->create($data);
            $items = array_map(fn (array $item) => $item + ['invoice_id' => $invoice->id], $items);
            $location->items()->createMany($items);
        }
    }

    private function resolveClientId(string $name): int
    {
        $name = trim((string) preg_replace('/\s+/', ' ', $name));
        $client = Client::query()->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->first();

        return (int) ($client ?: Client::create(['name' => $name]))->id;
    }
}
