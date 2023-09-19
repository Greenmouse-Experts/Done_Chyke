<?php

namespace App\Console\Commands;

use App\Models\Balance;
use App\Models\Expenses;
use App\Models\Payment;
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
        $yesterday = Carbon::yesterday()->format('Y-m-d');

        $balance = Balance::whereDate('date', $today)->first();

        $expenses = Expenses::whereDate('date', $today)->get()->sum('amount');

        $paymentsDateCash = Payment::where('payment_type', 'Cash')->whereDate('date_paid', $today)->get();
        $paymentsFinalCash = Payment::where('payment_type', 'Cash')->whereDate('final_date_paid', $today)->get();
        $cash = $paymentsDateCash->sum('payment_amount') + $paymentsFinalCash->sum('final_payment_amount');

        $paymentsDateTransfer = Payment::where('payment_type', 'Direct Transfer')->whereDate('date_paid', $today)->get();
        $paymentsFinalTransfer = Payment::where('payment_type', 'Direct Transfer')->whereDate('final_date_paid', $today)->get();
        $transfer =  $paymentsDateTransfer->sum('payment_amount') + $paymentsFinalTransfer->sum('final_payment_amount');

        $paymentsDateCheque = Payment::where('payment_type', 'Transfer by Cheques')->whereDate('date_paid', $today)->get();
        $paymentsFinalCheque = Payment::where('payment_type', 'Transfer by Cheques')->whereDate('final_date_paid', $today)->get();
        $cheques = $paymentsDateCheque->sum('payment_amount') + $paymentsFinalCheque->sum('final_payment_amount');

        $expensesCash = Expenses::where('payment_source', 'Cash')->whereDate('date', $today)->get()->sum('amount');
        $expensesCheque = Expenses::where('payment_source', 'Transfer by Cheques')->whereDate('date', $today)->get()->sum('amount');
        $expensesTransfer = Expenses::where('payment_source', 'Direct Transfer')->whereDate('date', $today)->get()->sum('amount');

        $yesterdayBalance = Balance::whereDate('date', $yesterday)->sum('starting_balance') ?? 0 ;

        $yesterdaypaymentsDateCash = Payment::where('payment_type', 'Cash')->whereDate('date_paid', $yesterday)->get();
        $yesterdaypaymentsFinalCash = Payment::where('payment_type', 'Cash')->whereDate('final_date_paid', $yesterday)->get();
        $yesterdayExpensesCash = Expenses::where('payment_source', 'Cash')->whereDate('date', $yesterday)->get()->sum('amount');
        $yesterdaycash = $yesterdaypaymentsDateCash->sum('payment_amount') + $yesterdaypaymentsFinalCash->sum('final_payment_amount') + $yesterdayExpensesCash;
        $yesterdayCashPayment = $yesterdaycash ?? 0;

        $remainingBalance = $yesterdayBalance - $yesterdayCashPayment;
        $closing_balance = $expenses + $cash + $transfer + $cheques;

        if($balance)
        {
            $balance->update([
                'closing_balance' => $closing_balance,
                'expense' => $expenses,
                'cash' => $cash,
                'transfer' => $transfer,
                'transfer_by_cheques' => $cheques
            ]);
        } else {
            Balance::create([
                'starting_balance' => $remainingBalance,
                'closing_balance' => $closing_balance,
                'date' => $today,
                'expense' => $expenses,
                'cash' => $cash,
                'transfer' => $transfer,
                'transfer_by_cheques' => $cheques
            ]);
        }
    }
}
