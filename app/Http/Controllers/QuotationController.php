<?php

namespace App\Http\Controllers;

use App\Models\BankDetail;
use App\Models\Client;
use App\Models\Quotation;
use App\Services\ApproveQuotation;
use App\Services\AuditTrail;
use App\Services\DocumentTotals;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuotationController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('quotationmenu');
        $search = trim((string) $request->input('search'));
        $status = $request->input('status');
        $quotations = Quotation::with(['client', 'user', 'invoice'])
            ->when($search, fn ($q) => $q->where(fn ($qq) => $qq
                ->where('quotation_number', 'like', "%{$search}%")
                ->orWhere('event_name', 'like', "%{$search}%")
                ->orWhereHas('client', fn ($client) => $client->where('name', 'like', "%{$search}%"))))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()->paginate(min(max((int) $request->input('per_page', 10), 5), 100))
            ->withQueryString();

        return view('quotations.index', compact('quotations', 'search', 'status'));
    }

    public function changes(Request $request)
    {
        $this->authorize('quotationmenu');

        return response()->json(['latest' => Quotation::max('updated_at')]);
    }

    public function rows(Request $request)
    {
        $this->authorize('quotationmenu');
        $ids = array_filter(array_map('intval', (array) $request->input('ids', [])));
        $quotations = Quotation::with(['client', 'user', 'invoice'])->whereIn('id', $ids)->latest()->get();

        return view('quotations._rows', compact('quotations'));
    }

    public function create()
    {
        $this->authorize('createquotation');

        return view('quotations.create', [
            'clients' => Client::orderBy('name')->get(),
            'bankDetails' => BankDetail::where('active', true)->orderBy('label')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('createquotation');
        $data = $this->validated($request);
        $quotation = DB::transaction(function () use ($data) {
            [$items, $summary] = $this->prepareItemsAndTotals($data);
            $quotation = Quotation::create($this->header($data, $summary, $this->resolveClientId($data['client_name'])) + [
                'user_id' => auth()->id(),
                'quotation_date' => today(),
                'status' => Quotation::STATUS_DRAFT,
            ]);
            $quotation->items()->createMany($items);
            AuditTrail::record('quotation.created', $quotation, [], $quotation->toArray());

            return $quotation;
        });

        return redirect()->route('quotations.show', $quotation)->with('success', 'Quotation draft berhasil dibuat.');
    }

    public function show(Quotation $quotation)
    {
        $this->authorize('quotationmenu');
        $quotation->load(['client', 'bankDetail', 'user', 'items', 'invoice']);

        return view('quotations.show', compact('quotation'));
    }

    public function edit(Quotation $quotation)
    {
        $this->authorize('editquotation');
        $quotation->load('items');

        return view('quotations.edit', [
            'quotation' => $quotation,
            'clients' => Client::orderBy('name')->get(),
            'bankDetails' => BankDetail::where(fn ($query) => $query->where('active', true)
                ->when($quotation->bank_detail_id, fn ($query) => $query->orWhere('id', $quotation->bank_detail_id)))
                ->orderBy('label')->get(),
        ]);
    }

    public function update(Request $request, Quotation $quotation)
    {
        $this->authorize('editquotation');
        if ($quotation->status === Quotation::STATUS_APPROVED) {
            return back()->with('error', 'Quotation yang sudah disetujui disimpan sebagai riwayat. Edit invoice draft-nya.');
        }
        $data = $this->validated($request);
        DB::transaction(function () use ($quotation, $data) {
            $before = $quotation->toArray();
            [$items, $summary] = $this->prepareItemsAndTotals($data);
            $quotation->update($this->header($data, $summary, $this->resolveClientId($data['client_name'])));
            $quotation->items()->delete();
            $quotation->items()->createMany($items);
            AuditTrail::record('quotation.updated', $quotation, $before, $quotation->fresh()->toArray());
        });

        return redirect()->route('quotations.show', $quotation)->with('success', 'Quotation berhasil diperbarui.');
    }

    public function approve(Quotation $quotation, ApproveQuotation $service)
    {
        $this->authorize('approvequotation');
        $invoice = $service->handle($quotation, (int) auth()->id());

        return redirect()->route('invoices.edit', $invoice)
            ->with('success', 'Quotation disetujui. Invoice draft otomatis dibuat dan masih dapat diedit.');
    }

    public function cancel(Quotation $quotation)
    {
        $this->authorize('editquotation');
        if ($quotation->status === Quotation::STATUS_APPROVED) {
            return back()->with('error', 'Quotation sudah disetujui.');
        }
        $before = $quotation->toArray();
        $quotation->update(['status' => Quotation::STATUS_CANCELLED]);
        AuditTrail::record('quotation.cancelled', $quotation, $before, $quotation->toArray());

        return back()->with('success', 'Quotation dibatalkan.');
    }

    public function destroy(Quotation $quotation)
    {
        $this->authorize('deletequotation');
        if ($quotation->invoice()->exists()) {
            return back()->with('error', 'Quotation yang sudah memiliki invoice tidak dapat dihapus.');
        }
        $quotation->delete();
        AuditTrail::record('quotation.deleted', $quotation);

        return redirect()->route('quotations.index')->with('success', 'Quotation dipindahkan ke arsip.');
    }

    public function exportPdf(Quotation $quotation)
    {
        $this->authorize('quotationmenu');
        $quotation->load(['client', 'bankDetail', 'items']);

        return Pdf::loadView('quotations.pdf', compact('quotation'))
            ->stream('Quotation-'.str_replace('/', '-', $quotation->quotation_number).'.pdf');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'client_name' => ['required', 'string', 'max:255'],
            'bank_detail_id' => ['nullable', 'integer', 'exists:bank_details,id'],
            'event_name' => ['nullable', 'string', 'max:255'],
            'location_event' => ['nullable', 'string', 'max:255'],
            'event_date' => ['nullable', 'date'],
            'loading_date' => ['nullable', 'date'],
            'bongkaran_date' => ['nullable', 'date', 'after_or_equal:loading_date'],
            'description' => ['nullable', 'string', 'max:3000'],
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount_value' => ['nullable', 'string', 'max:30'],
            'tax_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'tax_value' => ['nullable', 'string', 'max:30'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_name' => ['required', 'string', 'max:255'],
            'items.*.qty' => ['required', 'numeric', 'min:0.01'],
            'items.*.length' => ['nullable', 'numeric', 'min:0'],
            'items.*.unit_price' => ['required', 'string', 'max:30'],
            'items.*.merge_price' => ['nullable', 'boolean'],
        ]);
    }

    private function prepareItemsAndTotals(array $data): array
    {
        $items = [];
        $subtotal = 0;
        $leaderIndex = null;
        foreach ($data['items'] as $row) {
            $qty = (float) $row['qty'];
            $length = filled($row['length'] ?? null) ? (float) $row['length'] : null;
            $unitPrice = DocumentTotals::money($row['unit_price']);
            $mergePrice = filter_var($row['merge_price'] ?? false, FILTER_VALIDATE_BOOLEAN);

            if ($mergePrice && $leaderIndex !== null) {
                if (! $items[$leaderIndex]['price_group']) {
                    $group = (string) Str::uuid();
                    $subtotal -= $items[$leaderIndex]['total'];
                    $items[$leaderIndex]['price_group'] = $group;
                    $items[$leaderIndex]['total'] = $items[$leaderIndex]['unit_price'];
                    $subtotal += $items[$leaderIndex]['total'];
                }
                $items[] = [
                    'item_name' => $row['item_name'], 'qty' => $qty, 'length' => $length,
                    'unit_price' => 0, 'total' => 0, 'price_group' => $items[$leaderIndex]['price_group'],
                ];

                continue;
            }

            $total = (int) round($qty * (($length ?? 0) > 0 ? $length : 1) * $unitPrice);
            $subtotal += $total;
            $items[] = [
                'item_name' => $row['item_name'], 'qty' => $qty, 'length' => $length,
                'unit_price' => $unitPrice, 'total' => $total, 'price_group' => null,
            ];
            $leaderIndex = array_key_last($items);
        }
        $summary = DocumentTotals::summarize(
            $subtotal,
            DocumentTotals::decimal($data['discount_percent'] ?? null),
            DocumentTotals::money($data['discount_value'] ?? null),
            DocumentTotals::decimal($data['tax_percent'] ?? null),
            DocumentTotals::money($data['tax_value'] ?? null),
        );

        return [$items, $summary];
    }

    private function header(array $data, array $summary, int $clientId): array
    {
        return [
            'client_id' => $clientId, 'bank_detail_id' => $data['bank_detail_id'] ?? null,
            'event_name' => $data['event_name'] ?? null,
            'location_event' => $data['location_event'] ?? null, 'event_date' => $data['event_date'] ?? null,
            'loading_date' => $data['loading_date'] ?? null, 'bongkaran_date' => $data['bongkaran_date'] ?? null,
            'description' => $data['description'] ?? null, 'subtotal' => $summary['subtotal'],
            'discount_percent' => $summary['discount_percent'], 'discount' => $summary['discount_value'],
            'tax_percent' => $summary['tax_percent'], 'tax_value' => $summary['tax_value'],
            'grand_total' => $summary['grand_total'],
        ];
    }

    private function resolveClientId(string $name): int
    {
        $name = trim((string) preg_replace('/\s+/', ' ', $name));
        $client = Client::query()->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->first();

        return (int) ($client ?: Client::create(['name' => $name]))->id;
    }
}
