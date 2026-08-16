<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class InvoicesMarkOverdue extends Command
{
    protected $signature = 'invoices:mark-overdue {--dry : Hanya hitung kandidat tanpa mengubah data}';

    protected $description = 'Set status invoice menjadi overdue bila melewati due_date dan masih ada saldo.';

    public function handle(): int
    {
        $tz = config('app.timezone', 'Asia/Jakarta');
        $today = Carbon::now($tz)->toDateString();

        $base = Invoice::query()
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', $today)
            ->where('balance_due', '>', 0)          // masih ada sisa pembayaran
            ->whereIn('status', ['unpaid', 'partial']); // bukan paid/void/overdue

        $candidates = (clone $base)->count();
        $this->line("Candidates: {$candidates} (date < {$today})");

        if ($this->option('dry') || $candidates === 0) {
            $this->info($candidates === 0 ? 'No invoice to update.' : 'Dry-run: no changes made.');

            return self::SUCCESS;
        }

        // Update mass (tidak memicu events; aman karena tidak ubah totals)
        $updated = $base->update([
            'status' => 'overdue',
            'updated_at' => Carbon::now($tz),
        ]);

        $this->info("Marked as overdue: {$updated}");

        return self::SUCCESS;
    }
}
