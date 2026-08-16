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

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $canFinance = $user->isMaster() || $user->isAdmin();
        $currentPeriod = PayrollPeriod::forMonthYear((int) now()->month, (int) now()->year)->first();

        $data = [
            'canFinance' => $canFinance,
            'currentPeriod' => $currentPeriod,
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
                    ->where('scheduled_at', '>=', today()->startOfDay())
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
            $data += [
                'invoiceStats' => [
                    'open' => Invoice::open()->count(),
                    'receivable' => (int) Invoice::open()->sum('balance_due'),
                    'paidThisMonth' => (int) InvoicePayment::whereNull('voided_at')->whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('amount'),
                    'draftQuotations' => Quotation::where('status', Quotation::STATUS_DRAFT)->count(),
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

        return response()->json([
            'open_invoices' => Invoice::open()->count(),
            'receivable' => (int) Invoice::open()->sum('balance_due'),
            'stnk_due' => Armada::whereDate('stnk_expired', '<=', today()->addDays(30))->count(),
        ]);
    }
}
