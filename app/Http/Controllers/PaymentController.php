<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('paymentsmenu');
        // ====== Params dasar ======
        $mode = $request->string('mode')->lower()->value() ?: 'monthly'; // 'monthly' | 'weekly'
        $month = $request->input('month', now()->format('Y-m'));
        $weekStart = $request->input('week_start', now()->startOfWeek(Carbon::MONDAY)->toDateString());
        $search = trim($request->input('search', ''));
        $exactDate = $request->input('exact_date'); // YYYY-MM-DD (opsional)
        $perPageRaw = $request->input('per_page', '5');
        $sortRaw = $request->input('sort', 'paid_at:desc'); // kolom:arah

        // ====== Range waktu ======
        if ($mode === 'weekly') {
            $start = Carbon::parse($weekStart)->startOfDay();
            $end = (clone $start)->addDays(6)->endOfDay();
            $periodLabel = $start->translatedFormat('d M Y').' – '.$end->translatedFormat('d M Y');
        } else {
            $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth()->startOfDay();
            $end = (clone $start)->endOfMonth()->endOfDay();
            $periodLabel = $start->translatedFormat('F Y');
        }

        // ====== Sort parser ======
        $sortKey = 'paid_at';
        $sortDir = 'desc';
        if (is_string($sortRaw) && str_contains($sortRaw, ':')) {
            [$k, $d] = explode(':', $sortRaw, 2);
            $k = trim($k);
            $d = strtolower(trim($d));
            if (in_array($k, ['invoice_number', 'paid_at', 'client_name', 'amount', 'received_by'], true)) {
                $sortKey = $k;
            }
            if (in_array($d, ['asc', 'desc'], true)) {
                $sortDir = $d;
            }
        }

        // ====== Base query (JOIN untuk ambil meta) ======
        $q = DB::table('invoice_payments as p')
            ->leftJoin('invoices as i', 'i.id', '=', 'p.invoice_id')
            ->leftJoin('clients as c', 'c.id', '=', 'i.client_id')
            ->leftJoin('users as u', 'u.id', '=', 'p.received_by')
            ->whereNull('p.voided_at')
            ->whereBetween('p.paid_at', [$start, $end]);

        if ($exactDate) {
            // override: hanya tanggal tertentu
            $q->whereDate('p.paid_at', '=', $exactDate);
        }

        if ($search !== '') {
            $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $search).'%';
            $q->where(function ($qq) use ($like) {
                $qq->where('i.invoice_number', 'like', $like)
                    ->orWhere('c.name', 'like', $like)
                    ->orWhere('p.notes', 'like', $like)
                    ->orWhere('u.name', 'like', $like);
            });
        }

        // ====== Clones untuk summary (tanpa pagination) ======
        $sumQ = clone $q;
        $countQ = clone $q;
        $avgQ = clone $q;
        $maxQ = clone $q;

        $totalAmount = (int) ($sumQ->sum('p.amount') ?? 0);
        $count = (int) ($countQ->count('p.id') ?? 0);
        $avgAmount = $count > 0 ? (int) floor($totalAmount / $count) : 0;

        $maxPayment = $maxQ->select([
            'p.amount',
            'i.id as invoice_id',
            'i.invoice_number',
            'c.name as client_name',
        ])
            ->orderByDesc('p.amount')
            ->limit(1)
            ->first();
        $maxPaymentArr = $maxPayment ? [
            'amount' => (int) $maxPayment->amount,
            'invoice_id' => $maxPayment->invoice_id,
            'invoice_number' => $maxPayment->invoice_number,
            'client_name' => $maxPayment->client_name,
        ] : [];

        // ====== Sorting kolom terhitung ======
        // mapping kolom sort → ekspresi/order
        $sortMap = [
            'invoice_number' => 'i.invoice_number',
            'paid_at' => 'p.paid_at',
            'client_name' => 'c.name',
            'amount' => 'p.amount',
            'received_by' => 'u.name',
        ];
        $orderCol = $sortMap[$sortKey] ?? 'p.paid_at';
        $q->orderBy($orderCol, $sortDir)->orderBy('p.id', 'desc');

        // ====== Per page ======
        $showPagination = true;
        if ($perPageRaw === 'all') {
            $itemsRows = $q->select([
                'p.id',
                'p.invoice_id',
                'p.paid_at',
                'p.percent',
                'p.amount',
                'p.notes',
                'i.grand_total',
                'i.invoice_number',
                'c.name as client_name',
                'u.name as received_by',
            ])->get();
            $showPagination = false;
        } else {
            $perPage = max(1, (int) $perPageRaw ?: 5);
            $itemsRows = $q->select([
                'p.id',
                'p.invoice_id',
                'p.paid_at',
                'p.percent',
                'p.amount',
                'p.notes',
                'i.grand_total',
                'i.invoice_number',
                'c.name as client_name',
                'u.name as received_by',
            ])->paginate($perPage)->withQueryString();
        }

        // ====== Map rows → array untuk view ======
        $mapRow = function ($r) {
            $paidAtHuman = $r->paid_at ? Carbon::parse($r->paid_at)->translatedFormat('d M Y') : '';

            return [
                'id' => (int) $r->id,
                'invoice_id' => $r->invoice_id,
                'invoice_number' => (string) ($r->invoice_number ?? ''),
                'paid_at' => $r->paid_at,
                'paid_at_human' => $paidAtHuman,
                'client_name' => (string) ($r->client_name ?? ''),
                'percent' => (int) ($r->grand_total ?? 0) > 0
                    ? round(((int) $r->amount / (int) $r->grand_total) * 100, 2)
                    : null,
                'amount' => (int) $r->amount,
                'notes' => (string) ($r->notes ?? ''),
                'received_by' => (string) ($r->received_by ?? ''),
            ];
        };

        if ($showPagination) {
            $items = $itemsRows->getCollection()->map($mapRow)->values();
            // bungkus lagi ke paginator supaya komponenmu bisa panggil ->links()
            $items = new LengthAwarePaginator(
                $items,
                $itemsRows->total(),
                $itemsRows->perPage(),
                $itemsRows->currentPage(),
                ['path' => request()->url(), 'query' => request()->query()]
            );
            $perPage = $itemsRows->perPage();
        } else {
            $items = collect($itemsRows)->map($mapRow)->values();
            $perPage = 'all';
        }

        return view('payments.index', [
            'mode' => $mode,
            'month' => $month,
            'weekStart' => $weekStart,
            'search' => $search,
            'exactDate' => $exactDate,
            'perPage' => $perPage,
            'periodLabel' => $periodLabel,
            'showPagination' => $showPagination,

            'totalAmount' => $totalAmount,
            'count' => $count,
            'avgAmount' => $avgAmount,
            'maxPayment' => $maxPaymentArr,

            'items' => $items,
            'sort' => "{$sortKey}:{$sortDir}",
        ]);
    }
}
