<?php

namespace App\Console\Commands;

use App\Models\ExpensePeriod;
use Illuminate\Console\Command;

class ExpensesBumpPeriod extends Command
{
    // Artisan name → seragam dgn task lain
    protected $signature = 'expenses:bump-period {--include-reopen : Also close REOPEN periods in past months}';

    protected $description = 'Close past periods and ensure current month expense period is OPEN (idempotent).';

    public function handle(): int
    {
        $includeReopen = (bool) $this->option('include-reopen');

        $res = ExpensePeriod::bump($includeReopen);

        $this->info(sprintf(
            'BUMP: closed=%d | opened_current=%s | current=%02d/%d',
            $res['closed'],
            $res['opened'] ? 'yes' : 'no',
            $res['month'],
            $res['year']
        ));

        return self::SUCCESS;
    }
}
