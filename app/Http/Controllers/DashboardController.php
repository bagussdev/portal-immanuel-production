<?php

namespace App\Http\Controllers;

use App\Models\Armada;
use App\Models\FieldJob;
use App\Models\FieldJobStage;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Payroll;
use App\Models\PayrollPeriod;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $canFinance = $user->isMaster() || $user->isAdmin();
        $selectedMonth = $this->selectedMonth($request);
        $periodStart = $selectedMonth->copy()->startOfMonth();
        $periodEnd = $selectedMonth->copy()->endOfMonth();
        $currentPeriod = PayrollPeriod::forMonthYear((int) $selectedMonth->month, (int) $selectedMonth->year)->first();

        $data = [
            'canFinance' => $canFinance,
            'currentPeriod' => $currentPeriod,
            'selectedMonth' => $selectedMonth,
            'ownPayrolls' => Payroll::with('period')->where('user_id', $user->id)->latest()->limit(6)->get(),
            'payrollStats' => $currentPeriod ? [
                'total' => $currentPeriod->payrolls()->count(),
                'paid' => $currentPeriod->payrolls()->where('status', Payroll::STATUS_PAID)->count(),
                'net' => (int) $currentPeriod->payrolls()->sum('net_pay'),
            ] : ['total' => 0, 'paid' => 0, 'net' => 0],
            'upcomingStages' => $user->can('fieldjobsmenu')
                ? FieldJobStage::query()
                    ->where('is_active', true)
                    ->where('status', '!=', FieldJobStage::STATUS_COMPLETED)
                    ->whereNotNull('scheduled_at')
                    ->whereBetween('scheduled_at', [$periodStart, $periodEnd])
                    ->whereHas('fieldJob', fn ($jobs) => $jobs
                        ->visibleTo($user)
                        ->where('status', '!=', FieldJob::STATUS_CANCELLED))
                    ->with('fieldJob')
                    ->orderBy('scheduled_at')
                    ->limit(6)
                    ->get()
                : collect(),
        ];

        if ($canFinance) {
            $monthlyInvoices = Invoice::query()->whereNotIn('status', [Invoice::STATUS_DRAFT, Invoice::STATUS_VOID])
                ->whereBetween('issue_date', [$periodStart->toDateString(), $periodEnd->toDateString()]);
            $data += [
                'invoiceStats' => [
                    'open' => (clone $monthlyInvoices)->where(fn ($query) => $query->whereIn('status', [Invoice::STATUS_UNPAID, Invoice::STATUS_PARTIAL, Invoice::STATUS_OVERDUE])->orWhere(fn ($overpaid) => $overpaid->where('status', Invoice::STATUS_OVERPAID)->whereNull('resolved_at')))->count(),
                    'totalInvoice' => (int) (clone $monthlyInvoices)->sum('grand_total'),
                    'receivable' => (int) (clone $monthlyInvoices)->sum('balance_due'),
                    'paidThisMonth' => (int) InvoicePayment::whereNull('voided_at')->whereBetween('paid_at', [$periodStart, $periodEnd])->sum('amount'),
                    'draftQuotations' => Quotation::where('status', Quotation::STATUS_DRAFT)->whereBetween('quotation_date', [$periodStart->toDateString(), $periodEnd->toDateString()])->count(),
                ],
                'documentStats' => [
                    'overdue' => Armada::whereDate('stnk_expired', '<', today())->count(),
                    'dueSoon' => Armada::whereBetween('stnk_expired', [today(), today()->addDays(30)])->count(),
                ],
            ];
        }

        return view('dashboard.overview', $data);
    }

    public function stats(Request $request)
    {
        abort_unless($request->user()->isMaster() || $request->user()->isAdmin(), 403);

        $month = $this->selectedMonth($request);
        $invoices = Invoice::query()->whereNotIn('status', [Invoice::STATUS_DRAFT, Invoice::STATUS_VOID])->whereBetween('issue_date', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()]);
        return response()->json([
            'open_invoices' => (clone $invoices)->where('balance_due', '>', 0)->count(),
            'receivable' => (int) (clone $invoices)->sum('balance_due'),
            'stnk_due' => Armada::whereDate('stnk_expired', '<=', today()->addDays(30))->count(),
        ]);
    }

    private function selectedMonth(Request $request): Carbon
    {
        $value = (string) $request->input('month', now()->format('Y-m'));
        if (! preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $value)) $value = now()->format('Y-m');

        return Carbon::createFromFormat('Y-m', $value)->startOfMonth();
    }
}
