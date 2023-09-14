<?php

namespace App\Console\Commands;

use App\Models\Balance;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Console\Command;

class StartingCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'starting:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Starting Balance for today';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = Carbon::now()->format('Y-m-d');
        $yesterday = Carbon::yesterday()->format('Y-m-d');
        
        $yesterdayBalance = Balance::whereDate('date', $yesterday)->sum('starting_balance') ?? 0 ;

        $yesterdaypaymentsDateCash = Payment::where('payment_type', 'Cash')->whereDate('date_paid', $yesterday)->get();
        $yesterdaypaymentsFinalCash = Payment::where('payment_type', 'Cash')->whereDate('final_date_paid', $yesterday)->get();
        $yesterdaycash = $yesterdaypaymentsDateCash->sum('payment_amount') + $yesterdaypaymentsFinalCash->sum('final_payment_amount');
        $yesterdayCashPayment = $yesterdaycash ?? 0;

        $remainingBalance = $yesterdayBalance - $yesterdayCashPayment;

        Balance::create([
            'starting_balance' => $remainingBalance,
            'date' => $today,
        ]);
    }
}
