<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanupUnpaidOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:clear-unpaid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Xóa đơn hàng chưa thanh toán sau 6 tiếng';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
    }
}
