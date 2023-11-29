<?php

namespace App\Http\Controllers;

use App\Models\Balance;
use App\Models\Expenses;
use App\Models\Payment;
use App\Models\PaymentReceiptColumbite;
use App\Models\PaymentReceiptLowerGradeColumbite;
use App\Models\PaymentReceiptTin;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class AccountantController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }

    public function payments_tin_view($id, Request $request)
    {
        if ($id == 'pound') {
            $active_tab = $id;
            
            $tinPaymentReceiptKg = PaymentReceiptTin::latest()->where('type', 'kg')->get();
            $tinPaymentReceiptPound = PaymentReceiptTin::latest()->where('type', 'pound')->get();
        
            if ($request->start_date_pound != null && $request->end_date_pound != null) {
                $tinPaymentReceiptPound = $tinPaymentReceiptPound->whereBetween('date_of_purchase', [$request->start_date_pound, $request->end_date_pound]);
            }
        
            return view('accountant.payments.tin_view', [
                'tinPaymentReceiptKg' => $tinPaymentReceiptKg,
                'tinPaymentReceiptPound' => $tinPaymentReceiptPound,
                'active_tab' => $active_tab,
                'start_date_pound' => $request->start_date_pound,
                'end_date_pound' => $request->end_date_pound,
                'start_date_kg' => $request->start_date_kg,
                'end_date_kg' => $request->end_date_kg
            ]);
        }

        if ($id == 'kg') {
            $active_tab = $id;
            
            $tinPaymentReceiptKg = PaymentReceiptTin::latest()->where('type', 'kg')->get();
            $tinPaymentReceiptPound = PaymentReceiptTin::latest()->where('type', 'pound')->get();
        
            if ($request->start_date_kg != null && $request->end_date_kg != null) {
                $tinPaymentReceiptKg = $tinPaymentReceiptKg->whereBetween('date_of_purchase', [$request->start_date_kg, $request->end_date_kg]);
            }
        
            return view('accountant.payments.tin_view', [
                'tinPaymentReceiptKg' => $tinPaymentReceiptKg,
                'tinPaymentReceiptPound' => $tinPaymentReceiptPound,
                'active_tab' => $active_tab,
                'start_date_pound' => $request->start_date_pound,
                'end_date_pound' => $request->end_date_pound,
                'start_date_kg' => $request->start_date_kg,
                'end_date_kg' => $request->end_date_kg
            ]);
        }
    }

    public function payments_process_make($receipt_id, $type, $title, Request $request)
    {
        $receiptID = Crypt::decrypt($receipt_id);
        $receiptTYPE = Crypt::decrypt($type);
        $receiptTITLE = Crypt::decrypt($title); 

        if($receiptTITLE == 'Tin')
        {
            $receipt = PaymentReceiptTin::find($receiptID);

        } elseif($receiptTITLE == 'Columbite')
        {
            $receipt = PaymentReceiptColumbite::find($receiptID);

        } else {
            $receipt = PaymentReceiptLowerGradeColumbite::find($receiptID);
        }

        $this->validate($request, [
            'payment_action' => ['nullable', 'string', 'max:255'],
            'payment_type' => ['nullable', 'string', 'max:255'],
            'payment_amount' => ['nullable', 'numeric'],
            'date_paid' => ['nullable', 'date'],
            'final_payment_type' => ['nullable', 'string', 'max:255'],
            'final_payment_amount' => ['nullable', 'numeric'],
            'final_date_paid' => ['nullable', 'date'],
        ]);

        if(Payment::where(['receipt_title' => $receiptTITLE, 'receipt_type' => $receiptTYPE, 'receipt_id' => $receiptID])->exists())
        {
            $payment = Payment::where(['receipt_title' => $receiptTITLE, 'receipt_type' => $receiptTYPE, 'receipt_id' => $receiptID])->get();

            $sumPayment = $payment->sum('payment_amount') + $payment->sum('final_payment_amount');
            $totalPayment = $receipt->price - $sumPayment; 

            if($request->final_payment_amount < ($totalPayment) || $request->final_payment_amount > ($totalPayment))
            {
                return back()->with([
                    'type' => 'danger',
                    'message' => "Final payment can't be greater or less than the remaining balance."
                ]);
            }

            $payment = Payment::create(
                [
                    'user_id' => Auth::user()->id,
                    'receipt_title' => $receiptTITLE,
                    'receipt_type' => $receipt->type,
                    'receipt_id' => $receiptID,
                    'payment_action' => $request->payment_action,
                    'payment_type' => $request->payment_type,
                    'payment_amount' => $request->payment_amount,
                    'date_paid' => $request->date_paid,
                    'final_payment_type' => $request->final_payment_type,
                    'final_payment_amount' => $request->final_payment_amount,
                    'final_date_paid' => $request->final_date_paid,
                ]
            );
    
            return redirect()->route('payments.view.details', Crypt::encrypt($payment->id))->with([
                'type' => 'success',
                'message' => "Payment added successfully."
            ]);

        } else {
            if($request->payment_action == 'Full Payment')
            {
                if($request->payment_amount < ($receipt->price) || $request->payment_amount > ($receipt->price))
                {
                    return back()->with([
                        'type' => 'danger',
                        'message' => "Full payment can't be greater or less than the receipt amount payable."
                    ]);
                }
            } else {

                if($request->payment_amount >= ($receipt->price))
                {
                    return back()->with([
                        'type' => 'danger',
                        'message' => "Part payment can't be greater or equal to the receipt amount payable."
                    ]);
                }
            }
        }

        $payment = Payment::create(
            [
                'user_id' => Auth::user()->id,
                'receipt_title' => $receiptTITLE,
                'receipt_type' => $receipt->type,
                'receipt_id' => $receiptID,
                'payment_action' => $request->payment_action,
                'payment_type' => $request->payment_type,
                'payment_amount' => $request->payment_amount,
                'date_paid' => $request->date_paid,
                'final_payment_type' => $request->final_payment_type,
                'final_payment_amount' => $request->final_payment_amount,
                'final_date_paid' => $request->final_date_paid,
            ]
        );

        return redirect()->route('payments.view.details', Crypt::encrypt($payment->id))->with([
            'type' => 'success',
            'message' => "Payment added successfully."
        ]);
    }

    public function payments_columbite_view($id, Request $request)
    {
        if ($id == 'pound') {
            if ($request->start_date_pound == null && $request->end_date_pound == null) {
                $columbitePaymentReceiptKg = PaymentReceiptColumbite::latest()
                    ->where('type', 'kg')->get();
                $columbitePaymentReceiptPound = PaymentReceiptColumbite::latest()
                    ->where('type', 'pound')->get();
            } else {
                $columbitePaymentReceiptKg = PaymentReceiptColumbite::latest()
                ->where('type', 'kg')->get();
                $columbitePaymentReceiptPound = PaymentReceiptColumbite::latest()
                    ->where('type', 'pound')
                    ->whereBetween('date_of_purchase', [$request->start_date_pound, $request->end_date_pound])->get();
            }
        
            $active_tab = $id;
        
            return view('accountant.payments.columbite_view', [
                'columbitePaymentReceiptKg' => $columbitePaymentReceiptKg,
                'columbitePaymentReceiptPound' => $columbitePaymentReceiptPound,
                'active_tab' => $active_tab,
                'start_date_pound' => $request->start_date_pound,
                'end_date_pound' => $request->end_date_pound,
                'start_date_kg' => $request->start_date_kg,
                'end_date_kg' => $request->end_date_kg
            ]);
        }

        if ($id == 'kg') {
            if ($request->start_date_kg == null && $request->end_date_kg == null) {
                $columbitePaymentReceiptKg = PaymentReceiptColumbite::latest()
                    ->where('type', 'kg')->get();
                $columbitePaymentReceiptPound = PaymentReceiptColumbite::latest()
                    ->where('type', 'pound')->get();
            } else {
                $columbitePaymentReceiptPound = PaymentReceiptColumbite::latest()
                    ->where('type', 'pound')->get();
                $columbitePaymentReceiptKg = PaymentReceiptColumbite::latest()
                    ->where('type', 'kg')
                    ->whereBetween('date_of_purchase', [$request->start_date_kg, $request->end_date_kg])->get();
            }
        
            $active_tab = $id;
        
            return view('accountant.payments.columbite_view', [
                'columbitePaymentReceiptKg' => $columbitePaymentReceiptKg,
                'columbitePaymentReceiptPound' => $columbitePaymentReceiptPound,
                'active_tab' => $active_tab,
                'start_date_pound' => $request->start_date_pound,
                'end_date_pound' => $request->end_date_pound,
                'start_date_kg' => $request->start_date_kg,
                'end_date_kg' => $request->end_date_kg
            ]);
        }
    }

    public function payments_lower_grade_columbite_view($id, Request $request)
    {
        if ($id == 'pound') {
            if ($request->start_date_pound == null && $request->end_date_pound == null) {
                $lowergradecolumbitePaymentReceiptKg = PaymentReceiptLowerGradeColumbite::latest()
                    ->where('type', 'kg')->get();
                $lowergradecolumbitePaymentReceiptPound = PaymentReceiptLowerGradeColumbite::latest()
                    ->where('type', 'pound')->get();
            } else {
                $lowergradecolumbitePaymentReceiptKg = PaymentReceiptLowerGradeColumbite::latest()
                ->where('type', 'kg')->get();
                $lowergradecolumbitePaymentReceiptPound = PaymentReceiptLowerGradeColumbite::latest()
                    ->where('type', 'pound')
                    ->when($id == 'pound', function ($query) use ($request) {
                        return $query->whereBetween('date_of_purchase', [$request->start_date_pound, $request->end_date_pound]);
                    })->get();
            }
        
            $active_tab = $id;
        
            return view('accountant.payments.lower_grade_columbite_view', [
                'lowergradecolumbitePaymentReceiptKg' => $lowergradecolumbitePaymentReceiptKg,
                'lowergradecolumbitePaymentReceiptPound' => $lowergradecolumbitePaymentReceiptPound,
                'active_tab' => $active_tab,
                'start_date_pound' => $request->start_date_pound,
                'end_date_pound' => $request->end_date_pound,
                'start_date_kg' => $request->start_date_kg,
                'end_date_kg' => $request->end_date_kg
            ]);
        }

        if ($id == 'kg') {
            if ($request->start_date_kg == null && $request->end_date_kg == null) {
                $lowergradecolumbitePaymentReceiptKg = PaymentReceiptLowerGradeColumbite::latest()
                    ->where('type', 'kg')->get();
                $lowergradecolumbitePaymentReceiptPound = PaymentReceiptLowerGradeColumbite::latest()
                    ->where('type', 'pound')->get();
            } else {
                $lowergradecolumbitePaymentReceiptPound = PaymentReceiptLowerGradeColumbite::latest()
                    ->where('type', 'pound')->get();
                $lowergradecolumbitePaymentReceiptKg = PaymentReceiptLowerGradeColumbite::latest()
                    ->where('type', 'kg')
                    ->when($id == 'kg', function ($query) use ($request) {
                        return $query->whereBetween('date_of_purchase', [$request->start_date_kg, $request->end_date_kg]);
                    })->get();
            }
        
            $active_tab = $id;
        
            return view('accountant.payments.lower_grade_columbite_view', [
                'lowergradecolumbitePaymentReceiptKg' => $lowergradecolumbitePaymentReceiptKg,
                'lowergradecolumbitePaymentReceiptPound' => $lowergradecolumbitePaymentReceiptPound,
                'active_tab' => $active_tab,
                'start_date_pound' => $request->start_date_pound,
                'end_date_pound' => $request->end_date_pound,
                'start_date_kg' => $request->start_date_kg,
                'end_date_kg' => $request->end_date_kg
            ]);
        }
    }

    public function payments_process($receipt_id, $type, $title)
    {
        $receiptID = Crypt::decrypt($receipt_id);
        $receiptTYPE = Crypt::decrypt($type);
        $receiptTITLE = Crypt::decrypt($title); 

        if($receiptTITLE == 'Tin')
        {
            $receipt = PaymentReceiptTin::find($receiptID);

            if(Payment::where(['receipt_title' => $receiptTITLE, 'receipt_type' => $receiptTYPE, 'receipt_id' => $receiptID])->exists())
            {
                $Receipt = Payment::where(['receipt_title' => $receiptTITLE, 'receipt_type' => $receiptTYPE, 'receipt_id' => $receiptID])->first();
                $paymentAmount = $Receipt->payment_amount + $Receipt->final_payment_amount;
                $action = $Receipt->payment_action;
                $amount = '₦'.number_format($paymentAmount, 2);
                $type = $Receipt->payment_type ?? null;
                $id = $Receipt->id;
            } else {
                $paymentAmount = 0;
                $action = 'No Payment';
                $amount = '₦0';
                $type = null;
                $id = null;
            }

        } elseif($receiptTITLE == 'Columbite')
        {
            $receipt = PaymentReceiptColumbite::find($receiptID);
            if(Payment::where(['receipt_title' => $receiptTITLE, 'receipt_type' => $receiptTYPE, 'receipt_id' => $receiptID])->exists())
            {
                $Receipt = Payment::where(['receipt_title' => $receiptTITLE, 'receipt_type' => $receiptTYPE, 'receipt_id' => $receiptID])->first();
                $paymentAmount = $Receipt->payment_amount + $Receipt->final_payment_amount;
                $action = $Receipt->payment_action;
                $amount = '₦'.number_format($paymentAmount, 2);
                $type = $Receipt->payment_type ?? null;
                $id = $Receipt->id;
            } else {
                $paymentAmount = 0;
                $action = 'No Payment';
                $amount = '₦0';
                $type = null;
                $id = null;
            }

        } else {
            $receipt = PaymentReceiptLowerGradeColumbite::find($receiptID);
            if(Payment::where(['receipt_title' => $receiptTITLE, 'receipt_type' => $receiptTYPE, 'receipt_id' => $receiptID])->exists())
            {
                $Receipt = Payment::where(['receipt_title' => $receiptTITLE, 'receipt_type' => $receiptTYPE, 'receipt_id' => $receiptID])->first();
                $paymentAmount = $Receipt->payment_amount + $Receipt->final_payment_amount;
                $action = $Receipt->payment_action;
                $amount = '₦'.number_format($paymentAmount, 2);
                $type = $Receipt->payment_type ?? null;
                $id = $Receipt->id;
            } else {
                $paymentAmount = 0;
                $action = 'No Payment';
                $amount = '₦0';
                $type = null;
                $id = null;
            }
        }

        return view('accountant.payments.process', [
            'receiptTITLE' => $receiptTITLE,
            'receiptTYPE' => $receiptTYPE,
            'receiptID' => $receiptID,
            'receipt' => $receipt,
            'paymentAmount' => $paymentAmount,
            'action' => $action,
            'amount' => $amount,
            'type' => $type,
            'id' => $id
        ]);
    }

    public function payments_view_details($id)
    {
        $finder = Crypt::decrypt($id);

        $full = Payment::where('id', $finder)->first();
        if($full)
        {
            $full_payment = Payment::where(['receipt_title' => $full->receipt_title, 'receipt_type' => $full->receipt_type, 'receipt_id' => $full->receipt_id])->where('final_payment_type', null)->where('final_payment_amount',  null)->where('final_date_paid', null)->first();
            $part_payment = Payment::where(['receipt_title' => $full->receipt_title, 'receipt_type' => $full->receipt_type, 'receipt_id' => $full->receipt_id])->where('final_payment_type', '<>', null)->where('final_payment_amount', '<>', null)->where('final_date_paid', '<>', null)->first();
        
            return view('accountant.payments.view', [
                'full_payment' => $full_payment ?? null,
                'part_payment' => $part_payment ?? null
            ]);
        }             
        
        $part = Payment::where('id', $finder)->first();
        if($part)
        {
            $full_payment = Payment::where(['receipt_title' => $part->receipt_title, 'receipt_type' => $part->receipt_type, 'receipt_id' => $part->receipt_id])->where('final_payment_type', null)->where('final_payment_amount',  null)->where('final_date_paid', null)->first();
            $part_payment = Payment::where(['receipt_title' => $part->receipt_title, 'receipt_type' => $part->receipt_type, 'receipt_id' => $part->receipt_id])->where('final_payment_type', '<>', null)->where('final_payment_amount', '<>', null)->where('final_date_paid', '<>', null)->first();
        }

        return view('accountant.payments.view', [
            'full_payment' => $full_payment ?? null,
            'part_payment' => $part_payment ?? null
        ]);
    }

    public function expenses_view(Request $request)
    {
        if($request->start_date == null && $request->end_date == null && $request->source == null)
        {
            $expenses = Expenses::latest()->where('user_id', Auth::user()->id)->get();
        } elseif($request->start_date !== null && $request->end_date !== null && $request->source == null)
        {
            $expenses = Expenses::latest()->where('user_id', Auth::user()->id)->whereBetween('date', [$request->start_date, $request->end_date])->get();
        } elseif($request->start_date == null && $request->end_date == null && $request->source !== null)
        {
            $expenses = Expenses::latest()->where('user_id', Auth::user()->id)->where('payment_source', $request->source)->get();
        } else {
            $expenses = Expenses::latest()->where('user_id', Auth::user()->id)->where('payment_source', $request->source)->whereBetween('date', [$request->start_date, $request->end_date])->get();
        }

        return view('accountant.view_expenses', [
            'expenses' => $expenses,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'source' => $request->source
        ]);
    }

    public function expenses_add()
    {
        return view('accountant.add_expenses');
    }
    
    public function expenses_post(Request $request)
    {
        $this->validate($request, [
            'miscellaneous_expense_type' => ['required', 'string', 'max:255'],
            'payment_source' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric'],
            'supplier' => ['required', 'numeric'],
            'supplier_additional_field' => ['nullable', 'string'],
            'collected_by' => ['required', 'string'],
            'date' => ['required', 'date'],
        ]);

        if($request->supplier <> 0)
        {
            $supplier = User::find($request->supplier);

            if(!$supplier)
            {
                return back()->with([
                    'type' => 'danger',
                    'message' => 'Supplier not found in our database.'
                ]);
            }
            $supply = $supplier->id;
        } else {
            $supply = null;
        }

        if (request()->hasFile('receipt')) 
        {
            $this->validate($request, [
                'receipt' => 'required|mimes:jpeg,png,jpg'
            ]);
            
            $filename = request()->receipt->getClientOriginalName();
            request()->receipt->storeAs('expenses_receipts', $filename, 'public');

            $expense = Expenses::create([
                'user_id' => Auth::user()->id,
                'miscellaneous_expense_type' => $request->miscellaneous_expense_type,
                'supplier' => $supply,
                'payment_source' => $request->payment_source,
                'category' => $request->category,
                'description' => $request->description,
                'amount' => $request->amount,
                'date' => $request->date,
                'recurring_expense' => $request->recurring_expense,
                'supplier_additional_field' => $request->supplier_additional_field,
                'collected_by' => $request->collected_by,
                'receipt' => '/storage/expenses_receipts/'.$filename
            ]);

        } else {
            $expense = Expenses::create([
                'user_id' => Auth::user()->id,
                'miscellaneous_expense_type' => $request->miscellaneous_expense_type,
                'supplier' => $supply,
                'payment_source' => $request->payment_source,
                'category' => $request->category,
                'description' => $request->description,
                'amount' => $request->amount,
                'date' => $request->date,
                'supplier_additional_field' => $request->supplier_additional_field,
                'collected_by' => $request->collected_by,
                'recurring_expense' => $request->recurring_expense
            ]);
        }

        Transaction::create([
            'user_id' => Auth::user()->id,
            'accountant_process_id' => $expense->id,
            'amount' => $expense->amount,
            'reference' => config('app.name'),
            'status' => 'Expense'
        ]);

        return back()->with([
            'alertType' => 'success',
            'back' => route('expenses.view'),
            'message' => 'Expense added successfully!'
        ]);
    }

    public function daily_balance()
    {
        $today = Carbon::now()->format('Y-m-d');
        $yesterday = Carbon::yesterday()->format('Y-m-d');

        $balance = Balance::whereDate('date', Carbon::now()->format('Y-m-d'))->first();
        $balances = Balance::whereDate('date', $yesterday)->get();

        $totalBalance = Balance::whereDate('date', $today)->first()->starting_balance ?? 0;
        
        $expenses = Expenses::whereDate('date', $yesterday)->get()->sum('amount');

        $paymentsDateCash = Payment::where('payment_type', 'Cash')->whereDate('date_paid', $yesterday)->get();
        $paymentsFinalCash = Payment::where('payment_type', 'Cash')->whereDate('final_date_paid', $yesterday)->get();
        $cash = $paymentsDateCash->sum('payment_amount') + $paymentsFinalCash->sum('final_payment_amount');

        $paymentsDateTransfer = Payment::where('payment_type', 'Direct Transfer')->whereDate('date_paid', $yesterday)->get();
        $paymentsFinalTransfer = Payment::where('payment_type', 'Direct Transfer')->whereDate('final_date_paid', $yesterday)->get();
        $transfer =  $paymentsDateTransfer->sum('payment_amount') + $paymentsFinalTransfer->sum('final_payment_amount');

        $paymentsDateCheque = Payment::where('payment_type', 'Transfer by Cheques')->whereDate('date_paid', $yesterday)->get();
        $paymentsFinalCheque = Payment::where('payment_type', 'Transfer by Cheques')->whereDate('final_date_paid', $yesterday)->get();
        $cheques = $paymentsDateCheque->sum('payment_amount') + $paymentsFinalCheque->sum('final_payment_amount');

        $closing_balance = $expenses + $cash + $transfer + $cheques;

        return view('accountant.daily_balance')->with([
            'starting_balance' => $totalBalance,
            'balances' => $balances,
            'closing_balance' => $closing_balance
        ]);

        // $date = Balance::whereDate('date', Carbon::now()->format('Y-m-d'))->first();

        // if($date)
        // {
        //     $starting_balance = $date->starting_balance;
        //     $additional_income = $date->additional_income;
        //     $amount_used = $date->amount_used;
        //     $remaining_balance = $date->remaining_balance;
        // } else {
        //     $starting_balance = null;
        //     $additional_income = null;
        //     $amount_used = null;
        //     $remaining_balance = null;
        // }

        // return view('accountant.daily_balance')->with([
        //     'starting_balance' => $starting_balance,
        //     'additional_income' => $additional_income,
        //     'amount_used' => $amount_used,
        //     'remaining_balance' => $remaining_balance
        // ]);
    }

    public function daily_balance_add(Request $request)
    {
        $this->validate($request, [
            'starting_balance' => ['required', 'numeric']
        ]);
    
        $date = Carbon::now()->format('Y-m-d');
    
        // Check if there's a balance record for the current date
        $balance = Balance::whereDate('date', $date)->first();
    
        if ($balance && $request->starting_balance == $balance->starting_balance) {
            // If the record exists and the starting balance is the same, no need to update
            return back()->with([
                'alertType' => 'info',
                'message' => 'Daily starting balance is the same.'
            ]);
        }
    
        // If there's no record for the current date or the balance is different
        if ($balance) {
            // Update the existing record
            $balance->update([
                'starting_balance' => $balance->starting_balance + $request->starting_balance,
            ]);
        } else {
            // Create a new record
            Balance::create([
                'starting_balance' => $request->starting_balance,
                'date' => $date,
            ]);
        }
    
        return back()->with([
            'alertType' => 'success',
            'message' => 'Daily starting balance updated successfully.'
        ]);
    }
}
