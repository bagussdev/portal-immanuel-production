<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpensePeriod;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    use AuthorizesRequests;

    /**
     * Hitung objek period + locks untuk bulan/tahun tertentu.
     * Mengandalkan timezone app (now()).
     *
     * @return array{0:ExpensePeriod|null,1:array}
     */
    private function resolvePeriodAndLocks(int $month, int $year): array
    {
        $now = now(); // pakai app.timezone
        $curYear = (int) $now->year;
        $curMonth = (int) $now->month;

        $isCurrent = ($year === $curYear && $month === $curMonth);
        $isPast = ($year < $curYear) || ($year === $curYear && $month < $curMonth);

        $period = ExpensePeriod::ofYm($year, $month)->first();
        $exists = (bool) $period;
        $status = $exists ? strtolower((string) $period->status) : null;

        // PENTING: can_add disamakan dengan logika index (OPEN/REOPEN; lampau wajib REOPEN)
        $canAdd = $exists && $status !== 'closed' && (! $isPast || $status === 'reopen');

        $locks = [
            'is_current' => $isCurrent,
            'is_past' => $isPast,
            'period_exists' => $exists,
            'can_add' => $canAdd,
            'can_close' => $exists && in_array($status, ['open', 'reopen'], true),
            'can_reopen' => $exists && $status === 'closed',
        ];

        return [$period, $locks];
    }

    /* ============== Helpers ============== */
    private function cleanCurrency($value): int
    {
        if ($value === null || $value === '') {
            return 0;
        }
        $s = preg_replace('/[^0-9,]/', '', (string) $value);
        if (strpos($s, ',') !== false) {
            $s = explode(',', $s)[0];
        }
        $s = preg_replace('/\D+/', '', $s);

        return (int) ($s ?: 0);
    }

    /** Builder untuk index & polling (month/year + search) */
    protected function buildIndexQuery(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $month = (int) ($request->input('month') ?: now()->month);
        $year = (int) ($request->input('year') ?: now()->year);

        return Expense::with('creator')
            ->ofMonthYear($month, $year)
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($w) use ($search) {
                    $w->where('expense_number', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('id');
    }

    /* ============== Index (seed polling) ============== */
    public function index(Request $request)
    {
        $this->authorize('expensesmenu');

        $search = trim((string) $request->input('search', ''));
        $perPage = $request->input('per_page', 10);
        $month = (int) ($request->input('month') ?: now()->month);
        $year = (int) ($request->input('year') ?: now()->year);

        $query = $this->buildIndexQuery($request);

        $rows = $perPage === 'all'
            ? $query->get()
            : $query->paginate((int) $perPage)->appends($request->query());

        $period = ExpensePeriod::ofYm($year, $month)->first();

        $now = now();
        $isCurrent = ($year === (int) $now->year && $month === (int) $now->month);
        $isPast = ($year < (int) $now->year) || ($year === (int) $now->year && $month < (int) $now->month);

        if (! $period && $isCurrent) {
            $period = ExpensePeriod::ensureOpen($year, $month);
        }

        $latestTs = '';
        if ($period) {
            $rawLatest = (clone $query)
                ->select(DB::raw('GREATEST(MAX(updated_at), MAX(created_at)) as ts'))
                ->value('ts');
            $latestTs = $rawLatest ? Carbon::parse($rawLatest)->toIso8601String() : now()->toIso8601String();
        }

        $baseOffset = ($rows instanceof LengthAwarePaginator)
            ? (($rows->currentPage() - 1) * $rows->perPage())
            : 0;

        $stat = $period ? strtolower($period->status) : 'not_opened';
        $canAdd = $period && $stat !== 'closed' && (! $isPast || $stat === 'reopen');

        $locks = [
            'period_exists' => (bool) $period,
            'status' => $stat,
            'can_add' => $canAdd,
            'is_current' => $isCurrent,
            'is_past' => $isPast,
            'can_reopen' => $period && $stat === 'closed',
            'can_close' => $period && $stat !== 'closed',
        ];

        $statsQ = Expense::ofMonthYear($month, $year);
        $stats = [
            'count' => (clone $statsQ)->count(),
            'total' => (int) (clone $statsQ)->sum('total'),
            'avg' => (int) (((clone $statsQ)->avg('total')) ?: 0),
        ];

        return view('expenses.index', [
            'rows' => $rows,
            'expenses' => $rows,
            'perPage' => $perPage,
            'search' => $search,
            'latestTs' => $latestTs,
            'baseOffset' => $baseOffset,
            'month' => $month,
            'year' => $year,
            'period' => $period,
            'locks' => $locks,
            'stats' => $stats,
            // >>> tambahkan ini supaya render awal juga konsisten dengan polling
            'allowActions' => $canAdd,
        ]);
    }

    /* ============== POLLING: heartbeat changes (JSON) ============== */
    public function changes(Request $request)
    {
        $this->authorize('expensesmenu');

        $month = (int) ($request->input('month') ?: now()->month);
        $year = (int) ($request->input('year') ?: now()->year);
        if (! ExpensePeriod::ofYm($year, $month)->exists()) {
            return response()->json([
                'latest_ts' => '',
                'created' => [],
                'updated' => [],
                'deleted' => [],
            ]);
        }

        $sinceIso = $request->query('since');
        $since = $sinceIso ? Carbon::parse($sinceIso) : now()->subYears(10);
        $since2 = (clone $since)->subSeconds(2);

        $base = $this->buildIndexQuery($request);

        $created = (clone $base)->where('created_at', '>=', $since2)->pluck('id')->all();
        $updated = (clone $base)->where('updated_at', '>=', $since2)->where('created_at', '<', $since2)->pluck('id')->all();

        $visible = array_values(array_filter((array) $request->query('visible'), fn ($v) => is_numeric($v)));
        $deleted = [];
        if (! empty($visible)) {
            $existingVisible = (clone $base)->whereIn('id', $visible)->pluck('id')->all();
            $deleted = array_values(array_diff($visible, $existingVisible));
        }

        $rawLatest = (clone $base)->select(DB::raw('GREATEST(MAX(updated_at), MAX(created_at)) as ts'))->value('ts');
        $latest = $rawLatest ? Carbon::parse($rawLatest)->toIso8601String() : now()->toIso8601String();

        return response()->json([
            'latest_ts' => $latest,
            'created' => array_values(array_unique($created)),
            'updated' => array_values(array_unique($updated)),
            'deleted' => $deleted,
        ]);
    }

    /* ============== POLLING: render rows (HTML <tr>…) ============== */
    public function rows(Request $request)
    {
        $this->authorize('expensesmenu');

        $ids = array_filter((array) $request->query('ids'), fn ($v) => is_numeric($v));
        if (! $ids) {
            return response('');
        }

        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);

        $expenses = Expense::with('creator')
            ->whereIn('id', $ids)
            ->orderByDesc('id')
            ->get();

        // period + locks untuk bulan yg sedang dilihat
        [$period, $locks] = $this->resolvePeriodAndLocks($month, $year);

        // >>> boolean sederhana untuk kontrol tombol
        $now = now();
        $isPast = ($year < (int) $now->year) || ($year === (int) $now->year && $month < (int) $now->month);
        $stat = $period ? strtolower($period->status) : 'not_opened';
        $allowActions = $period && $stat !== 'closed' && (! $isPast || $stat === 'reopen');

        return view('expenses._rows', [
            'expenses' => $expenses,
            'period' => $period,
            'locks' => $locks,
            'allowActions' => $allowActions, // <— dipakai partial
            'baseOffset' => 0,
        ]);
    }

    /* ============== CREATE ============== */
    public function create(Request $request)
    {
        $this->authorize('createexpenses');

        // periode target dikirim dari index (?month=..&year=..)
        $targetMonth = (int) ($request->query('month') ?: now()->month);
        $targetYear = (int) ($request->query('year') ?: now()->year);

        $first = Carbon::create($targetYear, $targetMonth, 1)->startOfDay();
        $last = (clone $first)->endOfMonth();

        $now = now();
        $isCurrent = ($targetYear === (int) $now->year && $targetMonth === (int) $now->month);
        $isPast = ($targetYear < (int) $now->year) || ($targetYear === (int) $now->year && $targetMonth < (int) $now->month);

        // Cek period
        $period = ExpensePeriod::ofYm($targetYear, $targetMonth)->first();
        if (! $period && $isCurrent) {
            $period = ExpensePeriod::ensureOpen($targetYear, $targetMonth);
        }
        // Tidak boleh create jika period tidak ada (Not Opened) atau CLOSED
        if (! $period || $period->isClosed() || ($isPast && strtolower($period->status) !== 'reopen')) {
            abort(403, 'Periode ini tidak dapat menerima transaksi baru.');
        }

        // default value tanggal: clamp ke range [first, last] dan <= today
        $today = now();
        $def = $today->copy();
        if ($def->lt($first)) {
            $def = $first->copy();
        }
        if ($def->gt($last)) {
            $def = $last->copy();
        }
        if ($def->gt($today)) {
            $def = $today->copy();
        }

        return view('expenses.create', [
            'targetMonth' => $targetMonth,
            'targetYear' => $targetYear,
            'minDate' => $first->toDateString(),
            'maxDate' => $last->toDateString(),
            'defaultDate' => $def->toDateString(),
        ]);
    }

    /* ============== STORE ============== */
    public function store(Request $request)
    {
        $this->authorize('createexpenses');

        $targetMonth = (int) $request->input('target_month'); // hidden dari form
        $targetYear = (int) $request->input('target_year');

        if (! $targetMonth || ! $targetYear) {
            $targetMonth = (int) now()->month;
            $targetYear = (int) now()->year;
        }

        $first = Carbon::create($targetYear, $targetMonth, 1)->startOfDay();
        $last = (clone $first)->endOfMonth();

        $messages = [
            'expense_date.after_or_equal' => 'Tanggal harus dalam periode yang dipilih.',
            'expense_date.before_or_equal' => 'Tanggal harus dalam periode yang dipilih dan tidak melebihi hari ini.',
        ];

        $data = $request->validate([
            'expense_date' => ['required', 'date', 'after_or_equal:'.$first->toDateString(), 'before_or_equal:'.min(now()->toDateString(), $last->toDateString())],
            'name' => ['required', 'string', 'max:255'],
            'qty' => ['required', 'integer', 'min:1'],
            'total' => ['required', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
            'target_month' => ['required', 'integer', 'between:1,12'],
            'target_year' => ['required', 'integer', 'min:2000'],
        ], $messages);

        $dt = Carbon::parse($data['expense_date']);
        if ((int) $dt->month !== $targetMonth || (int) $dt->year !== $targetYear) {
            return back()->withErrors(['expense_date' => 'Tanggal harus di bulan '.$first->format('F Y').'.'])
                ->withInput();
        }

        $period = ExpensePeriod::ofYm($targetYear, $targetMonth)->first();
        $now = now();
        $isPast = ($targetYear < (int) $now->year) || ($targetYear === (int) $now->year && $targetMonth < (int) $now->month);
        if (! $period || $period->isClosed() || ($isPast && strtolower($period->status) !== 'reopen')) {
            return back()->withErrors(['expense_date' => 'Periode ini tidak menerima transaksi baru.'])
                ->withInput();
        }

        DB::transaction(function () use ($request, $data) {
            $path = null;
            if ($request->hasFile('attachment')) {
                $path = $request->file('attachment')->store('expenses', 'local');
            }

            Expense::create([
                'expense_date' => $data['expense_date'],
                'name' => $data['name'],
                'qty' => (int) $data['qty'],
                'total' => $this->cleanCurrency($data['total']),
                'notes' => $data['notes'] ?? null,
                'attachment' => $path,
                'created_by' => $request->user()->id,
            ]);
        });

        return redirect()->route('expenses.index', ['month' => $targetMonth, 'year' => $targetYear])
            ->with('success', 'Expense berhasil ditambahkan.');
    }

    /* ============== EDIT/UPDATE/DESTROY============== */

    public function edit(Request $request, Expense $expense)
    {
        $this->authorize('editexpenses');

        $dt = $expense->expense_date instanceof Carbon
            ? $expense->expense_date->copy()
            : Carbon::parse($expense->expense_date);

        $year = (int) $dt->year;
        $month = (int) $dt->month;

        $first = Carbon::create($year, $month, 1)->startOfDay();
        $last = (clone $first)->endOfMonth();
        $today = now();
        $max = $last->lt($today) ? $last : $today;

        // period harus ada & boleh diedit (OPEN/REOPEN); lampau wajib REOPEN
        $period = ExpensePeriod::ofYm($year, $month)->first();
        $isPast = ($year < (int) $today->year) || ($year === (int) $today->year && $month < (int) $today->month);
        if (! $period || $period->isClosed() || ($isPast && strtolower($period->status) !== 'reopen')) {
            abort(403, 'Periode ini tidak dapat diedit.');
        }

        return view('expenses.edit', [
            'expense' => $expense,
            'targetMonth' => $month,
            'targetYear' => $year,
            'minDate' => $first->toDateString(),
            'maxDate' => $max->toDateString(),
            'defaultDate' => $dt->toDateString(),
        ]);
    }

    public function update(Request $request, Expense $expense)
    {
        $this->authorize('editexpenses');

        $dtOrig = $expense->expense_date instanceof Carbon
            ? $expense->expense_date->copy()
            : Carbon::parse($expense->expense_date);

        $year = (int) $dtOrig->year;
        $month = (int) $dtOrig->month;

        $first = Carbon::create($year, $month, 1)->startOfDay();
        $last = (clone $first)->endOfMonth();
        $today = now();
        $max = $last->lt($today) ? $last : $today;

        // period harus ada & boleh diedit
        $period = ExpensePeriod::ofYm($year, $month)->first();
        $isPast = ($year < (int) $today->year) || ($year === (int) $today->year && $month < (int) $today->month);
        if (! $period || $period->isClosed() || ($isPast && strtolower($period->status) !== 'reopen')) {
            return back()->withErrors(['expense_date' => 'Periode ini terkunci.'])->withInput();
        }

        $messages = [
            'expense_date.after_or_equal' => 'Tanggal harus dalam periode yang dipilih.',
            'expense_date.before_or_equal' => 'Tanggal harus dalam periode yang dipilih dan tidak melebihi hari ini.',
        ];

        $data = $request->validate([
            'expense_date' => ['required', 'date', 'after_or_equal:'.$first->toDateString(), 'before_or_equal:'.$max->toDateString()],
            'name' => ['required', 'string', 'max:255'],
            'qty' => ['required', 'integer', 'min:1'],
            'total' => ['required', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ], $messages);

        $dtNew = Carbon::parse($data['expense_date']);
        if ((int) $dtNew->month !== $month || (int) $dtNew->year !== $year) {
            return back()->withErrors(['expense_date' => 'Tanggal harus tetap di bulan '.$first->translatedFormat('F Y').'.'])->withInput();
        }

        DB::transaction(function () use ($request, $expense, $data) {
            $update = [
                'expense_date' => $data['expense_date'],
                'name' => $data['name'],
                'qty' => (int) $data['qty'],
                'total' => $this->cleanCurrency($data['total']),
                'notes' => $data['notes'] ?? null,
            ];

            if ($request->hasFile('attachment')) {
                if ($expense->attachment) {
                    $this->deleteAttachment($expense->attachment);
                }
                $update['attachment'] = $request->file('attachment')->store('expenses', 'local');
            }

            $expense->update($update);
        });

        return redirect()->route('expenses.index', ['month' => $month, 'year' => $year])
            ->with('success', 'Expense berhasil diperbarui.');
    }

    public function destroy(Request $request, Expense $expense)
    {
        $this->authorize('deleteexpenses');

        $dt = $expense->expense_date instanceof Carbon
            ? $expense->expense_date
            : Carbon::parse($expense->expense_date);

        $period = ExpensePeriod::ofYm((int) $dt->year, (int) $dt->month)->first();
        if (! $period || $period->isClosed()) {
            abort(403, 'Periode tertutup. Tidak bisa menghapus data.');
        }

        if ((int) $expense->created_by !== (int) $request->user()->id) {
            abort(403, 'You are not allowed to delete this expense.');
        }

        DB::transaction(function () use ($expense) {
            if ($expense->attachment) {
                $this->deleteAttachment($expense->attachment);
            }
            $expense->delete();
        });

        return redirect()->route('expenses.index')->with('success', 'Expense berhasil dihapus.');
    }

    public function attachment(Expense $expense)
    {
        $this->authorize('expensesmenu');
        abort_unless($expense->attachment, 404);

        foreach (['local', 'public'] as $disk) {
            if (Storage::disk($disk)->exists($expense->attachment)) {
                return Storage::disk($disk)->download($expense->attachment);
            }
        }

        abort(404);
    }

    /* ============== Period Actions ============== */
    public function periodReopen(ExpensePeriod $period)
    {
        $this->authorize('manageexpenses');
        if ($period->isClosed()) {
            $period->reopen();
        }

        return back()->with('success', 'Periode berhasil di-Reopen.');
    }

    private function deleteAttachment(string $path): void
    {
        Storage::disk('local')->delete($path);
        Storage::disk('public')->delete($path);
    }

    public function periodClose(ExpensePeriod $period)
    {
        $this->authorize('manageexpenses');
        if (! $period->isClosed()) {
            $period->close();
        }

        return back()->with('success', 'Periode berhasil ditutup (CLOSED).');
    }
}
