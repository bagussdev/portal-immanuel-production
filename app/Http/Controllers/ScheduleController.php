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
        $fieldJob = $invoice->fieldJob;

        if (! $fieldJob) {
            return redirect()->route('field-jobs.index')
                ->with('warning', 'Jadwal operasional untuk invoice ini belum tersedia.');
        }

        return redirect()->route('field-jobs.show', $fieldJob);
    }
}
