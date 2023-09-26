<?php

namespace App\Http\Controllers;

use App\Models\AnalysisCalculation;
use App\Models\Balance;
use App\Models\BeratingCalculation;
use App\Models\Expenses;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\PaymentReceiptColumbite;
use App\Models\PaymentReceiptTin;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
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
    
    public function dashboard()
    {
        /* This sets the $time variable to the current hour in the 24 hour clock format */
        $time = date("H");
        /* Set the $timezone variable to become the current timezone */
        $timezone = date("e");
        /* If the time is less than 1200 hours, show good morning */
        if ($time < "12") {
            $moment = "Good morning";
        } else
        /* If the time is grater than or equal to 1200 hours, but less than 1700 hours, so good afternoon */
        if ($time >= "12" && $time < "17") {
            $moment = "Good afternoon";
        } else
        /* Should the time be between or equal to 1700 and 1900 hours, show good evening */
        if ($time >= "17" && $time < "19") {
            $moment = "Good evening";
        } else
        /* Finally, show good night if the time is greater than or equal to 1900 hours */
        if ($time >= "19") {
            $moment = "Good night";
        }

        $today = Carbon::now()->format('Y-m-d');
        $yesterday = Carbon::yesterday()->format('Y-m-d');

        $monthly_expenses = Expenses::whereDate('date', $today)->sum('amount');
        $monthly_expenses_count = Expenses::whereDate('date', $today)->get()->count();

        $notifications = Notification::latest()->where('to', Auth::user()->id)->get()->take(2);

        $totalBalance = Balance::whereDate('date', $today)->first()->starting_balance ?? 0;

        $totalStartingBalance = $totalBalance;

        $paymentsDateCash = Payment::where('payment_type', 'Cash')->whereDate('date_paid', $today)->get();
        $paymentsFinalCash = Payment::where('payment_type', 'Cash')->whereDate('final_date_paid', $today)->get();
        $paymentCash = $paymentsDateCash->sum('payment_amount') + $paymentsFinalCash->sum('final_payment_amount');

        $expensesCash = Expenses::where('payment_source', 'Cash')->whereDate('date', $today)->get()->sum('amount');

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

        $expensesCash = Expenses::where('payment_source', 'Cash')->whereDate('date', $yesterday)->get()->sum('amount');
        $expensesCheque = Expenses::where('payment_source', 'Transfer by Cheques')->whereDate('date', $yesterday)->get()->sum('amount');
        $expensesTransfer = Expenses::where('payment_source', 'Direct Transfer')->whereDate('date', $yesterday)->get()->sum('amount');

        $payments = Payment::latest()->where('payment_action', 'Full Payment')->get();

        return view('dashboard.dashboard', [
            'moment' => $moment,
            'notifications' => $notifications,
            'monthly_expenses' => $monthly_expenses,
            'monthly_expenses_count' => $monthly_expenses_count,
            'totalStartingBalance' => $totalStartingBalance,
            'paymentCash' => $paymentCash,
            'expensesCash' => $expensesCash,
            'closing_balance' => $closing_balance,
            'direct_transfer' => $expensesTransfer + $transfer,
            'transfer_cheque' => $expensesCheque + $cheques,
            'payments' => $payments
        ]);
    }

    public function profile()
    {
        return view('dashboard.profile');
    }

    public function update_profile(Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
        ]);

        $user = User::findorfail(Auth::user()->id);

        if($user->email == $request->email)
        {
            $user->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'gender' => $request->gender,
            ]); 
        } else {
            //Validate Request
            $this->validate($request, [
                'email' => ['string', 'email', 'max:255', 'unique:users'],
            ]);

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'gender' => $request->gender,
            ]); 
        }

        return back()->with([
            'alertType' => 'success',
            'message' => $user->name. ' profile updated successfully!'
        ]);
    }

    public function update_password(Request $request)
    {
        //Validate Request
        $this->validate($request, [
            'new_password' => ['required', 'string', 'min:8', 'confirmed']
        ]);

        $user = User::findorfail(Auth::user()->id);
        
        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with([
            'alertType' => 'success',
            'message' => $user->name. ' password updated successfully.'
        ]); 
    }

    public function upload_profile_picture(Request $request)
    {
        $this->validate($request, [
            'avatar' => 'required|mimes:jpeg,png,jpg',
        ]);

        $user = User::findorfail(Auth::user()->id);

        $filename = request()->avatar->getClientOriginalName();
        if($user->avatar) {
            Storage::delete(str_replace("storage", "public", $user->avatar));
        }
        request()->avatar->storeAs('users_avatar', $filename, 'public');
        $user->avatar = '/storage/users_avatar/'.$filename;
        $user->save();

        return back()->with([
            'alertType' => 'success',
            'message' => $user->name. ' profile picture uploaded successfully!'
        ]);
    }

    // Notifications
    public function notifications()
    {
        $notifications = Notification::latest()->where('to', Auth::user()->id)->get();

        return view('dashboard.notifications', [
            'notifications' => $notifications
        ]);
    }
    
    public function read_notification($id)
    {
        $finder = Crypt::decrypt($id);

        $notification = Notification::find($finder);

        $notification->update([
            'status' => 'Read'
        ]);

        return back();
    }

    public function get_berating_rate(Request $request)
    {
        $beratingcalculation = BeratingCalculation::find($request->id);

        return $beratingcalculation;
    }

    public function update_berating_rate(Request $request)
    {
        $this->validate($request, [
            'price' => ['required', 'numeric'],
            'unit_price' => ['required', 'numeric'],
        ]);

        $beratingcalculation = BeratingCalculation::find($request->id);

        if($beratingcalculation->grade == $request->grade)
        {
            $beratingcalculation->update([
                'grade' => $request->grade,
                'price' => $request->price,
                'unit_price' => $request->unit_price
            ]);
    
        } else {
            $this->validate($request, [
                'grade' => ['required', 'numeric', 'unique:berating_calculations'],
            ]);

            $beratingcalculation->update([
                'grade' => $request->grade,
                'price' => $request->price,
                'unit_price' => $request->unit_price
            ]);
        }

        return back()->with([
            'alertType' => 'success',
            'message' => 'Updated successfully!'
        ]);
    }

    public function add_berating_rate(Request $request)
    {
        $response = [
            'grade.regex' => 'Grade field requires decimal point.'
        ];

        $this->validate($request, [
            'grade' => ['required', 'numeric', 'unique:berating_calculations', 'regex:/^[-+]?[0-9]+\.[0-9]+$/'],
            'price' => ['required', 'numeric'],
            'unit_price' => ['required', 'numeric'], 
        ], $response);

        BeratingCalculation::create([
            'grade' => $request->grade,
            'price' => $request->price,
            'unit_price' => $request->unit_price
        ]);

        return back()->with([
            'alertType' => 'success',
            'message' => 'Added successfully!'
        ]);
    }

    public function get_analysis_rate(Request $request)
    {
        $analysiscalculation = AnalysisCalculation::find($request->id);

        return $analysiscalculation;
    }

    public function update_analysis_rate(Request $request)
    {
        $this->validate($request, [
            'dollar' => ['required', 'numeric'],
            'exchange' => ['required', 'numeric'],
        ]);

        $analysiscalculation = AnalysisCalculation::find($request->id);
        
        if($analysiscalculation->percentage_min == $request->percentage_min && $analysiscalculation->percentage_max == $request->percentage_max)
        {
            $analysiscalculation->update([
                'dollar_rate' => $request->dollar,
                'exchange_rate' => $request->exchange
            ]);

        } else {
            $this->validate($request, [
                'percentage_min' => ['required', 'string', 'max:255', 'unique:analysis_calculations'],
                'percentage_max' => ['required', 'string', 'max:255', 'unique:analysis_calculations'],
            ]);

            $analysiscalculation->update([
                'percentage_min' => $request->percentage_min,
                'percentage_max' => $request->percentage_max,
                'dollar_rate' => $request->dollar,
                'exchange_rate' => $request->exchange
            ]);
        }

        return back()->with([
            'alertType' => 'success',
            'message' => 'Updated successfully!'
        ]);
    }

    public function add_analysis_rate(Request $request)
    {
        $this->validate($request, [
            'percentage_min' => ['required', 'string', 'max:255', 'unique:analysis_calculations'],
            'percentage_max' => ['required', 'string', 'max:255', 'unique:analysis_calculations'],
            'dollar' => ['required', 'numeric'],
            'exchange' => ['required', 'numeric'],
        ]);
        
       AnalysisCalculation::create([
            'percentage_min' => $request->percentage_min,
            'percentage_max' => $request->percentage_max,
            'dollar_rate' => $request->dollar,
            'exchange_rate' => $request->exchange
        ]);

        return back()->with([
            'alertType' => 'success',
            'message' => 'Added successfully!'
        ]);
    }
}
