<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Services\FieldJobSynchronizer;
use Illuminate\Console\Command;

class FieldJobsSync extends Command
{
    protected $signature = 'field-jobs:sync';

    protected $description = 'Membuat atau menyinkronkan pekerjaan lapangan dari invoice yang sudah diterbitkan';

    public function handle(FieldJobSynchronizer $synchronizer): int
    {
        $count = 0;
        Invoice::query()
            ->where('status', '!=', Invoice::STATUS_DRAFT)
            ->orderBy('id')
            ->eachById(function (Invoice $invoice) use ($synchronizer, &$count) {
                $synchronizer->sync($invoice);
                $count++;
            });

        $this->info("{$count} invoice berhasil disinkronkan ke pekerjaan lapangan.");

        return self::SUCCESS;
    }
}
