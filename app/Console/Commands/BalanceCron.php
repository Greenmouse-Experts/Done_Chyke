<?php

namespace App\Console\Commands;

use App\Models\Balance;
use App\Models\Expenses;
use Carbon\Carbon;
use Illuminate\Console\Command;

class BalanceCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'balance:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = Carbon::now()->format('Y-m-d');

        $balance = Balance::whereDate('date', Carbon::now()->format('Y-m-d'))->first();
        $expensesCash = Expenses::where('payment_source', 'Cash')->whereDate('date', Carbon::now()->format('Y-m-d'))->get()->sum('amount');
        $expensesCheque = Expenses::where('payment_source', 'Cheque')->whereDate('date', Carbon::now()->format('Y-m-d'))->get()->sum('amount');
        $expensesTransfer = Expenses::where('payment_source', 'Transfer')->whereDate('date', Carbon::now()->format('Y-m-d'))->get()->sum('amount');

        $starting_balance = ($balance->starting_balance ?? 0) + $expensesCheque + $expensesTransfer;
        $expenses = $expensesCash + $expensesCheque + $expensesTransfer;
        $closing_balance = $starting_balance - $expenses;

        if($balance)
        {
            $balance->update([
                'closing_balance' => $closing_balance
            ]);
        } else {
            Balance::create([
                'starting_balance' => 0,
                'closing_balance' => $closing_balance,
                'date' => $today
            ]);
        }
        
        \Log::info($closing_balance);
    }
}
