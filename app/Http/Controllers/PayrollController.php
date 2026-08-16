<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Models\PayrollPeriod;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PayrollController extends Controller
{
    use AuthorizesRequests;

    /* =========================================================
     | Helpers
     * ========================================================*/

    /** Bersihkan input currency ke integer Rupiah (tanpa desimal). */
    private function cleanCurrency($value): int
    {
        if ($value === null || $value === '') {
            return 0;
        }
        $s = trim((string) $value);
        // buang pecahan di ujung (",00" atau ".50")
        $s = preg_replace('/([.,]\d{1,2})\s*$/', '', $s);
        // sisakan digit saja
        $s = preg_replace('/[^\d]/', '', $s);

        return (int) ($s === '' ? 0 : $s);
    }

    /** True jika (month,year) lebih kecil dari bulan berjalan. */
    private function isPastPeriod(int $month, int $year): bool
    {
        $nowY = (int) now()->year;
        $nowM = (int) now()->month;

        return ($year < $nowY) || ($year === $nowY && $month < $nowM);
    }

    /** True jika (month,year) sama dengan bulan berjalan. */
    private function isCurrentPeriod(int $month, int $year): bool
    {
        return ((int) now()->month === $month) && ((int) now()->year === $year);
    }

    /* =========================================================
     | INDEX
     * ========================================================*/
    public function index(Request $request)
    {
        $this->authorize('payrollmenu');

        $user = $request->user();
        $isCrew = ! $user->canViewAllPayrolls();

        $month = (int) ($request->input('month') ?: now()->month);
        $year = (int) ($request->input('year') ?: now()->year);
        $search = trim((string) $request->input('search', ''));
        $perPage = (int) ($request->input('per_page') ?: 5);
        if ($perPage <= 0) {
            $perPage = 5;
        }

        $period = PayrollPeriod::forMonthYear($month, $year)->first();

        // ===== TABEL
        if ($period) {
            $q = Payroll::query()
                ->with('user')
                ->where('payroll_period_id', $period->id)
                ->leftJoin('users', 'users.id', '=', 'payrolls.user_id')
                ->select('payrolls.*');

            if ($isCrew) {
                $q->where('payrolls.user_id', $user->id);
            }

            if ($search !== '') {
                $q->where(fn ($qq) => $qq->where('users.name', 'like', "%{$search}%"));
            }

            $rows = $q->orderBy('users.name')
                ->paginate($perPage)
                ->appends($request->query());
        } else {
            $rows = new LengthAwarePaginator([], 0, $perPage, 1, [
                'path' => $request->url(),
                'query' => $request->query(),
            ]);
        }

        // ===== STATS periode
        if ($period) {
            $baseQ = Payroll::where('payroll_period_id', $period->id);
            if ($isCrew) {
                $baseQ->where('user_id', $user->id);
            }

            $total = (clone $baseQ)->count();
            $draft = (clone $baseQ)->where('status', 'draft')->count();
            $paid = (clone $baseQ)->where('status', 'paid')->count();

            $sums = (clone $baseQ)
                ->selectRaw('COALESCE(SUM(total_base),0) as base, COALESCE(SUM(total_deductions),0) as ded, COALESCE(SUM(net_pay),0) as net')
                ->first();

            $stats = [
                'total' => (int) $total,
                'draft' => (int) $draft,
                'paid' => (int) $paid,
                'base' => (int) ($sums->base ?? 0),
                'ded' => (int) ($sums->ded ?? 0),
                'net' => (int) ($sums->net ?? 0),
            ];
        } else {
            $stats = ['total' => 0, 'draft' => 0, 'paid' => 0, 'base' => 0, 'ded' => 0, 'net' => 0];
        }

        // ===== LOCKS
        $isPast = $this->isPastPeriod($month, $year);
        $isCurrent = $this->isCurrentPeriod($month, $year);

        if ($isCrew) {
            $locks = [
                'period_exists' => (bool) $period,
                'can_open' => false,
                'can_add' => false,
                'can_close' => false,
                'can_reopen' => false,
                'is_past' => $isPast,
                'is_current' => $isCurrent,
            ];
        } else {
            $locks = [
                'period_exists' => (bool) $period,
                'can_open' => $user->can('managepayroll') && ! $period && $isCurrent,
                'can_add' => $user->can('addpayroll') && $period && in_array($period->status, [
                    PayrollPeriod::STATUS_OPEN,
                    PayrollPeriod::STATUS_REOPEN,
                ], true),
                'can_close' => $user->can('managepayroll') && $period && $stats['total'] > 0
                    && $stats['paid'] === $stats['total']
                    && $period->status !== PayrollPeriod::STATUS_CLOSED,
                'can_reopen' => $user->can('managepayroll') && $period && $period->status === PayrollPeriod::STATUS_CLOSED,
                'is_past' => $isPast,
                'is_current' => $isCurrent,
            ];
        }

        // ===== seed latestTs untuk polling
        if ($period) {
            $rawLatest = Payroll::where('payroll_period_id', $period->id)
                ->select(DB::raw('GREATEST(MAX(updated_at), MAX(created_at)) as ts'))
                ->value('ts');
            $latestTs = $rawLatest ? Carbon::parse($rawLatest)->toIso8601String() : now()->toIso8601String();
        } else {
            $latestTs = null;
        }

        return view('payroll.index', compact(
            'rows',
            'period',
            'month',
            'year',
            'search',
            'perPage',
            'stats',
            'locks',
            'latestTs'
        ))->with('isCrew', $isCrew);
    }

    /* =========================================================
     | PERIOD ACTIONS
     * ========================================================*/

    public function openPeriod(Request $request)
    {
        $this->authorize('managepayroll');
        $data = $request->validate([
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
        ]);

        $month = (int) $data['month'];
        $year = (int) $data['year'];

        if (! $this->isCurrentPeriod($month, $year)) {
            return redirect()->route('payroll.index', $data)
                ->with('error', 'Hanya periode bulan & tahun berjalan yang bisa di-OPEN. Untuk periode lampau gunakan REOPEN (jika sudah ada).');
        }

        $existing = PayrollPeriod::forMonthYear($month, $year)->first();
        if ($existing) {
            return redirect()->route('payroll.index', $data)
                ->with('info', 'Periode sudah ada.');
        }

        PayrollPeriod::create([
            'month' => $month,
            'year' => $year,
            'status' => PayrollPeriod::STATUS_OPEN,
            'open_by' => Auth::id(),
            'open_at' => now(),
        ]);

        return redirect()->route('payroll.index', $data)->with('success', 'Periode dibuka (OPEN).');
    }

    public function closePeriod(PayrollPeriod $period, Request $request)
    {
        $this->authorize('managepayroll');
        if (! in_array($period->status, [PayrollPeriod::STATUS_OPEN, PayrollPeriod::STATUS_REOPEN], true)) {
            return back()->with('error', 'Hanya bisa CLOSE dari status OPEN/REOPEN.');
        }

        $total = $period->payrolls()->count();
        $paid = $period->payrolls()->where('status', Payroll::STATUS_PAID ?? 'paid')->count();

        if ($total === 0) {
            return back()->with('error', 'Tidak ada payroll pada periode ini.');
        }
        if ($paid < $total) {
            return back()->with('error', "Progress paid belum 100% ($paid/$total). Selesaikan semua pembayaran terlebih dahulu.");
        }

        $period->update([
            'status' => PayrollPeriod::STATUS_CLOSED,
            'closed_by' => Auth::id(),
            'closed_at' => now(),
        ]);

        return redirect()->route('payroll.index', [
            'month' => $period->month,
            'year' => $period->year,
        ])->with('success', 'Periode ditutup (CLOSED).');
    }

    public function reopenPeriod(PayrollPeriod $period, Request $request)
    {
        $this->authorize('managepayroll');
        if ($period->status !== PayrollPeriod::STATUS_CLOSED) {
            return back()->with('error', 'Hanya bisa REOPEN dari status CLOSED.');
        }

        $period->update([
            'status' => PayrollPeriod::STATUS_REOPEN,
            'reopened_by' => Auth::id(),
            'reopened_at' => now(),
        ]);

        return redirect()->route('payroll.index', [
            'month' => $period->month,
            'year' => $period->year,
        ])->with('success', 'Periode dibuka kembali (REOPEN).');
    }

    /* =========================================================
     | SLIP-LEVEL actions
     * ========================================================*/

    public function pay(Payroll $payroll, Request $r)
    {
        $this->authorize('paypayroll');
        $period = PayrollPeriod::find($payroll->payroll_period_id);
        if (! $period || ! in_array($period->status, [PayrollPeriod::STATUS_OPEN, PayrollPeriod::STATUS_REOPEN], true)) {
            return back()->with('error', 'Slip hanya bisa dibayar saat periode OPEN/REOPEN.');
        }
        if ($payroll->status !== Payroll::STATUS_DRAFT) {
            return back()->with('error', 'Hanya slip DRAFT yang dapat ditandai dibayar.');
        }

        $payroll->markPaid((int) Auth::id());

        return redirect()->route('payroll.index', [
            'month' => (int) ($r->input('month') ?: $period->month),
            'year' => (int) ($r->input('year') ?: $period->year),
        ])->with('success', 'Slip ditandai PAID.');
    }

    /* =========================================================
     | SHOW & PDF (TETAP seperti sebelumnya)
     * ========================================================*/

    public function show(Request $r, Payroll $payroll)
    {
        $this->authorize('payrollmenu');

        // Crew hanya boleh lihat slip miliknya
        if (
            ! $r->user()->canViewAllPayrolls()
            && (int) $payroll->user_id !== (int) $r->user()->id
        ) {
            abort(403, 'Forbidden');
        }

        $payroll->load('user', 'period');

        // === Base: sekarang list
        $baseItems = $payroll->items()->where('type', 'base')->orderBy('id')->get();
        $deductionItems = $payroll->items()->where('type', 'deduction')->orderBy('id')->get();

        return view('payroll.show', [
            'payroll' => $payroll,
            'baseItems' => $baseItems,
            'deductionItems' => $deductionItems,
            'month' => $r->integer('month') ?: $payroll->period?->month,
            'year' => $r->integer('year') ?: $payroll->period?->year,
        ]);
    }

    public function slipPdf(Payroll $payroll)
    {
        $this->authorize('payrollmenu');

        if (
            ! Auth::user()->canViewAllPayrolls()
            && (int) $payroll->user_id !== (int) Auth::id()
        ) {
            abort(403, 'Forbidden');
        }

        $payroll->load('user', 'period');

        $baseItems = $payroll->items()->where('type', 'base')->orderBy('id')->get();
        $deductionItems = $payroll->items()->where('type', 'deduction')->orderBy('id')->get();

        $baseTotal = (int) $baseItems->sum('amount');
        $dedTotal = (int) $deductionItems->sum('amount');
        $net = $baseTotal - $dedTotal;

        $pdf = Pdf::loadView('payroll.slip', compact(
            'payroll',
            'baseItems',
            'deductionItems',
            'baseTotal',
            'dedTotal',
            'net'
        ))->setPaper('a4', 'portrait');

        $filename = sprintf(
            'Slip-%s-%02d-%d.pdf',
            str_replace(' ', '_', $payroll->user->name),
            $payroll->period->month ?? $payroll->month,
            $payroll->period->year ?? $payroll->year
        );

        return $pdf->stream($filename);
    }

    /* =========================================================
     | CREATE / STORE  (DIUBAH: Base multi-row)
     * ========================================================*/

    public function create(Request $request)
    {
        $this->authorize('addpayroll');
        $month = (int) ($request->input('month') ?: now()->month);
        $year = (int) ($request->input('year') ?: now()->year);

        $period = PayrollPeriod::forMonthYear($month, $year)->first();
        if (! $period || ! in_array($period->status, [PayrollPeriod::STATUS_OPEN, PayrollPeriod::STATUS_REOPEN], true)) {
            return redirect()->route('payroll.index', ['month' => $month, 'year' => $year])
                ->with('error', 'Period belum aktif (Open/Reopen dulu).');
        }

        $usedUserIds = Payroll::where('payroll_period_id', $period->id)->pluck('user_id')->all();
        $users = User::query()
            ->when($usedUserIds, fn ($q) => $q->whereNotIn('id', $usedUserIds))
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('payroll.create', compact('period', 'month', 'year', 'users'));
    }

    public function store(Request $request)
    {
        $this->authorize('addpayroll');
        $data = $request->validate([
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'user_id' => ['required', Rule::exists('users', 'id')],

            // Base -> array
            'bases' => ['required', 'array'],
            'bases.name.*' => ['nullable', 'string', 'max:100'],
            'bases.amount.*' => ['nullable', 'regex:/^\s*[\d.,\sRpRP]+\s*$/'],

            // Deductions -> tetap
            'deductions' => ['sometimes', 'array'],
            'deductions.name.*' => ['nullable', 'string', 'max:100'],
            'deductions.amount.*' => ['nullable', 'regex:/^\s*[\d.,\sRpRP]+\s*$/'],

            'notes' => ['nullable', 'string'],
        ]);

        $period = PayrollPeriod::forMonthYear((int) $data['month'], (int) $data['year'])->first();
        if (! $period || ! in_array($period->status, [PayrollPeriod::STATUS_OPEN, PayrollPeriod::STATUS_REOPEN], true)) {
            return redirect()->route('payroll.index', ['month' => $data['month'], 'year' => $data['year']])
                ->with('error', 'Period belum aktif (Open/Reopen dulu).');
        }

        // Cegah duplikasi slip user di periode yang sama
        $exists = Payroll::where('payroll_period_id', $period->id)
            ->where('user_id', $data['user_id'])
            ->exists();
        if ($exists) {
            return back()->withInput()->withErrors(['user_id' => 'User sudah memiliki payroll pada periode ini.']);
        }

        // Wajib minimal satu base > 0
        $baseNames = $data['bases']['name'] ?? [];
        $baseAmts = $data['bases']['amount'] ?? [];
        $hasBase = false;
        foreach ($baseAmts as $i => $amt) {
            if ($this->cleanCurrency($amt) > 0) {
                $hasBase = true;
                break;
            }
        }
        if (! $hasBase) {
            return back()->withInput()->withErrors(['bases' => 'Minimal satu komponen Base dengan nominal > 0.']);
        }

        DB::transaction(function () use ($data, $period, $baseNames, $baseAmts) {
            $payroll = Payroll::create([
                'payroll_period_id' => $period->id,
                'user_id' => (int) $data['user_id'],
                'status' => Payroll::STATUS_DRAFT ?? 'draft',
                'notes' => $data['notes'] ?? null,
            ]);

            // BASES (multi)
            foreach ($baseNames as $i => $nm) {
                $nm = trim((string) ($nm ?? ''));
                $val = max(0, $this->cleanCurrency($baseAmts[$i] ?? null));
                if ($val > 0) {
                    $payroll->items()->create([
                        'type' => PayrollItem::TYPE_BASE ?? 'base',
                        'name' => ($nm !== '' ? $nm : 'Gaji Pokok'),
                        'amount' => $val,
                    ]);
                }
            }

            // DEDUCTIONS (tetap)
            $names = $data['deductions']['name'] ?? [];
            $amts = $data['deductions']['amount'] ?? [];
            foreach ($names as $i => $nm) {
                $nm = trim((string) ($nm ?? ''));
                $val = max(0, $this->cleanCurrency($amts[$i] ?? null));
                if ($nm !== '' && $val > 0) {
                    $payroll->items()->create([
                        'type' => PayrollItem::TYPE_DEDUCTION ?? 'deduction',
                        'name' => $nm,
                        'amount' => $val,
                    ]);
                }
            }

            $payroll->recalcTotals();
        });

        return redirect()->route('payroll.index', ['month' => $period->month, 'year' => $period->year])
            ->with('success', 'Payroll berhasil dibuat.');
    }

    /* =========================================================
     | EDIT / UPDATE (DIUBAH: Base multi-row)
     * ========================================================*/

    public function edit(Request $r, Payroll $payroll)
    {
        $this->authorize('editpayroll');
        if ($payroll->status !== Payroll::STATUS_DRAFT) {
            return redirect()->route('payroll.show', $payroll)->with('error', 'Slip yang sudah dibayar tidak dapat diedit.');
        }
        $users = User::select('id', 'name')->orderBy('name')->get();

        // base jadi list
        $baseItems = $payroll->items()->where('type', 'base')->orderBy('id')->get();
        $deductionItems = $payroll->items()->where('type', 'deduction')->orderBy('id')->get();

        return view('payroll.edit', [
            'payroll' => $payroll,
            'users' => $users,
            'baseItems' => $baseItems,      // ganti dari baseItem tunggal
            'deductionItems' => $deductionItems,
            'month' => $r->integer('month') ?: $payroll->month,
            'year' => $r->integer('year') ?: $payroll->year,
        ]);
    }

    public function update(Request $r, Payroll $payroll)
    {
        $this->authorize('editpayroll');
        if ($payroll->status !== Payroll::STATUS_DRAFT) {
            return redirect()->route('payroll.show', $payroll)->with('error', 'Slip yang sudah dibayar tidak dapat diedit.');
        }
        $r->validate([
            'user_id' => ['required', 'exists:users,id'],

            // base arrays
            'bases.id.*' => ['nullable', 'integer'],
            'bases.name.*' => ['nullable', 'string', 'max:100'],
            'bases.amount.*' => ['nullable', 'regex:/^\s*[\d.,\sRpRP]+\s*$/'],
            'bases_delete.*' => ['nullable', 'integer'],

            // deductions (tetap)
            'deductions.name.*' => ['nullable', 'string', 'max:100'],
            'deductions.amount.*' => ['nullable', 'regex:/^\s*[\d.,\sRpRP]+\s*$/'],
            'deductions.id.*' => ['nullable', 'integer'],
            'deductions_delete.*' => ['nullable', 'integer'],

            'notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($r, $payroll) {
            // header
            $payroll->update(['notes' => $r->input('notes')]);

            // === BASE: delete
            $delBaseIds = (array) $r->input('bases_delete', []);
            if (! empty($delBaseIds)) {
                $payroll->items()->whereIn('id', $delBaseIds)->where('type', 'base')->delete();
            }

            // === BASE: upsert
            $bIds = (array) $r->input('bases.id', []);
            $bNames = (array) $r->input('bases.name', []);
            $bAmts = (array) $r->input('bases.amount', []);
            $hasBase = false;

            foreach ($bNames as $i => $nm) {
                $nm = trim($nm ?? '');
                $amt = max(0, $this->cleanCurrency($bAmts[$i] ?? 0));
                $id = $bIds[$i] ?? null;

                if ($amt > 0) {
                    $hasBase = true;
                }
                if ($nm === '' && $amt === 0) {
                    continue;
                }

                $payload = [
                    'type' => PayrollItem::TYPE_BASE ?? 'base',
                    'name' => ($nm !== '' ? $nm : 'Gaji Pokok'),
                    'amount' => $amt,
                ];

                if ($id) {
                    $payroll->items()->whereKey($id)->update($payload);
                } else {
                    $payroll->items()->create($payload);
                }
            }

            // Pastikan minimal ada satu base > 0
            if (! $hasBase) {
                abort(422, 'Minimal satu komponen Base dengan nominal > 0.');
            }

            // === DEDUCTIONS: delete
            $delIds = (array) $r->input('deductions_delete', []);
            if (! empty($delIds)) {
                $payroll->items()->whereIn('id', $delIds)->where('type', 'deduction')->delete();
            }

            // === DEDUCTIONS: upsert
            $ids = (array) $r->input('deductions.id', []);
            $names = (array) $r->input('deductions.name', []);
            $amts = (array) $r->input('deductions.amount', []);

            foreach ($names as $i => $nm) {
                $nm = trim($nm ?? '');
                $amt = $this->cleanCurrency($amts[$i] ?? 0);
                $id = $ids[$i] ?? null;

                if ($nm === '' && $amt === 0) {
                    continue;
                }

                $payload = [
                    'type' => PayrollItem::TYPE_DEDUCTION ?? 'deduction',
                    'name' => $nm,
                    'amount' => $amt,
                ];

                if ($id) {
                    $payroll->items()->whereKey($id)->update($payload);
                } else {
                    $payroll->items()->create($payload);
                }
            }

            $payroll->recalcTotals();
        });

        return redirect()->route('payroll.index', [
            'month' => $payroll->period?->month,
            'year' => $payroll->period?->year,
        ])->with('success', 'Payroll updated.');
    }

    /* =========================================================
     | POLLING (delta) – TETAP
     * ========================================================*/

    protected function buildBaseQuery(Request $request)
    {
        $user = $request->user();
        $isCrew = ! $user->canViewAllPayrolls();

        $month = (int) ($request->input('month') ?: now()->month);
        $year = (int) ($request->input('year') ?: now()->year);
        $search = trim((string) $request->input('search', ''));

        $period = PayrollPeriod::forMonthYear($month, $year)->first();

        $q = Payroll::query()
            ->with('user')
            ->where('payroll_period_id', $period?->id ?? 0)
            ->leftJoin('users', 'users.id', '=', 'payrolls.user_id')
            ->select('payrolls.*');

        if ($isCrew) {
            $q->where('payrolls.user_id', $user->id);
        }

        if ($search !== '') {
            $q->where(function ($qq) use ($search) {
                $qq->where('users.name', 'like', "%{$search}%");
            });
        }

        return [$q, $period, $isCrew, $month, $year];
    }

    public function changes(Request $request)
    {
        $this->authorize('payrollmenu');

        [$base, $period] = $this->buildBaseQuery($request);
        if (! $period) {
            return response()->json([
                'latest_ts' => now()->toIso8601String(),
                'created' => [],
                'updated' => [],
                'deleted' => [],
            ]);
        }

        $sinceIso = $request->query('since');
        $since = $sinceIso ? Carbon::parse($sinceIso) : Carbon::now()->subYears(10);
        $since2 = (clone $since)->subSeconds(2);

        $created = (clone $base)
            ->where('payrolls.created_at', '>=', $since2)
            ->pluck('payrolls.id')->all();

        $updated = (clone $base)
            ->where('payrolls.updated_at', '>=', $since2)
            ->where('payrolls.created_at', '<', $since2)
            ->pluck('payrolls.id')->all();

        $visible = array_values(array_filter((array) $request->query('visible'), fn ($v) => is_numeric($v)));
        $deleted = [];
        if (! empty($visible)) {
            $existingVisible = (clone $base)->whereIn('payrolls.id', $visible)->pluck('payrolls.id')->all();
            $deleted = array_values(array_diff($visible, $existingVisible));
        }

        $rawLatest = (clone $base)
            ->select(DB::raw('GREATEST(MAX(payrolls.updated_at), MAX(payrolls.created_at)) as ts'))
            ->value('ts');

        $latest = $rawLatest
            ? Carbon::parse($rawLatest)->toIso8601String()
            : Carbon::now()->toIso8601String();

        return response()->json([
            'latest_ts' => $latest,
            'created' => array_values(array_unique($created)),
            'updated' => array_values(array_unique($updated)),
            'deleted' => $deleted,
        ]);
    }

    public function rows(Request $request)
    {
        $this->authorize('payrollmenu');

        [$base, $period, $isCrew, $month, $year] = $this->buildBaseQuery($request);

        $ids = array_filter((array) $request->query('ids'), fn ($v) => is_numeric($v));
        if (! $ids) {
            return response('');
        }

        $rows = (clone $base)->whereIn('payrolls.id', $ids)->get();
        $rows = $rows->sortBy(fn ($r) => strtolower($r->user->name ?? ''))->values();

        return view('payroll._rows', compact('rows', 'period', 'month', 'year'));
    }
}
