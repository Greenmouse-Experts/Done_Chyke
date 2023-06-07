<?php

namespace App\Http\Controllers;

use App\Models\Expenses;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function expenses_view(Request $request)
    {
        if($request->start_date == null && $request->end_date == null)
        {
            $expenses = Expenses::latest()->where('user_id', Auth::user()->id)->get();
        } else {
            $expenses = Expenses::latest()->where('user_id', Auth::user()->id)->whereBetween('date', [$request->start_date, $request->end_date])->get();
        }

        return view('accountant.view_expenses', [
            'expenses' => $expenses
        ]);
    }

    public function expenses_add()
    {
        return view('accountant.add_expenses');
    }
    
    public function expenses_post(Request $request)
    {
        $this->validate($request, [
            'payment_source' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric'],
            'supplier' => ['required', 'numeric'],
            'date' => ['required', 'date'],
        ]);

        $supplier = User::find($request->supplier);

        if(!$supplier)
        {
            return back()->with([
                'type' => 'danger',
                'message' => 'Supplier not found in our database.'
            ]);
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
                'supplier' => $supplier->id,
                'payment_source' => $request->payment_source,
                'category' => $request->category,
                'description' => $request->description,
                'amount' => $request->amount,
                'date' => $request->date,
                'recurring_expense' => $request->recurring_expense,
                'receipt' => '/storage/expenses_receipts/'.$filename
            ]);

        } else {
            $expense = Expenses::create([
                'user_id' => Auth::user()->id,
                'supplier' => $supplier->id,
                'payment_source' => $request->payment_source,
                'category' => $request->category,
                'description' => $request->description,
                'amount' => $request->amount,
                'date' => $request->date,
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
}
