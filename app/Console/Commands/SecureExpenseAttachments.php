<?php

namespace App\Console\Commands;

use App\Models\Expense;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SecureExpenseAttachments extends Command
{
    protected $signature = 'expenses:secure-attachments {--dry-run : Hanya tampilkan file yang akan dipindahkan}';

    protected $description = 'Pindahkan lampiran pengeluaran lama dari storage public ke storage private';

    public function handle(): int
    {
        $moved = 0;
        $alreadyPrivate = 0;
        $missing = 0;
        $skipped = 0;
        $dryRun = (bool) $this->option('dry-run');

        Expense::query()->whereNotNull('attachment')->select(['id', 'attachment'])
            ->chunkById(100, function ($expenses) use (&$moved, &$alreadyPrivate, &$missing, &$skipped, $dryRun): void {
                foreach ($expenses as $expense) {
                    $path = ltrim((string) $expense->attachment, '/');

                    if (! str_starts_with($path, 'expenses/')) {
                        $skipped++;
                        $this->warn("Lewati path tidak dikenal untuk expense #{$expense->id}.");

                        continue;
                    }

                    if (Storage::disk('local')->exists($path)) {
                        if (! $dryRun) {
                            Storage::disk('public')->delete($path);
                        }
                        $alreadyPrivate++;

                        continue;
                    }

                    if (! Storage::disk('public')->exists($path)) {
                        $missing++;

                        continue;
                    }

                    if ($dryRun) {
                        $this->line("Akan dipindahkan: {$path}");
                        $moved++;

                        continue;
                    }

                    $stream = Storage::disk('public')->readStream($path);
                    if ($stream === false) {
                        $missing++;

                        continue;
                    }

                    try {
                        Storage::disk('local')->writeStream($path, $stream);
                    } finally {
                        if (is_resource($stream)) {
                            fclose($stream);
                        }
                    }

                    if (Storage::disk('local')->exists($path)) {
                        Storage::disk('public')->delete($path);
                        $moved++;
                    } else {
                        $missing++;
                    }
                }
            });

        $this->info("Lampiran private: {$alreadyPrivate}; dipindahkan: {$moved}; hilang: {$missing}; dilewati: {$skipped}.");

        return self::SUCCESS;
    }
}
