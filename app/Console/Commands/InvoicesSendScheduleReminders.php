<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class InvoicesSendScheduleReminders extends Command
{
    protected $signature = 'invoices:send-schedule-reminders';

    protected $description = 'Kirim pengingat H-7 untuk jadwal dari invoice';

    public function handle(): int
    {
        $target = today()->addDays(7);
        $types = ['loading_date' => 'Loading', 'event_date' => 'Acara', 'bongkaran_date' => 'Bongkar'];
        $sent = 0;

        foreach ($types as $column => $label) {
            Invoice::with('client')->whereNotIn('status', [Invoice::STATUS_DRAFT, Invoice::STATUS_VOID])
                ->whereDate($column, $target)->chunkById(100, function ($invoices) use ($column, $label, $target, &$sent) {
                    foreach ($invoices as $invoice) {
                        $inserted = DB::table('invoice_schedule_reminders')->insertOrIgnore([
                            'invoice_id' => $invoice->id, 'type' => $column,
                            'scheduled_for' => $target->toDateString(), 'sent_at' => now(),
                            'created_at' => now(), 'updated_at' => now(),
                        ]);
                        if (! $inserted) {
                            continue;
                        }
                        NotificationService::pushToAllowedRoles('invoice_schedule_h7', [
                            'title' => "Pengingat H-7 {$label}",
                            'message' => ($invoice->event_name ?: $invoice->client?->name ?: 'Agenda').
                                ' dijadwalkan pada '.$target->translatedFormat('d F Y').'.',
                            'link' => route('field-jobs.index'),
                            'icon' => 'calendar',
                        ]);
                        $sent++;
                    }
                });
        }
        $this->info("{$sent} pengingat dikirim.");

        return self::SUCCESS;
    }
}
