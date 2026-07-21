<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ActivateMonthlyInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:activate-monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengubah status invoice dari inactive menjadi unpaid pada bulan yang bersangkutan.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        $updatedCount = Invoice::where('status', 'inactive')
            ->where('period_month', $currentMonth)
            ->where('period_year', $currentYear)
            ->update(['status' => 'unpaid']);

        $this->info("Berhasil mengaktifkan {$updatedCount} invoice untuk periode {$currentMonth}/{$currentYear}.");
    }
}
