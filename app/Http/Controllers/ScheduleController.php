<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;

class ScheduleController extends Controller
{
    use AuthorizesRequests;

    public function index(): RedirectResponse
    {
        $this->authorize('fieldjobsmenu');

        return redirect()->route('field-jobs.index');
    }

    public function show(Invoice $invoice): RedirectResponse
    {
        $this->authorize('fieldjobsmenu');
        $fieldJobs = $invoice->fieldJobs()->orderBy('id')->get();

        if ($fieldJobs->isEmpty()) {
            return redirect()->route('field-jobs.index')
                ->with('warning', 'Jadwal operasional untuk invoice ini belum tersedia.');
        }

        if ($fieldJobs->count() === 1) {
            return redirect()->route('field-jobs.show', $fieldJobs->first());
        }

        return redirect()->route('field-jobs.index', ['invoice_id' => $invoice->id]);
    }
}
