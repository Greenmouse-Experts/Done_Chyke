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

    public function expenses_view()
    {
        return view('accountant.view_expenses');
    }

    public function expenses_add()
    {
        return view('accountant.add_expenses');
    }
    
    public function expenses_post(Request $request)
    {
        $this->validate($request, [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric'],
            'date' => ['required', 'date'],
        ]);

        if (request()->hasFile('receipt')) 
        {
            $this->validate($request, [
                'receipt' => 'required|mimes:jpeg,png,jpg'
            ]);
            
            $filename = request()->receipt->getClientOriginalName();
            request()->receipt->storeAs('expenses_receipts', $filename, 'public');

            $expense = Expenses::create([
                'user_id' => Auth::user()->id,
                'title' => $request->title,
                'description' => $request->description,
                'amount' => $request->amount,
                'date' => $request->date,
                'receipt' => '/storage/expenses_receipts/'.$filename
            ]);

        } else {

            $expense = Expenses::create([
                'user_id' => Auth::user()->id,
                'title' => $request->title,
                'description' => $request->description,
                'amount' => $request->amount,
                'date' => $request->date
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
