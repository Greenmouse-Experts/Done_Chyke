<?php

namespace App\Http\Controllers;

use App\Models\AnalysisCalculation;
use App\Models\Balance;
use App\Models\Benchmark;
use App\Models\BeratingCalculation;
use App\Models\PaymentReceiptColumbite;
use App\Models\Expenses;
use App\Models\Notification;
use App\Models\PaymentReceiptLowerGradeColumbite;
use App\Models\PaymentReceiptTin;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use DataTables;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth','verified']);
        define("pound_rate", 70);
        define("rate", 50);
        define("fixed_rate", 2.20462);
        define("columbite_rate", 80);
    }

    function replaceCharsInNumber($num, $chars) 
    {
        return substr((string) $num, 0, -strlen($chars)) . $chars;
    }

    public function dashboard(Request $request)
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

        $staffs = User::where('account_type', '!=', 'Administrator')->get()->count();
        $month = date('m');
        $year = date('Y');
        $from =  Carbon::now()->startOfWeek()->format('Y-m-d');
        $to = Carbon::now()->endOfWeek()->format('Y-m-d');
        $today = Carbon::now()->format('Y-m-d');

        if($request->expenses_interval == 'yearly')
        {
            $expenses = Expenses::whereYear('date', $year)->sum('amount');
        } elseif($request->expenses_interval == 'monthly')
        {
            $expenses = Expenses::whereMonth('date', $month)->sum('amount');
        } elseif($request->expenses_interval == 'weekly')
        {
            $expenses = Expenses::whereBetween('date',[$from, $to])->get()->sum('amount');
        } else {
            $expenses = Expenses::whereMonth('date', $month)->sum('amount');
        }

        if($request->receipt_interval == 'weekly')
        {
            $receiptTinPound = PaymentReceiptTin::where('type','pound')->whereBetween('date_of_purchase',[$from, $to])->get()->sum('price');
            $receiptTinPoundCount = PaymentReceiptTin::where('type','pound')->whereBetween('date_of_purchase',[$from, $to])->get()->count();
            $receiptTinKg = PaymentReceiptTin::where('type','kg')->whereBetween('date_of_purchase',[$from, $to])->get()->sum('price');
            $receiptTinKgCount = PaymentReceiptTin::where('type','kg')->whereBetween('date_of_purchase',[$from, $to])->get()->count();
            $receiptColumbitePound = PaymentReceiptColumbite::where('type','pound')->whereBetween('date_of_purchase',[$from, $to])->get()->sum('price');
            $receiptColumbitePoundCount = PaymentReceiptColumbite::where('type','pound')->whereBetween('date_of_purchase',[$from, $to])->get()->count();
            $receiptColumbiteKg = PaymentReceiptColumbite::where('type','kg')->whereBetween('date_of_purchase',[$from, $to])->get()->sum('price');
            $receiptColumbiteKgCount = PaymentReceiptColumbite::where('type','kg')->whereBetween('date_of_purchase',[$from, $to])->get()->count();
        } elseif($request->receipt_interval == 'monthly')
        {
            $receiptTinPound = PaymentReceiptTin::where('type','pound')->whereMonth('date_of_purchase', $month)->sum('price');
            $receiptTinPoundCount = PaymentReceiptTin::where('type','pound')->whereMonth('date_of_purchase', $month)->get()->count();
            $receiptTinKg = PaymentReceiptTin::where('type','kg')->whereMonth('date_of_purchase', $month)->sum('price');
            $receiptTinKgCount = PaymentReceiptTin::where('type','kg')->whereMonth('date_of_purchase', $month)->get()->count();
            $receiptColumbitePound = PaymentReceiptColumbite::where('type','pound')->whereMonth('date_of_purchase', $month)->sum('price');
            $receiptColumbitePoundCount = PaymentReceiptColumbite::where('type','pound')->whereMonth('date_of_purchase', $month)->get()->count();
            $receiptColumbiteKg = PaymentReceiptColumbite::where('type','kg')->whereMonth('date_of_purchase', $month)->sum('price');
            $receiptColumbiteKgCount = PaymentReceiptColumbite::where('type','kg')->whereMonth('date_of_purchase', $month)->get()->count();
        } elseif($request->receipt_interval == 'today')
        {
            $receiptTinPound = PaymentReceiptTin::where('type','pound')->whereDate('date_of_purchase', $today)->sum('price');
            $receiptTinPoundCount = PaymentReceiptTin::where('type','pound')->whereDate('date_of_purchase', $today)->get()->count();
            $receiptTinKg = PaymentReceiptTin::where('type','kg')->whereDate('date_of_purchase', $today)->sum('price');
            $receiptTinKgCount = PaymentReceiptTin::where('type','kg')->whereDate('date_of_purchase', $today)->get()->count();
            $receiptColumbitePound = PaymentReceiptColumbite::where('type','pound')->whereDate('date_of_purchase', $today)->sum('price');
            $receiptColumbitePoundCount = PaymentReceiptColumbite::where('type','pound')->whereDate('date_of_purchase', $today)->get()->count();
            $receiptColumbiteKg = PaymentReceiptColumbite::where('type','kg')->whereDate('date_of_purchase', $today)->sum('price');
            $receiptColumbiteKgCount = PaymentReceiptColumbite::where('type','kg')->whereDate('date_of_purchase', $today)->get()->count();
        } else {
            $receiptTinPound = PaymentReceiptTin::where('type','pound')->whereDate('date_of_purchase', $today)->sum('price');
            $receiptTinPoundCount = PaymentReceiptTin::where('type','pound')->whereDate('date_of_purchase', $today)->get()->count();
            $receiptTinKg = PaymentReceiptTin::where('type','kg')->whereDate('date_of_purchase', $today)->sum('price');
            $receiptTinKgCount = PaymentReceiptTin::where('type','kg')->whereDate('date_of_purchase', $today)->get()->count();
            $receiptColumbitePound = PaymentReceiptColumbite::where('type','pound')->whereDate('date_of_purchase', $today)->sum('price');
            $receiptColumbitePoundCount = PaymentReceiptColumbite::where('type','pound')->whereDate('date_of_purchase', $today)->get()->count();
            $receiptColumbiteKg = PaymentReceiptColumbite::where('type','kg')->whereDate('date_of_purchase', $today)->sum('price');
            $receiptColumbiteKgCount = PaymentReceiptColumbite::where('type','kg')->whereDate('date_of_purchase', $today)->get()->count();
        }

        if($request->receipt_lower_grade == 'weekly')
        {
            $receiptLowerGradeColumbitePound = PaymentReceiptLowerGradeColumbite::where('type','pound')->whereBetween('date_of_purchase',[$from, $to])->get()->sum('price');
            $receiptLowerGradeColumbitePoundCount = PaymentReceiptLowerGradeColumbite::where('type','pound')->whereBetween('date_of_purchase',[$from, $to])->get()->count();
            $receiptLowerGradeColumbiteKg = PaymentReceiptLowerGradeColumbite::where('type','kg')->whereBetween('date_of_purchase',[$from, $to])->get()->sum('price');
            $receiptLowerGradeColumbiteKgCount = PaymentReceiptLowerGradeColumbite::where('type','kg')->whereBetween('date_of_purchase',[$from, $to])->get()->count();
        } elseif($request->receipt_lower_grade == 'monthly')
        {
            $receiptLowerGradeColumbitePound = PaymentReceiptLowerGradeColumbite::where('type','pound')->whereMonth('date_of_purchase', $month)->sum('price');
            $receiptLowerGradeColumbitePoundCount = PaymentReceiptLowerGradeColumbite::where('type','pound')->whereMonth('date_of_purchase', $month)->get()->count();
            $receiptLowerGradeColumbiteKg = PaymentReceiptLowerGradeColumbite::where('type','kg')->whereMonth('date_of_purchase', $month)->sum('price');
            $receiptLowerGradeColumbiteKgCount = PaymentReceiptLowerGradeColumbite::where('type','kg')->whereMonth('date_of_purchase', $month)->get()->count();
        } elseif($request->receipt_lower_grade == 'today')
        {
            $receiptLowerGradeColumbitePound = PaymentReceiptLowerGradeColumbite::where('type','pound')->whereDate('date_of_purchase', $today)->sum('price');
            $receiptLowerGradeColumbitePoundCount = PaymentReceiptLowerGradeColumbite::where('type','pound')->whereDate('date_of_purchase', $today)->get()->count();
            $receiptLowerGradeColumbiteKg = PaymentReceiptLowerGradeColumbite::where('type','kg')->whereDate('date_of_purchase', $today)->sum('price');
            $receiptLowerGradeColumbiteKgCount = PaymentReceiptLowerGradeColumbite::where('type','kg')->whereDate('date_of_purchase', $today)->get()->count();
        } else {
            $receiptLowerGradeColumbitePound = PaymentReceiptLowerGradeColumbite::where('type','pound')->whereDate('date_of_purchase', $today)->sum('price');
            $receiptLowerGradeColumbitePoundCount = PaymentReceiptLowerGradeColumbite::where('type','pound')->whereDate('date_of_purchase', $today)->get()->count();
            $receiptLowerGradeColumbiteKg = PaymentReceiptLowerGradeColumbite::where('type','kg')->whereDate('date_of_purchase', $today)->sum('price');
            $receiptLowerGradeColumbiteKgCount = PaymentReceiptLowerGradeColumbite::where('type','kg')->whereDate('date_of_purchase', $today)->get()->count();
        }

        $totalBalance = Balance::whereDate('date', $today)->first()->starting_balance ?? 0;
        $totalAdditionalIncome = Balance::whereDate('date', $today)->first()->additional_income ?? 0;
        $totalRemainingBalance = Balance::whereDate('date', '!=', $today)->sum('remaining_balance') ?? 0;

        $totalStartingBalance = $totalBalance + $totalAdditionalIncome + $totalRemainingBalance;

        $totalReceipt = PaymentReceiptTin::get()->count() + PaymentReceiptColumbite::get()->count();

        $response = [
            'expenses' => number_format($expenses, 2),
            'receiptTinPound' => number_format($receiptTinPound, 2),
            'receiptTinPoundCount' => $receiptTinPoundCount,
            'receiptTinKg' => number_format($receiptTinKg, 2),
            'receiptTinKgCount' => $receiptTinKgCount,
            'receiptColumbitePound' => number_format($receiptColumbitePound, 2),
            'receiptColumbitePoundCount' => $receiptColumbitePoundCount,
            'receiptColumbiteKg' => number_format($receiptColumbiteKg, 2),
            'receiptColumbiteKgCount' => $receiptColumbiteKgCount,
            'receiptLowerGradeColumbitePound' => number_format($receiptLowerGradeColumbitePound, 2),
            'receiptLowerGradeColumbitePoundCount' => $receiptLowerGradeColumbitePoundCount,
            'receiptLowerGradeColumbiteKg' => number_format($receiptLowerGradeColumbiteKg, 2),
            'receiptLowerGradeColumbiteKgCount' => $receiptLowerGradeColumbiteKgCount
        ];

        if (request()->ajax()) {
            return response()->json($response);
        }

        return view('admin.dashboard', [
            'moment' => $moment,
            'staffs' => $staffs,
            'expenses' => $expenses,
            'receiptTinKg' => $receiptTinKg,
            'receiptTinKgCount' => $receiptTinKgCount,
            'receiptTinPound' => $receiptTinPound,
            'receiptTinPoundCount' => $receiptTinPoundCount,
            'receiptColumbitePound' => $receiptColumbitePound,
            'receiptColumbitePoundCount' => $receiptColumbitePoundCount,
            'receiptColumbiteKg' => $receiptColumbiteKg,
            'receiptColumbiteKgCount' => $receiptColumbiteKgCount,
            'totalStartingBalance' => $totalStartingBalance,
            'totalReceipt' => $totalReceipt,
            'receiptLowerGradeColumbitePound' => $receiptLowerGradeColumbitePound,
            'receiptLowerGradeColumbitePoundCount' => $receiptLowerGradeColumbitePoundCount,
            'receiptLowerGradeColumbiteKg' => $receiptLowerGradeColumbiteKg,
            'receiptLowerGradeColumbiteKgCount' => $receiptLowerGradeColumbiteKgCount
        ]);
    }

    public function profile()
    {
        return view('admin.profile');
    }

    public function update_admin_profile(Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
        ]);

        $user = User::findorfail(Auth::user()->id);

        if($user->email == $request->email)
        {
            $user->update([
                'name' => $request->name,
            ]); 
        } else {
            //Validate Request
            $this->validate($request, [
                'email' => ['string', 'email', 'max:255', 'unique:users'],
            ]);

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]); 
        }

        return back()->with([
            'alertType' => 'success',
            'message' => $user->name. ' profile updated successfully!'
        ]);
    }

    public function update_admin_password(Request $request)
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

    public function upload_admin_profile_picture(Request $request)
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

    public function sub_admins()
    {
        return view('admin.sub-admins.view');
    }

    public function add_sub_admin(Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed']
        ]);

  
        if($request->notify == 'on')
        {
            if($request->status !== null && $request->status == 'false')
            {
                $user = User::create([
                    'account_type' => 'Administrator',
                    'name' => $request->name,
                    'email' => $request->email,
                    'email_verified_at' => now(),
                    'password' => Hash::make($request->password),
                    'phone' => $request->phone,
                    'gender' => $request->gender,
                    'status' => false,
                    'access' => true,
                    'role' => 'Sub-admin',
                    'current_password' => $request->password
                ]);
            } else {
                $user = User::create([
                    'account_type' => 'Administrator',
                    'name' => $request->name,
                    'email' => $request->email,
                    'email_verified_at' => now(),
                    'password' => Hash::make($request->password),
                    'phone' => $request->phone,
                    'gender' => $request->gender,
                    'status' => true,
                    'access' => true,
                    'role' => 'Sub-admin',
                    'current_password' => $request->password
                ]);   
            }
            
            /** Store information to include in mail in $data as an array */
            $data = array(
                'name' => $user->name,
                'email' => $user->email,
                'password' => $request->password
            );

            /** Send message to the user */
            Mail::send('emails.notifyAdmin', $data, function ($m) use ($data) {
                $m->to($data['email'])->subject(config('app.name'));
            });

            return back()->with([
                'alertType' => 'success',
                'message' => $user->name. ' account created successfully!'
            ]);
        }
    
        if($request->status !== null && $request->status == 'false')
        {
            $user =  User::create([
                'account_type' => 'Administrator',
                'name' => $request->name,
                'email' => $request->email,
                'email_verified_at' => now(),
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'gender' => $request->gender,
                'status' => false,
                'access' => true,
                'role' => 'Sub-admin',
                'current_password' => $request->password
            ]);
        } else {
            $user = User::create([
                'account_type' => 'Administrator',
                'name' => $request->name,
                'email' => $request->email,
                'email_verified_at' => now(),
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'gender' => $request->gender,
                'status' => true,
                'access' => true,
                'role' => 'Sub-admin',
                'current_password' => $request->password
            ]);
        }

        return back()->with([
            'alertType' => 'success',
            'message' => $user->name. ' account created successfully!'
        ]);
    }

    public function sub_admin_update($id, Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
        ]);

        $finder = Crypt::decrypt($id);

        $user = User::find($finder);

        if($request->notify == 'on')
        {
            if($request->password == null)
            {
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

            $this->validate($request, [
                'password' => ['required', 'string', 'min:8', 'confirmed']
            ]);

            if($user->email == $request->email)
            {
                $user->update([
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'gender' => $request->gender,
                    'password' => Hash::make($request->password),
                    'current_password' => $request->password
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
                    'password' => Hash::make($request->password),
                    'current_password' => $request->password
                ]); 
            }

            /** Store information to include in mail in $data as an array */
            $data = array(
                'name' => $user->name,
                'email' => $user->email,
                'password' => $request->password
            );

            /** Send message to the user */
            Mail::send('emails.notifyAdminUpdate', $data, function ($m) use ($data) {
                $m->to($data['email'])->subject(config('app.name'));
            });

            return back()->with([
                'alertType' => 'success',
                'message' => $user->name. ' profile updated successfully!'
            ]);
        }

        if($request->password == null)
        {
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

        $this->validate($request, [
            'password' => ['required', 'string', 'min:8', 'confirmed']
        ]);

        if($user->email == $request->email)
        {
            $user->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'gender' => $request->gender,
                'password' => Hash::make($request->password),
                'current_password' => $request->password
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
                'password' => Hash::make($request->password),
                'current_password' => $request->password
            ]); 
        }

        /** Store information to include in mail in $data as an array */
        $data = array(
            'name' => $user->name,
            'email' => $user->email,
            'password' => $request->password
        );

        /** Send message to the user */
        Mail::send('emails.notifyAdminUpdate', $data, function ($m) use ($data) {
            $m->to($data['email'])->subject(config('app.name'));
        });
        
        return back()->with([
            'alertType' => 'success',
            'message' => $user->name. ' profile updated successfully!'
        ]);
    }

    public function sub_admin_activate($id)
    {
        $finder = Crypt::decrypt($id);

        $user = User::find($finder);

        $user->update([
            'status' => true
        ]);

        return back()->with([
            'alertType' => 'success',
            'message' => $user->name. ' account activated successfully!'
        ]);
    }

    public function sub_admin_deactivate($id)
    {
        $finder = Crypt::decrypt($id);

        $user = User::find($finder);

        $user->update([
            'status' => false
        ]);

        return back()->with([
            'alertType' => 'success',
            'message' => $user->name. ' account deactivated successfully!'
        ]);
    }

    public function sub_admin_delete($id)
    {
        $finder = Crypt::decrypt($id);

        $user = User::find($finder);

        if($user->avatar) {
            Storage::delete(str_replace("storage", "public", $user->avatar));
        }

        $user->delete();

        return back()->with([
            'alertType' => 'success',
            'message' => 'User deleted successfully!'
        ]);
    }

    public function staff()
    {
        return view('admin.staff.view');
    }

    public function staff_add()
    {
        return view('admin.staff.index');
    }

    public function staff_post(Request $request)
    {
        $this->validate($request, [
            'account_type' => ['required', 'string'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['string', 'email', 'max:255', 'unique:users']
        ]);

        if($request->account_type == 'Accountant' || $request->account_type == 'Assistant Manager' || $request->account_type == 'Store Personnel')
        {
            $this->validate($request, [
                'password' => ['required', 'string', 'min:8', 'confirmed']
            ]);

            if($request->notify == 'on')
            {
                if($request->status !== null && $request->status == 'false')
                {
                    $user = User::create([
                        'account_type' => $request->account_type,
                        'name' => $request->name,
                        'email' => $request->email,
                        'email_verified_at' => now(),
                        'password' => Hash::make($request->password),
                        'phone' => $request->phone,
                        'gender' => $request->gender,
                        'status' => false,
                        'access' => true,
                        'current_password' => $request->password
                    ]);
                } else {
                    $user = User::create([
                        'account_type' => $request->account_type,
                        'name' => $request->name,
                        'email' => $request->email,
                        'email_verified_at' => now(),
                        'password' => Hash::make($request->password),
                        'phone' => $request->phone,
                        'gender' => $request->gender,
                        'status' => true,
                        'access' => true,
                        'current_password' => $request->password
                    ]);   
                }


                /** Store information to include in mail in $data as an array */
                $data = array(
                    'name' => $user->name,
                    'email' => $user->email,
                    'password' => $request->password
                );

                /** Send message to the user */
                Mail::send('emails.notifyUser', $data, function ($m) use ($data) {
                    $m->to($data['email'])->subject(config('app.name'));
                });

                return back()->with([
                    'alertType' => 'success',
                    'back' => route('admin.staff'),
                    'message' => $user->name. ' account created successfully!'
                ]);
            }
        
            if($request->status !== null && $request->status == 'false')
            {
                $user =  User::create([
                    'account_type' => $request->account_type,
                    'name' => $request->name,
                    'email' => $request->email,
                    'email_verified_at' => now(),
                    'password' => Hash::make($request->password),
                    'phone' => $request->phone,
                    'gender' => $request->gender,
                    'status' => false,
                    'access' => true,
                    'current_password' => $request->password
                ]);
            } else {
                $user = User::create([
                    'account_type' => $request->account_type,
                    'name' => $request->name,
                    'email' => $request->email,
                    'email_verified_at' => now(),
                    'password' => Hash::make($request->password),
                    'phone' => $request->phone,
                    'gender' => $request->gender,
                    'status' => true,
                    'access' => true,
                    'current_password' => $request->password
                ]);
            }

            return back()->with([
                'alertType' => 'success',
                'back' => route('admin.staff'),
                'message' => $user->name. ' account created successfully!'
            ]);
        } else {
            if($request->status !== null && $request->status == 'false')
            {
                $user =  User::create([
                    'account_type' => $request->account_type,
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'phone' => $request->phone,
                    'gender' => $request->gender,
                    'status' => false,
                ]);
            } else {
                $user = User::create([
                    'account_type' => $request->account_type,
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'phone' => $request->phone,
                    'gender' => $request->gender,
                    'status' => true,
                ]);
            }

            return back()->with([
                'alertType' => 'success',
                'back' => route('admin.staff'),
                'message' => $user->name. ' account created successfully!'
            ]);
        }
    }

    public function staff_edit($id)
    {
        $finder = Crypt::decrypt($id);

        $user = User::find($finder);

        return view ('admin.staff.edit', [
            'user' => $user
        ]);
    }

    public function staff_activate($id)
    {
        $finder = Crypt::decrypt($id);

        $user = User::find($finder);

        $user->update([
            'status' => true
        ]);

        return back()->with([
            'alertType' => 'success',
            'message' => $user->name. ' account activated successfully!'
        ]);
    }

    public function staff_deactivate($id)
    {
        $finder = Crypt::decrypt($id);

        $user = User::find($finder);

        $user->update([
            'status' => false
        ]);

        return back()->with([
            'alertType' => 'success',
            'message' => $user->name. ' account deactivated successfully!'
        ]);
    }

    public function staff_delete($id)
    {
        $finder = Crypt::decrypt($id);

        $user = User::find($finder);

        if($user->avatar) {
            Storage::delete(str_replace("storage", "public", $user->avatar));
        }

        $user->delete();

        return back()->with([
            'alertType' => 'success',
            'message' => 'User deleted successfully!'
        ]);
    }

    public function staff_update_profile($id, Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
        ]);

        $finder = Crypt::decrypt($id);

        $user = User::find($finder);

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

    public function staff_update_password($id, Request $request)
    {
        //Validate Request
        $this->validate($request, [
            'new_password' => ['required', 'string', 'min:8', 'confirmed']
        ]);

        $finder = Crypt::decrypt($id);

        $user = User::find($finder);

        if($request->notify == 'on')
        {
            $user->password = Hash::make($request->new_password);
            $user->current_password = $request->new_password;
            $user->save();

            /** Store information to include in mail in $data as an array */
            $data = array(
                'name' => $user->name,
                'email' => $user->email,
                'password' => $request->new_password
            );

            /** Send message to the user */
            Mail::send('emails.changePassword', $data, function ($m) use ($data) {
                $m->to($data['email'])->subject(config('app.name'));
            });
        } else {
            $user->password = Hash::make($request->new_password);
            $user->save();
        }

        return back()->with([
            'alertType' => 'success',
            'message' => $user->name. ' password updated successfully.'
        ]); 
    }

    public function staff_update_profile_picture($id, Request $request)
    {
        $this->validate($request, [
            'avatar' => 'required|mimes:jpeg,png,jpg',
        ]);

        $finder = Crypt::decrypt($id);

        $user = User::find($finder);

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

    // Transactions
    public function transactions()
    {
        $transactions = Transaction::latest()->get();

        return view('admin.transactions', [
            'transactions' => $transactions
        ]);
    }

    // Notifications
    public function notifications()
    {
        $notifications = Notification::latest()->get();

        return view('admin.notifications', [
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

    // Rates List
    public function rates_berating()
    {
        return view('admin.berating-rate.view');
    }

    public function add_rate_berating()
    {
        return view('admin.berating-rate.add');
    }

    public function post_rate_berating(Request $request)
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
            'back' => route('admin.rates.berating'),
            'message' => 'Added successfully!'
        ]);
    }

    public function rate_berating_update($id, Request $request)
    {
        $this->validate($request, [
            'price' => ['required', 'numeric'],
            'unit_price' => ['required', 'numeric'],
        ]);

        $finder = Crypt::decrypt($id);
        
        $beratingcalculation = BeratingCalculation::find($finder);

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

    public function rate_berating_activate($id)
    {
        $finder = Crypt::decrypt($id);

        $beratingcalculation = BeratingCalculation::find($finder);

        $beratingcalculation->update([
            'status' => 'Active'
        ]);

        return back()->with([
            'alertType' => 'success',
            'message' => 'Activated successfully!'
        ]);
    }

    public function rate_berating_deactivate($id)
    {
        $finder = Crypt::decrypt($id);

        $beratingcalculation = BeratingCalculation::find($finder);

        $beratingcalculation->update([
            'status' => 'Inactive'
        ]);

        return back()->with([
            'alertType' => 'success',
            'message' => 'Deactivated successfully!'
        ]);
    }

    public function rate_berating_delete($id)
    {
        $finder = Crypt::decrypt($id);

        $rate = BeratingCalculation::find($finder);

        $payment_analysis_tin = PaymentReceiptTin::where('grade', $rate->id)->get();
        $payment_analysis_columbite = PaymentReceiptColumbite::where('grade', $rate->id)->get();

        if($payment_analysis_tin->count() > 0)
        {
            return back()->with([
                'type' => 'danger',
                'message' => "Sorry, rate can't be deleted, it has been used on a payment receipt."
            ]);
        }

        if($payment_analysis_columbite->count() > 0)
        {
            return back()->with([
                'type' => 'danger',
                'message' => "Sorry, rate can't be deleted, it has been used on a payment receipt."
            ]);
        }

        $rate->delete();

        return back()->with([
            'alertType' => 'success',
            'message' => 'Deleted successfully!'
        ]);
    }

    public function rates_analysis()
    {
        return view('admin.analysis-rate.view');
    }

    public function add_rate_analysis()
    {
        return view('admin.analysis-rate.add');
    }

    public function post_rate_analysis(Request $request)
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
            'back' => route('admin.rates.analysis'),
            'message' => 'Added successfully!'
        ]);
    }

    public function rate_analysis_update($id, Request $request)
    {
        $this->validate($request, [
            'dollar' => ['required', 'numeric'],
            'exchange' => ['required', 'numeric'],
        ]);

        $finder = Crypt::decrypt($id);

        $analysiscalculation = AnalysisCalculation::find($finder);
        

        if($analysiscalculation->grade == $request->grade)
        {
            $analysiscalculation->update([
                'percentage_min' => $request->percentage_min,
                'percentage_max' => $request->percentage_max,
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

    public function rate_analysis_activate($id)
    {
        $finder = Crypt::decrypt($id);

        $analysiscalculation = AnalysisCalculation::find($finder);

        $analysiscalculation->update([
            'status' => 'Active'
        ]);

        return back()->with([
            'alertType' => 'success',
            'message' => 'Activated successfully!'
        ]);
    }

    public function rate_analysis_deactivate($id)
    {
        $finder = Crypt::decrypt($id);

        $analysiscalculation = AnalysisCalculation::find($finder);

        $analysiscalculation->update([
            'status' => 'Inactive'
        ]);

        return back()->with([
            'alertType' => 'success',
            'message' => 'Deactivated successfully!'
        ]);
    }

    public function rate_analysis_delete($id)
    {
        $finder = Crypt::decrypt($id);

        AnalysisCalculation::find($finder)->delete();

        return back()->with([
            'alertType' => 'success',
            'message' => 'Deleted successfully!'
        ]);
    }

    public function rates_beanchmark()
    {
        $benchmark = Benchmark::latest()->get();

        return view('admin.benchmark.view', [
            'benchmark' => $benchmark
        ]);
    }

    public function post_rate_benchmark(Request $request)
    {
        $this->validate($request, [
            'amount' => ['required', 'numeric'],
        ]);

        $result = ($request->amount / 70 * fixed_rate) / 70;

        $bench = number_format((float)$result, 2, '.', '');
        
        Benchmark::create([
            'amount' => $request->amount,
            'benchmark_value' => $bench,
        ]);

        return back()->with([
            'alertType' => 'success',
            'message' => 'Added successfully!'
        ]);
    }

    public function rate_benchmark_update($id, Request $request)
    {
        $this->validate($request, [
            'amount' => ['required', 'numeric'],
        ]);

        $finder = Crypt::decrypt($id);

        $benchmark = Benchmark::find($finder);

        $result = ($request->amount / 70 * fixed_rate) / 70;

        $bench = number_format((float)$result, 2, '.', '');

        $benchmark->update([
            'amount' => $request->amount,
            'benchmark_value' => $bench
        ]);

        return back()->with([
            'alertType' => 'success',
            'message' => 'Updated successfully!'
        ]);
    }

    public function rate_benchmark_delete($id, Request $request)
    {
        $finder = Crypt::decrypt($id);

        Benchmark::find($finder)->delete();

        return back()->with([
            'alertType' => 'success',
            'message' => 'Deleted successfully!'
        ]);
    }

    // Payment Receipt
    public function payment_receipt_tin_view($id, Request $request)
    {
        if($id == 'kg')
        {
            if($request->start_date == null && $request->end_date == null)
            {
                $tinPaymentReceiptKg = PaymentReceiptTin::latest()->where('type', 'kg')->get();
                $tinPaymentReceiptPound = PaymentReceiptTin::latest()->where('type', 'pound')->get();
            } else {
                $tinPaymentReceiptKg = PaymentReceiptTin::latest()->where('type', 'kg')->whereBetween('date_of_purchase', [$request->start_date, $request->end_date])->get();
                $tinPaymentReceiptPound = PaymentReceiptTin::latest()->where('type', 'pound')->get();
            }

            $active_tab = $id;

            if($active_tab == 'pound') {
                return view('admin.payment-receipt.view_tin', [
                    'tinPaymentReceiptKg' => $tinPaymentReceiptKg,
                    'tinPaymentReceiptPound' => $tinPaymentReceiptPound,
                    'active_tab' => $active_tab,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date
                ]);
            } elseif($active_tab == 'kg') {
                return view('admin.payment-receipt.view_tin', [
                    'tinPaymentReceiptKg' => $tinPaymentReceiptKg,
                    'tinPaymentReceiptPound' => $tinPaymentReceiptPound,
                    'active_tab' => $active_tab,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date
                ]);
            } else {
                $active_tab == 'kg';
                return view('admin.payment-receipt.view_tin', [
                    'tinPaymentReceiptKg' => $tinPaymentReceiptKg,
                    'tinPaymentReceiptPound' => $tinPaymentReceiptPound,
                    'active_tab' => $active_tab,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date
                ]);
            }
        }

        if($id == 'pound')
        {
            if($request->start_date == null && $request->end_date == null)
            {
                $tinPaymentReceiptKg = PaymentReceiptTin::latest()->where('type', 'kg')->get();
                $tinPaymentReceiptPound = PaymentReceiptTin::latest()->where('type', 'pound')->get();
            } else {
                $tinPaymentReceiptPound = PaymentReceiptTin::latest()->where('type', 'pound')->whereBetween('date_of_purchase', [$request->start_date, $request->end_date])->get();
                $tinPaymentReceiptKg = PaymentReceiptTin::latest()->where('type', 'kg')->get();
            }

            $active_tab = $id;

            if($active_tab == 'pound') {
                return view('admin.payment-receipt.view_tin', [
                    'tinPaymentReceiptKg' => $tinPaymentReceiptKg,
                    'tinPaymentReceiptPound' => $tinPaymentReceiptPound,
                    'active_tab' => $active_tab,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date
                ]);
            } elseif($active_tab == 'kg') {
                return view('admin.payment-receipt.view_tin', [
                    'tinPaymentReceiptKg' => $tinPaymentReceiptKg,
                    'tinPaymentReceiptPound' => $tinPaymentReceiptPound,
                    'active_tab' => $active_tab,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date
                ]);
            } else {
                $active_tab == 'kg';
                return view('admin.payment-receipt.view_tin', [
                    'tinPaymentReceiptKg' => $tinPaymentReceiptKg,
                    'tinPaymentReceiptPound' => $tinPaymentReceiptPound,
                    'active_tab' => $active_tab,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date
                ]);
            }
        }
    }

    public function payment_receipt_tin_add($id)
    {
        $active_tab = $id;

        if($active_tab == 'pound') {
            return view ('admin.payment-receipt.add_tin', compact('active_tab'));
        } elseif($active_tab == 'kg') {
            return view ('admin.payment-receipt.add_tin', compact('active_tab'));
        } else {
            $active_tab == 'kg';
            return view ('admin.payment-receipt.add_tin', compact('active_tab'));
        }
    }

    public function payment_receipt_tin_pound_post(Request $request)
    {
        if($request->save) 
        {
            $this->validate($request, [
                'assist_manager' => ['required', 'string', 'max:255'],
                'supplier' => ['required', 'string', 'max:255'],
                'grade' => ['required', 'numeric'],
                'manager' => ['required', 'numeric'],
                'date_of_purchase' => ['required', 'date'],
                'receipt_no' => 'required|string',
                'receipt_image' => 'required|mimes:jpeg,png,jpg'
            ]);

            $berating = BeratingCalculation::find($request->grade);

            if(!$berating)
            {
                return redirect()->route('admin.payment.receipt.tin.add', 'pound')->with([
                    'type' => 'danger',
                    'message' => 'Admin yet to add this berating value, try again later.'
                ]); 
            }

            $response = [
                'grade' => $berating->grade,
                'price' => $berating->price,
                'unit_price' => $berating->unit_price
            ];

            $berate = json_encode($response);

            $manager = User::find($request->manager);

            if(!$manager)
            {
                return redirect()->route('admin.payment.receipt.tin.add', 'pound')->with([
                    'type' => 'danger',
                    'message' => 'Manager not found in our database.'
                ]); 
            }

            $assist_manager = User::find($request->assist_manager);

            if(!$assist_manager)
            {
                return redirect()->route('admin.payment.receipt.tin.add', 'pound')->with([
                    'type' => 'danger',
                    'message' => 'Assistant Manager not found in our database.'
                ]); 
            }

            if($request->weight == 'bag')
            {
                if($request->bag_pounds == null)
                {
                    $bag_pounds = 0;
                } else {
                    $this->validate($request, [
                        'bag_pounds' => ['required', 'numeric', 'max:69'],
                    ]);

                    $bag_pounds = $request->bag_pounds;
                }

                $this->validate($request, [
                    'bags' => ['required', 'numeric'],
                ]);

                if($bag_pounds < pound_rate)
                {
                    $price_pound = $berating->price / pound_rate;
                    $price_bag = $berating->price;

                    $equivalentPriceForBag = $request->bags * $price_bag;
                    $equivalentPriceForPound = $bag_pounds * $price_pound;
                    $total_in_pounds = ($request->bags * pound_rate) + $bag_pounds;

                    $total = $equivalentPriceForBag + $equivalentPriceForPound;

                    $totalPrice = number_format((float)$total, 0, '.', '');

                    $filename = uniqid(5).'-'.request()->receipt_image->getClientOriginalName();
                    request()->receipt_image->storeAs('payment_analysis', $filename, 'public');

                    $tinPayment = PaymentReceiptTin::create([
                        'type' => $request->type,
                        'user_id' => $assist_manager->id,
                        'supplier' => $request->supplier,
                        'staff' => $manager->id,
                        'grade' => $request->grade,
                        'bag' => $request->bags,
                        'pound' => $bag_pounds,
                        'total_in_pound' => $total_in_pounds,
                        'berating_rate_list' => $berate,
                        'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                        'date_of_purchase' => $request->date_of_purchase,
                        'receipt_no' => $request->receipt_no,
                        'receipt_image' => '/storage/payment_analysis/'.$filename
                    ]);
            
                    Transaction::create([
                        'user_id' => $assist_manager->id,
                        'accountant_process_id' => $tinPayment->id,
                        'amount' => $tinPayment->price,
                        'reference' => config('app.name'),
                        'status' => 'Payment Receipt'
                    ]);

                    Notification::create([
                        'to' => $assist_manager->id,
                        'admin_id' => Auth::user()->id,
                        'title' => config('app.name'),
                        'body' => 'Admin has added a payment receipt, with Receipt No:'.$tinPayment->receipt_no.' on your behalf.'
                    ]);

                    return redirect()->route('admin.payment.receipt.tin.add', 'pound')->with([
                        'alertType' => 'success',
                        'back' => route('admin.payment.receipt.tin.view', 'pound'),
                        'message' => 'Payment Receipt created successfully'
                    ]);
                } else {
                    return redirect()->route('admin.payment.receipt.tin.add', 'pound')->with([
                        'type' => 'danger',
                        'message' => 'Pound should not be greater or equal to '.pound_rate
                    ]);
                }
            } 

            if($request->weight == 'pound')
            {
                $this->validate($request, [
                    'pounds' => ['required', 'numeric']
                ]);

                $equivalentPriceForPound = $request->pounds * $berating->unit_price;

                $total_in_pounds = $request->pounds;

                $total = $equivalentPriceForPound;

                $totalPrice = number_format((float)$total, 0, '.', '');

                $filename = uniqid(5).'-'.request()->receipt_image->getClientOriginalName();
                request()->receipt_image->storeAs('payment_analysis', $filename, 'public');

                $tinPayment = PaymentReceiptTin::create([
                    'type' => $request->type,
                    'user_id' => $assist_manager->id,
                    'supplier' => $request->supplier,
                    'staff' => $manager->id,
                    'grade' => $request->grade,
                    'pound' => $request->pounds,
                    'total_in_pound' => $total_in_pounds,
                    'berating_rate_list' => $berate,
                    'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                    'date_of_purchase' => $request->date_of_purchase,
                    'receipt_no' => $request->receipt_no,
                    'receipt_image' => '/storage/payment_analysis/'.$filename
                ]);
        
                Transaction::create([
                    'user_id' => $assist_manager->id,
                    'accountant_process_id' => $tinPayment->id,
                    'amount' => $tinPayment->price,
                    'reference' => config('app.name'),
                    'status' => 'Payment Receipt'
                ]);

                Notification::create([
                    'to' => $assist_manager->id,
                    'admin_id' => Auth::user()->id,
                    'title' => config('app.name'),
                    'body' => 'Admin has added a payment receipt, with Receipt No:'.$tinPayment->receipt_no.' on your behalf.'
                ]);

                return redirect()->route('admin.payment.receipt.tin.add', 'pound')->with([
                    'alertType' => 'success',
                    'back' => route('admin.payment.receipt.tin.view', 'pound'),
                    'message' => 'Payment Receipt created successfully'
                ]);
            } 

            return redirect()->route('admin.payment.receipt.tin.add', 'pound')->with([
                'type' => 'danger',
                'message' => 'Please select weight type.'
            ]);
        }

        $this->validate($request, [
            'grade' => ['required', 'numeric'],
        ]);

        $berating = BeratingCalculation::find($request->grade);

        if(!$berating)
        {
            return redirect()->route('admin.payment.receipt.tin.add', 'pound')->with([
                'type' => 'danger',
                'message' => 'Admin yet to add this berating value, try again later.'
            ]); 
        }
       
        if($request->weight == 'bag')
        {
            if($request->bag_pounds == null)
            {
                $bag_pounds = 0;
            } else {
                $this->validate($request, [
                    'bag_pounds' => ['required', 'numeric', 'max:69'],
                ]);

                $bag_pounds = $request->bag_pounds;
            }

            $this->validate($request, [
                'bags' => ['required', 'numeric'],
            ]);

            if($bag_pounds < pound_rate)
            {
                $price_pound = $berating->price / pound_rate;
                $price_bag = $berating->price;

                $equivalentPriceForBag = $request->bags * $price_bag;
                $equivalentPriceForPound = $request->bag_pounds * $price_pound;

                $total = $equivalentPriceForBag + $equivalentPriceForPound;

                $totalPrice = number_format((float)$total, 0, '.', '');

                return redirect()->route('admin.payment.receipt.tin.add', 'pound')->with([
                    'previewPrice' => 'success',
                    'message' => $this->replaceCharsInNumber($totalPrice, '0')
                ]);
            } else {
                return redirect()->route('admin.payment.receipt.tin.add', 'pound')->with([
                    'type' => 'danger',
                    'message' => 'Pound should not be greater or equal to '.pound_rate
                ]);
            }
        } 

        if($request->weight == 'pound')
        {
            $this->validate($request, [
                'pounds' => ['required', 'numeric']
            ]);

            $equivalentPriceForPound = $request->pounds * $berating->unit_price;

            $total = $equivalentPriceForPound;

            $totalPrice = number_format((float)$total, 0, '.', '');

            return redirect()->route('admin.payment.receipt.tin.add', 'pound')->with([
                'previewPrice' => 'success',
                'message' => $this->replaceCharsInNumber($totalPrice, '0')
            ]);
        } 

        return redirect()->route('admin.payment.receipt.tin.add', 'pound')->with([
            'type' => 'danger',
            'message' => 'Please select weight type.'
        ]);
    }

    public function payment_receipt_tin_kg_post(Request $request)
    {
        $benchmark = Benchmark::latest()->first();

        if(!$benchmark)
        {
            return back()->with([
                'type' => 'danger',
                'message' => 'Admin yet to add benchmark, try again later.'
            ]); 
        }

        $resBench = [
            'amount' => $benchmark->amount,
            'benchmark_value' => $benchmark->benchmark_value,
        ];

        $benchMark = json_encode($resBench);
        
        if($request->save) 
        {
            $this->validate($request, [
                'assist_manager' => ['required', 'string', 'max:255'],
                'supplier' => ['required', 'string', 'max:255'],
                'grade' => ['required', 'numeric'],
                'manager' => ['required', 'numeric'],
                'date_of_purchase' => ['required', 'date'],
                'receipt_no' => 'required|string',
                'receipt_image' => 'required|mimes:jpeg,png,jpg'
            ]);

            $berating = BeratingCalculation::find($request->grade);

            if(!$berating)
            {
                return redirect()->route('admin.payment.receipt.tin.add', 'kg')->with([
                    'type' => 'danger',
                    'message' => 'Admin yet to add this berating value, try again later.'
                ]); 
            }

            $response = [
                'grade' => $berating->grade,
                'price' => $berating->price,
                'unit_price' => $berating->unit_price
            ];

            $berate = json_encode($response);

            $manager = User::find($request->manager);

            if(!$manager)
            {
                return redirect()->route('admin.payment.receipt.tin.add', 'kg')->with([
                    'type' => 'danger',
                    'message' => 'Manager not found in our database.'
                ]); 
            }

            $assist_manager = User::find($request->assist_manager);

            if(!$assist_manager)
            {
                return redirect()->route('admin.payment.receipt.tin.add', 'kg')->with([
                    'type' => 'danger',
                    'message' => 'Assistant Manager not found in our database.'
                ]); 
            }

            if($request->kgweight == 'bag')
            {
                if($request->bag_kg == null)
                {
                    $bag_kgs = 0;
                } else {
                    $this->validate($request, [
                        'bag_kg' => ['required', 'numeric', 'max:49'],
                    ]);

                    $bag_kgs = $request->bag_kg;
                }

                $this->validate($request, [
                    'bags' => ['required', 'numeric'],
                    'percentage' => ['required', 'numeric', 'min:25'],
                ]);

                $analysis = AnalysisCalculation::get();

                foreach($analysis as $analyses)
                {
                    if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                    {
                        $myDollarRate = $analyses->dollar_rate;
                        $myExchangeRate = $analyses->exchange_rate;
                    }
                }

                $dollarRate = $myDollarRate ?? 0;
                $exchangeRate = $myExchangeRate ?? 0;

                if($dollarRate == 0 && $exchangeRate == 0)
                {
                    return redirect()->route('admin.payment.receipt.tin.add', 'kg')->with([
                        'type' => 'danger',
                        'message' => 'Percentage Analysis entered not found in our database, try again.'
                    ]);
                }

                $res = [
                    'dollar_rate' => $dollarRate,
                    'exchange_rate' => $exchangeRate,
                ];
    
                $analysisRate = json_encode($res);

                if($bag_kgs < rate)
                {
                    $totalKG = $request->bags * rate + $request->bag_kg;
                
                    $sub = $benchmark->benchmark_value * $request->percentage;

                    $total = $sub * $totalKG;

                    $totalPrice = floor($total);
                    
                    $filename = uniqid(5).'-'.request()->receipt_image->getClientOriginalName();
                    request()->receipt_image->storeAs('payment_analysis', $filename, 'public');

                    $tinPayment = PaymentReceiptTin::create([
                        'type' => $request->type,
                        'user_id' => $assist_manager->id,
                        'supplier' => $request->supplier,
                        'staff' => $manager->id,
                        'grade' => $request->grade,
                        'bag' => $request->bags,
                        'kg' => $bag_kgs,
                        'total_in_kg' => $totalKG,
                        'berating_rate_list' => $berate,
                        'percentage_analysis' => $request->percentage,
                        'analysis_rate_list' => $analysisRate,
                        'benchmark' => $benchMark,
                        'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                        'date_of_purchase' => $request->date_of_purchase,
                        'receipt_no' => $request->receipt_no,
                        'receipt_image' => '/storage/payment_analysis/'.$filename
                    ]);

                    Transaction::create([
                        'user_id' => $assist_manager->id,
                        'accountant_process_id' => $tinPayment->id,
                        'amount' => $tinPayment->price,
                        'reference' => config('app.name'),
                        'status' => 'Payment Receipt'
                    ]);

                    Notification::create([
                        'to' => $assist_manager->id,
                        'admin_id' => Auth::user()->id,
                        'title' => config('app.name'),
                        'body' => 'Admin has added a payment receipt, with Receipt No:'.$tinPayment->receipt_no.' on your behalf.'
                    ]);
    
                    return redirect()->route('admin.payment.receipt.tin.add', 'kg')->with([
                        'alertType' => 'success',
                        'back' => route('admin.payment.receipt.tin.view', 'kg'),
                        'message' => 'Payment Receipt created successfully'
                    ]);
                } else {
                    return redirect()->route('admin.payment.receipt.tin.add', 'kg')->with([
                        'type' => 'danger',
                        'message' => 'kg should not be greater or equal to '.rate
                    ]);
                }
            } 

            if($request->kgweight == 'kg')
            {
                $this->validate($request, [
                    'kg' => ['required', 'numeric']
                ]);
    
                $analysis = AnalysisCalculation::get();
    
                foreach($analysis as $analyses)
                {
                    if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                    {
                        $myDollarRate = $analyses->dollar_rate;
                        $myExchangeRate = $analyses->exchange_rate;
                    }
                }

                $dollarRate = $myDollarRate ?? 0;
                $exchangeRate = $myExchangeRate ?? 0;

                if($dollarRate == 0 && $exchangeRate == 0)
                {
                    return redirect()->route('admin.payment.receipt.tin.add', 'kg')->with([
                        'type' => 'danger',
                        'message' => 'Percentage Analysis entered not found in our database, try again.'
                    ]);
                }

                $res = [
                    'dollar_rate' => $dollarRate,
                    'exchange_rate' => $exchangeRate,
                ];
    
                $analysisRate = json_encode($res);

                $sub = $benchmark->benchmark_value * $request->percentage;

                $total = $sub * $request->kg;

                $totalPrice = floor($total);

                $filename = uniqid(5).'-'.request()->receipt_image->getClientOriginalName();
                request()->receipt_image->storeAs('payment_analysis', $filename, 'public');

                $tinPayment = PaymentReceiptTin::create([
                    'type' => $request->type,
                    'user_id' => $assist_manager->id,
                    'supplier' => $request->supplier,
                    'staff' => $manager->id,
                    'grade' => $request->grade,
                    'kg' => $request->kg,
                    'total_in_kg' => $request->kg,
                    'berating_rate_list' => $berate,
                    'percentage_analysis' => $request->percentage,
                    'analysis_rate_list' => $analysisRate,
                    'benchmark' => $benchMark,
                    'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                    'date_of_purchase' => $request->date_of_purchase,
                    'receipt_no' => $request->receipt_no,
                    'receipt_image' => '/storage/payment_analysis/'.$filename
                ]);
        
                Transaction::create([
                    'user_id' => $assist_manager->id,
                    'accountant_process_id' => $tinPayment->id,
                    'amount' => $tinPayment->price,
                    'reference' => config('app.name'),
                    'status' => 'Payment Receipt'
                ]);

                Notification::create([
                    'to' => $assist_manager->id,
                    'admin_id' => Auth::user()->id,
                    'title' => config('app.name'),
                    'body' => 'Admin has added a payment receipt, with Receipt No:'.$tinPayment->receipt_no.' on your behalf.'
                ]);

                return redirect()->route('admin.payment.receipt.tin.add', 'kg')->with([
                    'alertType' => 'success',
                    'back' => route('admin.payment.receipt.tin.view', 'kg'),
                    'message' => 'Payment Receipt created successfully'
                ]);
            } 

            return redirect()->route('admin.payment.receipt.tin.add', 'kg')->with([
                'type' => 'danger',
                'message' => 'Please select weight type.'
            ]);
        }

        $this->validate($request, [
            'grade' => ['required', 'numeric'],
        ]);

        $berating = BeratingCalculation::find($request->grade);

        if(!$berating)
        {
            return redirect()->route('admin.payment.receipt.tin.add', 'kg')->with([
                'type' => 'danger',
                'message' => 'Admin yet to add this berating value, try again later.'
            ]); 
        }
       
        if($request->kgweight == 'bag')
        {
            if($request->bag_kg == null)
            {
                $bag_kgs = 0;
            } else {
                $this->validate($request, [
                    'bag_kg' => ['required', 'numeric', 'max:49'],
                ]);

                $bag_kgs = $request->bag_kg;
            }

            $this->validate($request, [
                'bags' => ['required', 'numeric'],
                'percentage' => ['required', 'numeric', 'min:25'],
            ]);

            $analysis = AnalysisCalculation::get();

            foreach($analysis as $analyses)
            {
                if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                {
                    $myDollarRate = $analyses->dollar_rate;
                    $myExchangeRate = $analyses->exchange_rate;
                }
            }

            $dollarRate = $myDollarRate ?? 0;
            $exchangeRate = $myExchangeRate ?? 0;

            if($dollarRate == 0 && $exchangeRate == 0)
            {
                return redirect()->route('admin.payment.receipt.tin.add', 'kg')->with([
                    'type' => 'danger',
                    'message' => 'Percentage Analysis entered not found in our database, try again.'
                ]);
            }

            if($bag_kgs < rate)
            {
                $totalKG = $request->bags * rate + $request->bag_kg;
                
                $sub = $benchmark->benchmark_value * $request->percentage;

                $total = $sub * $totalKG;

                $totalPrice = floor($total);

                return redirect()->route('admin.payment.receipt.tin.add', 'kg')->with([
                    'previewPrice' => 'success',
                    'message' => $this->replaceCharsInNumber($totalPrice, '0')
                ]);
            } else {
                return redirect()->route('admin.payment.receipt.tin.add', 'kg')->with([
                    'type' => 'danger',
                    'message' => 'kg should not be greater or equal to '.rate
                ]);
            }
            
        } 

        if($request->kgweight == 'kg')
        {
            $this->validate($request, [
                'kg' => ['required', 'numeric']
            ]);

            $analysis = AnalysisCalculation::get();

            foreach($analysis as $analyses)
            {
                if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                {
                    $myDollarRate = $analyses->dollar_rate;
                    $myExchangeRate = $analyses->exchange_rate;
                }
            }

            $dollarRate = $myDollarRate ?? 0;
            $exchangeRate = $myExchangeRate ?? 0;

            if($dollarRate == 0 && $exchangeRate == 0)
            {
                return redirect()->route('admin.payment.receipt.tin.add', 'kg')->with([
                    'type' => 'danger',
                    'message' => 'Percentage Analysis entered not found in our database, try again.'
                ]);
            }

            $sub = $benchmark->benchmark_value * $request->percentage;

            $total = $sub * $request->kg;

            $totalPrice = floor($total);

            return redirect()->route('admin.payment.receipt.tin.add', 'kg')->with([
                'previewPrice' => 'success',
                'message' => $this->replaceCharsInNumber($totalPrice, '0')
            ]);
        } 

        return redirect()->route('admin.payment.receipt.tin.add', 'kg')->with([
            'type' => 'danger',
            'message' => 'Please select weight type.'
        ]);
    }

    public function payment_receipt_tin_edit($id)
    {
        $finder = Crypt::decrypt($id);

        $tinPayment = PaymentReceiptTin::find($finder);

        return view('admin.payment-receipt.edit_tin', [
            'tinPayment' => $tinPayment
        ]);
    }

    public function payment_receipt_tin_pound_update($id, Request $request)
    {
        $finder = Crypt::decrypt($id);

        $tinPayment = PaymentReceiptTin::find($finder);
        
        if($request->save) 
        {
            $this->validate($request, [
                'supplier' => ['required', 'string', 'max:255'],
                'grade' => ['required', 'numeric'],
                'manager' => ['required', 'numeric'],
                'date_of_purchase' => ['required', 'date'],
                'receipt_no' => 'required|string',
            ]);

            $berating = BeratingCalculation::find($request->grade);

            if(!$berating)
            {
                return back()->with([
                    'type' => 'danger',
                    'message' => 'Admin yet to add this berating value, try again later.'
                ]); 
            }

            $response = [
                'grade' => $berating->grade,
                'price' => $berating->price,
                'unit_price' => $berating->unit_price
            ];

            $berate = json_encode($response);

            $manager = User::find($request->manager);

            if(!$manager)
            {
                return back()->with([
                    'type' => 'danger',
                    'message' => 'Manager not found in our database.'
                ]); 
            }

            if($request->weight == 'bag')
            {
                if($request->bag_pounds == null)
                {
                    $bag_pounds = 0;
                } else {
                    $this->validate($request, [
                        'bag_pounds' => ['required', 'numeric', 'max:69'],
                    ]);

                    $bag_pounds = $request->bag_pounds;
                }

                $this->validate($request, [
                    'bags' => ['required', 'numeric'],
                ]);

                if($bag_pounds < pound_rate)
                {
                    $price_pound = $berating->price / pound_rate;
                    $price_bag = $berating->price;

                    $equivalentPriceForBag = $request->bags * $price_bag;
                    $equivalentPriceForPound = $bag_pounds * $price_pound;
                    $total_in_pounds = ($request->bags * pound_rate) + $bag_pounds;

                    $total = $equivalentPriceForBag + $equivalentPriceForPound;

                    $totalPrice = number_format((float)$total, 0, '.', '');

                    if (request()->hasFile('receipt_image')) 
                    {
                        $this->validate($request, [
                            'receipt_image' => 'required|mimes:jpeg,png,jpg'
                        ]);

                        $filename = uniqid(5).'-'.request()->receipt_image->getClientOriginalName();
                        if($tinPayment->receipt_image) {
                            Storage::delete(str_replace("storage", "public", $tinPayment->receipt_image));
                        }
                        request()->receipt_image->storeAs('payment_analysis', $filename, 'public');

                        $tinPayment->update([
                            'type' => $request->type,
                            'supplier' => $request->supplier,
                            'staff' => $manager->id,
                            'grade' => $request->grade,
                            'bag' => $request->bags,
                            'pound' => $bag_pounds,
                            'total_in_pound' => $total_in_pounds,
                            'berating_rate_list' => $berate,
                            'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                            'date_of_purchase' => $request->date_of_purchase,
                            'receipt_no' => $request->receipt_no,
                            'receipt_image' => '/storage/payment_analysis/'.$filename
                        ]);
                    } else {
                        $tinPayment->update([
                            'type' => $request->type,
                            'supplier' => $request->supplier,
                            'staff' => $manager->id,
                            'grade' => $request->grade,
                            'bag' => $request->bags,
                            'pound' => $bag_pounds,
                            'total_in_pound' => $total_in_pounds,
                            'berating_rate_list' => $berate,
                            'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                            'date_of_purchase' => $request->date_of_purchase,
                            'receipt_no' => $request->receipt_no,
                        ]);
                    }

                    $transaction = Transaction::where('accountant_process_id', $tinPayment->id)->first();

                    if($transaction)
                    {
                        $transaction->update([
                            'amount' => $tinPayment->price
                        ]);
                    }

                    Notification::create([
                        'to' => $tinPayment->user_id,
                        'admin_id' => Auth::user()->id,
                        'title' => config('app.name'),
                        'body' => 'Admin has updated a payment receipt, with Receipt No:'.$tinPayment->receipt_no
                    ]);

                    return back()->with([
                        'alertType' => 'success',
                        'back' => route('admin.payment.receipt.tin.view', 'pound'),
                        'message' => 'Payment Receipt updated successfully'
                    ]);
                } else {
                    return back()->with([
                        'type' => 'danger',
                        'message' => 'Pound should not be greater or equal to '.pound_rate
                    ]);
                }
            } 

            if($request->weight == 'pound')
            {
                $this->validate($request, [
                    'pounds' => ['required', 'numeric']
                ]);

                $equivalentPriceForPound = $request->pounds * $berating->unit_price;

                $total_in_pounds = $request->pounds;

                $total = $equivalentPriceForPound;

                $totalPrice = number_format((float)$total, 0, '.', '');

                if (request()->hasFile('receipt_image')) 
                {
                    $this->validate($request, [
                        'receipt_image' => 'required|mimes:jpeg,png,jpg'
                    ]);

                    $filename = uniqid(5).'-'.request()->receipt_image->getClientOriginalName();
                    if($tinPayment->receipt_image) {
                        Storage::delete(str_replace("storage", "public", $tinPayment->receipt_image));
                    }
                    request()->receipt_image->storeAs('payment_analysis', $filename, 'public');

                    $tinPayment->update([
                        'type' => $request->type,
                        'supplier' => $request->supplier,
                        'staff' => $manager->id,
                        'grade' => $request->grade,
                        'bag' => 0,
                        'pound' => $request->pounds,
                        'total_in_pound' => $total_in_pounds,
                        'berating_rate_list' => $berate,
                        'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                        'date_of_purchase' => $request->date_of_purchase,
                        'receipt_no' => $request->receipt_no,
                        'receipt_image' => '/storage/payment_analysis/'.$filename
                    ]);

                } else {
                    $tinPayment->update([
                        'type' => $request->type,
                        'supplier' => $request->supplier,
                        'staff' => $manager->id,
                        'grade' => $request->grade,
                        'bag' => 0,
                        'pound' => $request->pounds,
                        'total_in_pound' => $total_in_pounds,
                        'berating_rate_list' => $berate,
                        'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                        'date_of_purchase' => $request->date_of_purchase,
                        'receipt_no' => $request->receipt_no
                    ]);
                }

                $transaction = Transaction::where('accountant_process_id', $tinPayment->id)->first();

                if($transaction)
                {
                    $transaction->update([
                        'amount' => $tinPayment->price
                    ]);
                }

                Notification::create([
                    'to' => $tinPayment->user_id,
                    'admin_id' => Auth::user()->id,
                    'title' => config('app.name'),
                    'body' => 'Admin has updated a payment receipt, with Receipt No:'.$tinPayment->receipt_no
                ]);

                return back()->with([
                    'alertType' => 'success',
                    'back' => route('admin.payment.receipt.tin.view', 'pound'),
                    'message' => 'Payment Receipt updated successfully'
                ]);
            } 

            return back()->with([
                'type' => 'danger',
                'message' => 'Please select weight type.'
            ]);
        }

        $this->validate($request, [
            'grade' => ['required', 'numeric'],
        ]);

        $berating = BeratingCalculation::find($request->grade);

        if(!$berating)
        {
            return back()->with([
                'type' => 'danger',
                'message' => 'Admin yet to add this berating value, try again later.'
            ]); 
        }
       
        if($request->weight == 'bag')
        {
            if($request->bag_pounds == null)
            {
                $bag_pounds = 0;
            } else {
                $this->validate($request, [
                    'bag_pounds' => ['required', 'numeric', 'max:69'],
                ]);

                $bag_pounds = $request->bag_pounds;
            }

            $this->validate($request, [
                'bags' => ['required', 'numeric'],
            ]);

            if($bag_pounds < pound_rate)
            {
                $price_pound = $berating->price / pound_rate;
                $price_bag = $berating->price;

                $equivalentPriceForBag = $request->bags * $price_bag;
                $equivalentPriceForPound = $request->bag_pounds * $price_pound;

                $total = $equivalentPriceForBag + $equivalentPriceForPound;

                $totalPrice = number_format((float)$total, 0, '.', '');

                return back()->with([
                    'previewPrice' => 'success',
                    'message' => $this->replaceCharsInNumber($totalPrice, '0')
                ]);
            } else {
                return back()->with([
                    'type' => 'danger',
                    'message' => 'Pound should not be greater or equal to '.pound_rate
                ]);
            }
        } 

        if($request->weight == 'pound')
        {
            $this->validate($request, [
                'pounds' => ['required', 'numeric']
            ]);

            $equivalentPriceForPound = $request->pounds * $berating->unit_price;

            $total = $equivalentPriceForPound;

            $totalPrice = number_format((float)$total, 0, '.', '');

            return back()->with([
                'previewPrice' => 'success',
                'message' => $this->replaceCharsInNumber($totalPrice, '0')
            ]);
        } 

        return back()->with([
            'type' => 'danger',
            'message' => 'Please select weight type.'
        ]);
    }

    public function payment_receipt_tin_kg_update($id, Request $request)
    {
        $finder = Crypt::decrypt($id);

        $tinPayment = PaymentReceiptTin::find($finder);
        
        $benchmark = Benchmark::latest()->first();

        if(!$benchmark)
        {
            return back()->with([
                'type' => 'danger',
                'message' => 'Admin yet to add benchmark, try again later.'
            ]); 
        }

        $resBench = [
            'amount' => $benchmark->amount,
            'benchmark_value' => $benchmark->benchmark_value,
        ];

        $benchMark = json_encode($resBench);

        if($request->save) 
        {
            $this->validate($request, [
                'supplier' => ['required', 'string', 'max:255'],
                'grade' => ['required', 'numeric'],
                'manager' => ['required', 'numeric'],
                'date_of_purchase' => ['required', 'date'],
                'receipt_no' => 'required|string',
            ]);

            $berating = BeratingCalculation::find($request->grade);

            if(!$berating)
            {
                return back()->with([
                    'type' => 'danger',
                    'message' => 'Admin yet to add this berating value, try again later.'
                ]); 
            }

            $response = [
                'grade' => $berating->grade,
                'price' => $berating->price,
                'unit_price' => $berating->unit_price
            ];

            $berate = json_encode($response);

            $manager = User::find($request->manager);

            if(!$manager)
            {
                return back()->with([
                    'type' => 'danger',
                    'message' => 'Manager not found in our database.'
                ]); 
            }

            if($request->kgweight == 'bag')
            {
                if($request->bag_kg == null)
                {
                    $bag_kgs = 0;
                } else {
                    $this->validate($request, [
                        'bag_kg' => ['required', 'numeric', 'max:49'],
                    ]);

                    $bag_kgs = $request->bag_kg;
                }

                $this->validate($request, [
                    'bags' => ['required', 'numeric'],
                    'percentage' => ['required', 'numeric', 'min:25'],
                ]);

                $analysis = AnalysisCalculation::get();

                foreach($analysis as $analyses)
                {
                    if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                    {
                        $myDollarRate = $analyses->dollar_rate;
                        $myExchangeRate = $analyses->exchange_rate;
                    }
                }

                $dollarRate = $myDollarRate ?? 0;
                $exchangeRate = $myExchangeRate ?? 0;

                if($dollarRate == 0 && $exchangeRate == 0)
                {
                    return back()->with([
                        'type' => 'danger',
                        'message' => 'Percentage Analysis entered not found in our database, try again.'
                    ]);
                }

                $res = [
                    'dollar_rate' => $dollarRate,
                    'exchange_rate' => $exchangeRate,
                ];
    
                $analysisRate = json_encode($res);

                if($bag_kgs < rate)
                {
                    $totalKG = $request->bags * rate + $request->bag_kg;
                
                    $sub = $benchmark->benchmark_value * $request->percentage;

                    $total = $sub * $totalKG;

                    $totalPrice = floor($total);
                    
                    if (request()->hasFile('receipt_image')) 
                    {
                        $this->validate($request, [
                            'receipt_image' => 'required|mimes:jpeg,png,jpg'
                        ]);

                        $filename = uniqid(5).'-'.request()->receipt_image->getClientOriginalName();
                        if($tinPayment->receipt_image) {
                            Storage::delete(str_replace("storage", "public", $tinPayment->receipt_image));
                        }
                        request()->receipt_image->storeAs('payment_analysis', $filename, 'public');

                        $tinPayment->update([
                            'type' => $request->type,
                            'supplier' => $request->supplier,
                            'staff' => $manager->id,
                            'grade' => $request->grade,
                            'bag' => $request->bags,
                            'kg' => $bag_kgs,
                            'total_in_kg' => $totalKG,
                            'berating_rate_list' => $berate,
                            'percentage_analysis' => $request->percentage,
                            'analysis_rate_list' => $analysisRate,
                            'benchmark' => $benchMark,
                            'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                            'date_of_purchase' => $request->date_of_purchase,
                            'receipt_no' => $request->receipt_no,
                            'receipt_image' => '/storage/payment_analysis/'.$filename
                        ]);
    
                    } else {
                        $tinPayment->update([
                            'type' => $request->type,
                            'supplier' => $request->supplier,
                            'staff' => $manager->id,
                            'grade' => $request->grade,
                            'bag' => $request->bags,
                            'kg' => $bag_kgs,
                            'total_in_kg' => $totalKG,
                            'berating_rate_list' => $berate,
                            'percentage_analysis' => $request->percentage,
                            'analysis_rate_list' => $analysisRate,
                            'benchmark' => $benchMark,
                            'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                            'date_of_purchase' => $request->date_of_purchase,
                            'receipt_no' => $request->receipt_no,
                        ]);
                    }

                   
                    $transaction = Transaction::where('accountant_process_id', $tinPayment->id)->first();

                    if($transaction)
                    {
                        $transaction->update([
                            'amount' => $tinPayment->price
                        ]);
                    }

                    Notification::create([
                        'to' => $tinPayment->user_id,
                        'admin_id' => Auth::user()->id,
                        'title' => config('app.name'),
                        'body' => 'Admin has updated a payment receipt, with Receipt No:'.$tinPayment->receipt_no
                    ]);
    
                    return back()->with([
                        'alertType' => 'success',
                        'back' => route('admin.payment.receipt.tin.view', 'kg'),
                        'message' => 'Payment receipt updated successfully.'
                    ]);
                } else {
                    return back()->with([
                        'type' => 'danger',
                        'message' => 'kg should not be greater or equal to '.rate
                    ]);
                }
            } 

            if($request->kgweight == 'kg')
            {
                $this->validate($request, [
                    'kg' => ['required', 'numeric']
                ]);
    
                $analysis = AnalysisCalculation::get();

                foreach($analysis as $analyses)
                {
                    if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                    {
                        $myDollarRate = $analyses->dollar_rate;
                        $myExchangeRate = $analyses->exchange_rate;
                    }
                }

                $dollarRate = $myDollarRate ?? 0;
                $exchangeRate = $myExchangeRate ?? 0;

                if($dollarRate == 0 && $exchangeRate == 0)
                {
                    return back()->with([
                        'type' => 'danger',
                        'message' => 'Percentage Analysis entered not found in our database, try again.'
                    ]);
                }

                $res = [
                    'dollar_rate' => $dollarRate,
                    'exchange_rate' => $exchangeRate,
                ];
    
                $analysisRate = json_encode($res);

                $sub = $benchmark->benchmark_value * $request->percentage;

                $total = $sub * $request->kg;

                $totalPrice = floor($total);

                if (request()->hasFile('receipt_image')) 
                {
                    $this->validate($request, [
                        'receipt_image' => 'required|mimes:jpeg,png,jpg'
                    ]);

                    $filename = uniqid(5).'-'.request()->receipt_image->getClientOriginalName();
                    if($tinPayment->receipt_image) {
                        Storage::delete(str_replace("storage", "public", $tinPayment->receipt_image));
                    }
                    request()->receipt_image->storeAs('payment_analysis', $filename, 'public');

                    $tinPayment->update([
                        'type' => $request->type,
                        'supplier' => $request->supplier,
                        'staff' => $manager->id,
                        'grade' => $request->grade,
                        'bag' => 0,
                        'kg' => $request->kg,
                        'total_in_kg' => $request->kg,
                        'berating_rate_list' => $berate,
                        'percentage_analysis' => $request->percentage,
                        'analysis_rate_list' => $analysisRate,
                        'benchmark' => $benchMark,
                        'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                        'date_of_purchase' => $request->date_of_purchase,
                        'receipt_no' => $request->receipt_no,
                        'receipt_image' => '/storage/payment_analysis/'.$filename
                    ]);
                } else {
                    $tinPayment->update([
                        'type' => $request->type,
                        'supplier' => $request->supplier,
                        'staff' => $manager->id,
                        'grade' => $request->grade,
                        'bag' => 0,
                        'kg' => $request->kg,
                        'total_in_kg' => $request->kg,
                        'berating_rate_list' => $berate,
                        'percentage_analysis' => $request->percentage,
                        'analysis_rate_list' => $analysisRate,
                        'benchmark' => $benchMark,
                        'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                        'date_of_purchase' => $request->date_of_purchase,
                        'receipt_no' => $request->receipt_no,
                    ]);
                }
        
                $transaction = Transaction::where('accountant_process_id', $tinPayment->id)->first();

                if($transaction)
                {
                    $transaction->update([
                        'amount' => $tinPayment->price
                    ]);
                }

                Notification::create([
                    'to' => $tinPayment->user_id,
                    'admin_id' => Auth::user()->id,
                    'title' => config('app.name'),
                    'body' => 'Admin has updated a payment receipt, with Receipt No:'.$tinPayment->receipt_no
                ]);

                return back()->with([
                    'alertType' => 'success',
                    'back' => route('admin.payment.receipt.tin.view', 'kg'),
                    'message' => 'Payment Receipt updated successfully'
                ]);
            } 

            return back()->with([
                'type' => 'danger',
                'message' => 'Please select weight type.'
            ]);
        }

        $this->validate($request, [
            'grade' => ['required', 'numeric'],
        ]);

        $berating = BeratingCalculation::find($request->grade);

        if(!$berating)
        {
            return back()->with([
                'type' => 'danger',
                'message' => 'Admin yet to add this berating value, try again later.'
            ]); 
        }
       
        if($request->kgweight == 'bag')
        {
            if($request->bag_kg == null)
            {
                $bag_kgs = 0;
            } else {
                $this->validate($request, [
                    'bag_kg' => ['required', 'numeric', 'max:49'],
                ]);

                $bag_kgs = $request->bag_kg;
            }

            $this->validate($request, [
                'bags' => ['required', 'numeric'],
                'percentage' => ['required', 'numeric', 'min:25'],
            ]);

            $analysis = AnalysisCalculation::get();

            foreach($analysis as $analyses)
            {
                if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                {
                    $myDollarRate = $analyses->dollar_rate;
                    $myExchangeRate = $analyses->exchange_rate;
                }
            }

            $dollarRate = $myDollarRate ?? 0;
            $exchangeRate = $myExchangeRate ?? 0;

            if($dollarRate == 0 && $exchangeRate == 0)
            {
                return back()->with([
                    'type' => 'danger',
                    'message' => 'Percentage Analysis entered not found in our database, try again.'
                ]);
            }

            if($bag_kgs < rate)
            {
                $totalKG = $request->bags * rate + $request->bag_kg;
                
                $sub = $benchmark->benchmark_value * $request->percentage;

                $total = $sub * $totalKG;

                $totalPrice = floor($total);

                return back()->with([
                    'previewPrice' => 'success',
                    'message' => $this->replaceCharsInNumber($totalPrice, '0')
                ]);
            } else {
                return back()->with([
                    'type' => 'danger',
                    'message' => 'kg should not be greater or equal to '.rate
                ]);
            }
        } 

        if($request->kgweight == 'kg')
        {
            $this->validate($request, [
                'kg' => ['required', 'numeric']
            ]);

            $analysis = AnalysisCalculation::get();

            foreach($analysis as $analyses)
            {
                if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                {
                    $myDollarRate = $analyses->dollar_rate;
                    $myExchangeRate = $analyses->exchange_rate;
                }
            }

            $dollarRate = $myDollarRate ?? 0;
            $exchangeRate = $myExchangeRate ?? 0;

            if($dollarRate == 0 && $exchangeRate == 0)
            {
                return back()->with([
                    'type' => 'danger',
                    'message' => 'Percentage Analysis entered not found in our database, try again.'
                ]);
            }

            $sub = $benchmark->benchmark_value * $request->percentage;

            $total = $sub * $request->kg;

            $totalPrice = floor($total);

            return back()->with([
                'previewPrice' => 'success',
                'message' => $this->replaceCharsInNumber($totalPrice, '0')
            ]);
        } 

        return back()->with([
            'type' => 'danger',
            'message' => 'Please select weight type.'
        ]);
    }

    public function payment_receipt_tin_delete($id, $type)
    {
        $finder = Crypt::decrypt($id);

        $tinPayment = PaymentReceiptTin::find($finder);

        $transaction = Transaction::where('accountant_process_id', $tinPayment->id)->first();

        if($transaction)
        {
            $transaction->delete();
        }

        if($tinPayment->receipt_image) {
            Storage::delete(str_replace("storage", "public", $tinPayment->receipt_image));
        }

        $tinPayment->delete();

        return redirect()->route('admin.payment.receipt.tin.view', $type)->with([
            'alertType' => 'success',
            'message' => 'Payment receipt deleted successfully!'
        ]);
    }

    public function payment_receipt_columbite_view($id, Request $request)
    {
        if($id == 'kg')
        {
            if($request->start_date == null && $request->end_date == null)
            {
                $columbitePaymentReceiptKg = PaymentReceiptColumbite::latest()->where('type', 'kg')->get();
                $columbitePaymentReceiptPound = PaymentReceiptColumbite::latest()->where('type', 'pound')->get();
            } else {
                $columbitePaymentReceiptKg = PaymentReceiptColumbite::latest()->where('type', 'kg')->whereBetween('date_of_purchase', [$request->start_date, $request->end_date])->get();
                $columbitePaymentReceiptPound = PaymentReceiptColumbite::latest()->where('type', 'pound')->get();
            }

            $active_tab = $id;

            if($active_tab == 'pound') {
                return view('admin.payment-receipt.view_columbite', [
                    'columbitePaymentReceiptKg' => $columbitePaymentReceiptKg,
                    'columbitePaymentReceiptPound' => $columbitePaymentReceiptPound,
                    'active_tab' => $active_tab,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date
                ]);
            } elseif($active_tab == 'kg') {
                return view('admin.payment-receipt.view_columbite', [
                    'columbitePaymentReceiptKg' => $columbitePaymentReceiptKg,
                    'columbitePaymentReceiptPound' => $columbitePaymentReceiptPound,
                    'active_tab' => $active_tab,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date
                ]);
            } else {
                $active_tab == 'kg';
                return view('admin.payment-receipt.view_columbite', [
                    'columbitePaymentReceiptKg' => $columbitePaymentReceiptKg,
                    'columbitePaymentReceiptPound' => $columbitePaymentReceiptPound,
                    'active_tab' => $active_tab,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date
                ]);
            }
        }

        if($id == 'pound')
        {
            if($request->start_date == null && $request->end_date == null)
            {
                $columbitePaymentReceiptKg = PaymentReceiptColumbite::latest()->where('type', 'kg')->get();
                $columbitePaymentReceiptPound = PaymentReceiptColumbite::latest()->where('type', 'pound')->get();
            } else {
                $columbitePaymentReceiptPound = PaymentReceiptColumbite::latest()->where('type', 'pound')->whereBetween('date_of_purchase', [$request->start_date, $request->end_date])->get();
                $columbitePaymentReceiptKg = PaymentReceiptColumbite::latest()->where('type', 'kg')->get();
            }

            $active_tab = $id;

            if($active_tab == 'pound') {
                return view('admin.payment-receipt.view_columbite', [
                    'columbitePaymentReceiptKg' => $columbitePaymentReceiptKg,
                    'columbitePaymentReceiptPound' => $columbitePaymentReceiptPound,
                    'active_tab' => $active_tab,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date
                ]);
            } elseif($active_tab == 'kg') {
                return view('admin.payment-receipt.view_columbite', [
                    'columbitePaymentReceiptKg' => $columbitePaymentReceiptKg,
                    'columbitePaymentReceiptPound' => $columbitePaymentReceiptPound,
                    'active_tab' => $active_tab,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date
                ]);
            } else {
                $active_tab == 'kg';
                return view('admin.payment-receipt.view_columbite', [
                    'columbitePaymentReceiptKg' => $columbitePaymentReceiptKg,
                    'columbitePaymentReceiptPound' => $columbitePaymentReceiptPound,
                    'active_tab' => $active_tab,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date
                ]);
            }
        }
    }

    public function payment_receipt_columbite_add($id)
    {
        $active_tab = $id;

        if($active_tab == 'pound') {
            return view ('admin.payment-receipt.add_columbite', compact('active_tab'));
        } elseif($active_tab == 'kg') {
            return view ('admin.payment-receipt.add_columbite', compact('active_tab'));
        } else {
            $active_tab == 'kg';
            return view ('admin.payment-receipt.add_columbite', compact('active_tab'));
        }
    }

    public function payment_receipt_columbite_pound_post(Request $request)
    {
        if($request->save) 
        {
            $this->validate($request, [
                'assist_manager' => ['required', 'string', 'max:255'],
                'supplier' => ['required', 'string', 'max:255'],
                'grade' => ['required', 'numeric'],
                'manager' => ['required', 'numeric'],
                'date_of_purchase' => ['required', 'date'],
                'receipt_no' => 'required|string',
                'receipt_image' => 'required|mimes:jpeg,png,jpg'
            ]);

            $berating = BeratingCalculation::find($request->grade);
    
            if(!$berating)
            {
                return redirect()->route('admin.payment.receipt.columbite.add', 'pound')->with([
                    'type' => 'danger',
                    'message' => 'Admin yet to add this berating value, try again later.'
                ]); 
            }

            $response = [
                'grade' => $berating->grade,
                'price' => $berating->price,
                'unit_price' => $berating->unit_price
            ];

            $berate = json_encode($response);

            $manager = User::find($request->manager);

            if(!$manager)
            {
                return redirect()->route('admin.payment.receipt.columbite.add', 'pound')->with([
                    'type' => 'danger',
                    'message' => 'Manager not found in our database.'
                ]); 
            }

            $assist_manager = User::find($request->assist_manager);

            if(!$assist_manager)
            {
                return redirect()->route('admin.payment.receipt.columbite.add', 'pound')->with([
                    'type' => 'danger',
                    'message' => 'Assistant Manager not found in our database.'
                ]); 
            }

            if($request->poundweight == 'bag')
            {
                if($request->bag_pound == null)
                {
                    $bag_pounds = 0;
                } else {
                    $this->validate($request, [
                        'bag_pound' => ['required', 'numeric', 'max:79'],
                    ]);

                    $bag_pounds = $request->bag_pound;
                }

                $this->validate($request, [
                    'bags' => ['required', 'numeric'],
                    'percentage' => ['required', 'numeric', 'min:25'],
                ]);

                $analysis = AnalysisCalculation::get();

                foreach($analysis as $analyses)
                {
                    if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                    {
                        $myDollarRate = $analyses->dollar_rate;
                        $myExchangeRate = $analyses->exchange_rate;
                    }
                }

                $dollarRate = $myDollarRate ?? 0;
                $exchangeRate = $myExchangeRate ?? 0;

                if($dollarRate == 0 && $exchangeRate == 0)
                {
                    return redirect()->route('admin.payment.receipt.columbite.add', 'pound')->with([
                        'type' => 'danger',
                        'message' => 'Percentage Analysis entered not found in our database, try again.'
                    ]);
                }

                $res = [
                    'dollar_rate' => $dollarRate,
                    'exchange_rate' => $exchangeRate,
                ];
    
                $analysisRate = json_encode($res);

                if($bag_pounds < columbite_rate)
                {
                    $per = $request->percentage / 100;

                    $rateCalculation = $dollarRate * $exchangeRate;

                    $subTotal = $per * $rateCalculation;

                    $subPrice = $request->bags * columbite_rate + $request->bag_pound;
                    
                    $total = floor($subTotal) * $subPrice;

                    $totalPrice = number_format((float)$total, 0, '.', '');
                    
                    $filename = uniqid(5).'-'.request()->receipt_image->getClientOriginalName();
                    request()->receipt_image->storeAs('payment_analysis', $filename, 'public');

                    $columbitePayment = PaymentReceiptColumbite::create([
                        'type' => $request->type,
                        'user_id' => $assist_manager->id,
                        'supplier' => $request->supplier,
                        'staff' => $manager->id,
                        'grade' => $request->grade,
                        'bag' => $request->bags,
                        'pound' => $bag_pounds,
                        'total_in_pound' => $subPrice,
                        'berating_rate_list' => $berate,
                        'percentage_analysis' => $request->percentage,
                        'analysis_rate_list' => $analysisRate,
                        'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                        'date_of_purchase' => $request->date_of_purchase,
                        'receipt_no' => $request->receipt_no,
                        'receipt_image' => '/storage/payment_analysis/'.$filename
                    ]);
            
                    Transaction::create([
                        'user_id' => $assist_manager->id,
                        'accountant_process_id' => $columbitePayment->id,
                        'amount' => $columbitePayment->price,
                        'reference' => config('app.name'),
                        'status' => 'Payment Receipt'
                    ]);

                    Notification::create([
                        'to' => $assist_manager->id,
                        'admin_id' => Auth::user()->id,
                        'title' => config('app.name'),
                        'body' => 'Admin has added a payment receipt, with Receipt No:'.$columbitePayment->receipt_no.' on your behalf.'
                    ]);

                    return redirect()->route('admin.payment.receipt.columbite.add', 'pound')->with([
                        'alertType' => 'success',
                        'back' => route('admin.payment.receipt.columbite.view', 'pound'),
                        'message' => 'Payment Receipt created successfully'
                    ]);
                } else {
                    return redirect()->route('admin.payment.receipt.columbite.add', 'pound')->with([
                        'type' => 'danger',
                        'message' => 'kg should not be greater or equal to '.columbite_rate
                    ]);
                }
            } 

            if($request->poundweight == 'pound')
            {
                $this->validate($request, [
                    'pounds' => ['required', 'numeric']
                ]);
    
                $analysis = AnalysisCalculation::get();
    
                foreach($analysis as $analyses)
                {
                    if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                    {
                        $myDollarRate = $analyses->dollar_rate;
                        $myExchangeRate = $analyses->exchange_rate;
                    }
                }

                $dollarRate = $myDollarRate ?? 0;
                $exchangeRate = $myExchangeRate ?? 0;

                if($dollarRate == 0 && $exchangeRate == 0)
                {
                    return redirect()->route('admin.payment.receipt.columbite.add', 'pound')->with([
                        'type' => 'danger',
                        'message' => 'Percentage Analysis entered not found in our database, try again.'
                    ]);
                }
    
                $res = [
                    'dollar_rate' => $dollarRate,
                    'exchange_rate' => $exchangeRate,
                ];
    
                $analysisRate = json_encode($res);

                $per = $request->percentage / 100;
    
                $rateCalculation = $dollarRate * $exchangeRate;
    
                $subTotal = $per * $rateCalculation;
    
                $total = floor($subTotal) * $request->pounds;
    
                $totalPrice = number_format((float)$total, 0, '.', '');

                $filename = uniqid(5).'-'.request()->receipt_image->getClientOriginalName();
                request()->receipt_image->storeAs('payment_analysis', $filename, 'public');

                $columbitePayment = PaymentReceiptColumbite::create([
                    'type' => $request->type,
                    'user_id' => $assist_manager->id,
                    'supplier' => $request->supplier,
                    'staff' => $manager->id,
                    'grade' => $request->grade,
                    'pound' => $request->pounds,
                    'total_in_pound' => $request->pounds,
                    'berating_rate_list' => $berate,
                    'percentage_analysis' => $request->percentage,
                    'analysis_rate_list' => $analysisRate,
                    'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                    'date_of_purchase' => $request->date_of_purchase,
                    'receipt_no' => $request->receipt_no,
                    'receipt_image' => '/storage/payment_analysis/'.$filename
                ]);
        
                Transaction::create([
                    'user_id' => $assist_manager->id,
                    'accountant_process_id' => $columbitePayment->id,
                    'amount' => $columbitePayment->price,
                    'reference' => config('app.name'),
                    'status' => 'Payment Receipt'
                ]);

                Notification::create([
                    'to' => $assist_manager->id,
                    'admin_id' => Auth::user()->id,
                    'title' => config('app.name'),
                    'body' => 'Admin has added a payment receipt, with Receipt No:'.$columbitePayment->receipt_no.' on your behalf.'
                ]);


                return redirect()->route('admin.payment.receipt.columbite.add', 'pound')->with([
                    'alertType' => 'success',
                    'back' => route('admin.payment.receipt.columbite.view', 'pound'),
                    'message' => 'Payment Receipt created successfully'
                ]);
            } 

            return redirect()->route('admin.payment.receipt.columbite.add', 'pound')->with([
                'type' => 'danger',
                'message' => 'Please select weight type.'
            ]);
        }

        $this->validate($request, [
            'grade' => ['required', 'numeric'],
        ]);

        $berating = BeratingCalculation::find($request->grade);

        if(!$berating)
        {
            return redirect()->route('admin.payment.receipt.columbite.add', 'pound')->with([
                'type' => 'danger',
                'message' => 'Admin yet to add this berating value, try again later.'
            ]); 
        }
       
        if($request->poundweight == 'bag')
        {
            if($request->bag_pound == null)
            {
                $bag_pounds = 0;
            } else {
                $this->validate($request, [
                    'bag_pound' => ['required', 'numeric', 'max:79'],
                ]);

                $bag_pounds = $request->bag_pound;
            }

            $this->validate($request, [
                'bags' => ['required', 'numeric'],
                'percentage' => ['required', 'numeric', 'min:25'],
            ]);

            $analysis = AnalysisCalculation::get();

            foreach($analysis as $analyses)
            {
                if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                {
                    $myDollarRate = $analyses->dollar_rate;
                    $myExchangeRate = $analyses->exchange_rate;
                }
            }

            $dollarRate = $myDollarRate ?? 0;
            $exchangeRate = $myExchangeRate ?? 0;

            if($dollarRate == 0 && $exchangeRate == 0)
            {
                return redirect()->route('admin.payment.receipt.columbite.add', 'pound')->with([
                    'type' => 'danger',
                    'message' => 'Percentage Analysis entered not found in our database, try again.'
                ]);
            }

            if($bag_pounds < columbite_rate)
            {
                $per = $request->percentage / 100;

                $rateCalculation = $dollarRate * $exchangeRate;

                $subTotal = $per * $rateCalculation;

                $subPrice = $request->bags * columbite_rate + $request->bag_pound;
                
                $total = floor($subTotal) * $subPrice;

                $totalPrice = number_format((float)$total, 0, '.', '');

                return redirect()->route('admin.payment.receipt.columbite.add', 'pound')->with([
                    'previewPrice' => 'success',
                    'message' => $this->replaceCharsInNumber($totalPrice, '0')
                ]);
            } else {
                return redirect()->route('admin.payment.receipt.columbite.add', 'pound')->with([
                    'type' => 'danger',
                    'message' => 'pound should not be greater or equal to '.columbite_rate
                ]);
            }
            
        } 

        if($request->poundweight == 'pound')
        {
            $this->validate($request, [
                'pounds' => ['required', 'numeric']
            ]);

            $analysis = AnalysisCalculation::get();

            foreach($analysis as $analyses)
            {
                if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                {
                    $myDollarRate = $analyses->dollar_rate;
                    $myExchangeRate = $analyses->exchange_rate;
                }
            }

            $dollarRate = $myDollarRate ?? 0;
            $exchangeRate = $myExchangeRate ?? 0;

            if($dollarRate == 0 && $exchangeRate == 0)
            {
                return redirect()->route('admin.payment.receipt.columbite.add', 'pound')->with([
                    'type' => 'danger',
                    'message' => 'Percentage Analysis entered not found in our database, try again.'
                ]);
            }

            $per = $request->percentage / 100;

            $rateCalculation = $dollarRate * $exchangeRate;

            $subTotal = $per * $rateCalculation;

            $total = floor($subTotal) * $request->pounds;

            $totalPrice = number_format((float)$total, 0, '.', '');

            return redirect()->route('admin.payment.receipt.columbite.add', 'pound')->with([
                'previewPrice' => 'success',
                'message' => $this->replaceCharsInNumber($totalPrice, '0')
            ]);
        } 

        return redirect()->route('admin.payment.receipt.columbite.add', 'pound')->with([
            'type' => 'danger',
            'message' => 'Please select weight type.'
        ]);
    }

    public function payment_receipt_columbite_kg_post(Request $request)
    {
        if($request->save) 
        {
            $this->validate($request, [
                'assist_manager' => ['required', 'string', 'max:255'],
                'supplier' => ['required', 'string', 'max:255'],
                'grade' => ['required', 'numeric'],
                'manager' => ['required', 'numeric'],
                'date_of_purchase' => ['required', 'date'],
                'receipt_no' => 'required|string',
                'receipt_image' => 'required|mimes:jpeg,png,jpg'
            ]);

            $berating = BeratingCalculation::find($request->grade);

            if(!$berating)
            {
                return redirect()->route('admin.payment.receipt.columbite.add', 'kg')->with([
                    'type' => 'danger',
                    'message' => 'Admin yet to add this berating value, try again later.'
                ]); 
            }

            $response = [
                'grade' => $berating->grade,
                'price' => $berating->price,
                'unit_price' => $berating->unit_price
            ];

            $berate = json_encode($response);

            $manager = User::find($request->manager);

            if(!$manager)
            {
                return redirect()->route('admin.payment.receipt.columbite.add', 'kg')->with([
                    'type' => 'danger',
                    'message' => 'Manager not found in our database.'
                ]); 
            }

            $assist_manager = User::find($request->assist_manager);

            if(!$assist_manager)
            {
                return redirect()->route('admin.payment.receipt.columbite.add', 'kg')->with([
                    'type' => 'danger',
                    'message' => 'Assistant Manager not found in our database.'
                ]); 
            }

            if($request->kgweight == 'bag')
            {
                if($request->bag_kg == null)
                {
                    $bag_kgs = 0;
                } else {
                    $this->validate($request, [
                        'bag_kg' => ['required', 'numeric', 'max:49'],
                    ]);

                    $bag_kgs = $request->bag_kg;
                }

                $this->validate($request, [
                    'bags' => ['required', 'numeric'],
                    'percentage' => ['required', 'numeric', 'min:25'],
                ]);

                $analysis = AnalysisCalculation::get();

                foreach($analysis as $analyses)
                {
                    if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                    {
                        $myDollarRate = $analyses->dollar_rate;
                        $myExchangeRate = $analyses->exchange_rate;
                    }
                }

                $dollarRate = $myDollarRate ?? 0;
                $exchangeRate = $myExchangeRate ?? 0;

                if($dollarRate == 0 && $exchangeRate == 0)
                {
                    return redirect()->route('admin.payment.receipt.columbite.add', 'kg')->with([
                        'type' => 'danger',
                        'message' => 'Percentage Analysis entered not found in our database, try again.'
                    ]);
                }

                $res = [
                    'dollar_rate' => $dollarRate,
                    'exchange_rate' => $exchangeRate,
                ];
    
                $analysisRate = json_encode($res);

                if($bag_kgs < rate)
                {
                    $per = $request->percentage / 100;

                    $rateCalculation = $dollarRate * $exchangeRate;

                    $subTotal = $per * $rateCalculation * fixed_rate;

                    $subPrice = $request->bags * rate + $request->bag_kg;
                    
                    $total = floor($subTotal) * $subPrice;

                    $totalPrice = number_format((float)$total, 0, '.', '');
                    
                    $filename = uniqid(5).'-'.request()->receipt_image->getClientOriginalName();
                    request()->receipt_image->storeAs('payment_analysis', $filename, 'public');

                    $columbitePayment = PaymentReceiptColumbite::create([
                        'type' => $request->type,
                        'user_id' => $assist_manager->id,
                        'supplier' => $request->supplier,
                        'staff' => $manager->id,
                        'grade' => $request->grade,
                        'bag' => $request->bags,
                        'kg' => $bag_kgs,
                        'total_in_kg' => $subPrice,
                        'berating_rate_list' => $berate,
                        'percentage_analysis' => $request->percentage,
                        'analysis_rate_list' => $analysisRate,
                        'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                        'date_of_purchase' => $request->date_of_purchase,
                        'receipt_no' => $request->receipt_no,
                        'receipt_image' => '/storage/payment_analysis/'.$filename
                    ]);

                    Transaction::create([
                        'user_id' => $assist_manager->id,
                        'accountant_process_id' => $columbitePayment->id,
                        'amount' => $columbitePayment->price,
                        'reference' => config('app.name'),
                        'status' => 'Payment Receipt'
                    ]);

                    Notification::create([
                        'to' => $assist_manager->id,
                        'admin_id' => Auth::user()->id,
                        'title' => config('app.name'),
                        'body' => 'Admin has added a payment receipt, with Receipt No:'.$columbitePayment->receipt_no.' on your behalf.'
                    ]);
    
                    return redirect()->route('admin.payment.receipt.columbite.add', 'kg')->with([
                        'alertType' => 'success',
                        'back' => route('admin.payment.receipt.columbite.view', 'kg'),
                        'message' => 'Payment Receipt created successfully.'
                    ]);
                } else {
                    return redirect()->route('admin.payment.receipt.columbite.add', 'kg')->with([
                        'type' => 'danger',
                        'message' => 'kg should not be greater or equal to '.rate
                    ]);
                }
            } 

            if($request->kgweight == 'kg')
            {
                $this->validate($request, [
                    'kg' => ['required', 'numeric']
                ]);
    
                $analysis = AnalysisCalculation::get();
    
                foreach($analysis as $analyses)
                {
                    if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                    {
                        $myDollarRate = $analyses->dollar_rate;
                        $myExchangeRate = $analyses->exchange_rate;
                    }
                }

                $dollarRate = $myDollarRate ?? 0;
                $exchangeRate = $myExchangeRate ?? 0;

                if($dollarRate == 0 && $exchangeRate == 0)
                {
                    return redirect()->route('admin.payment.receipt.columbite.add', 'kg')->with([
                        'type' => 'danger',
                        'message' => 'Percentage Analysis entered not found in our database, try again.'
                    ]);
                }

                $res = [
                    'dollar_rate' => $dollarRate,
                    'exchange_rate' => $exchangeRate,
                ];
    
                $analysisRate = json_encode($res);

                $per = $request->percentage / 100;
    
                $rateCalculation = $dollarRate * $exchangeRate;
    
                $subTotal = $per * $rateCalculation * fixed_rate;
    
                $total = floor($subTotal) * $request->kg;
    
                $totalPrice = number_format((float)$total, 0, '.', '');

                $filename = uniqid(5).'-'.request()->receipt_image->getClientOriginalName();
                request()->receipt_image->storeAs('payment_analysis', $filename, 'public');

                $columbitePayment = PaymentReceiptColumbite::create([
                    'type' => $request->type,
                    'user_id' => $assist_manager->id,
                    'supplier' => $request->supplier,
                    'staff' => $manager->id,
                    'grade' => $request->grade,
                    'kg' => $request->kg,
                    'total_in_kg' => $request->kg,
                    'berating_rate_list' => $berate,
                    'percentage_analysis' => $request->percentage,
                    'analysis_rate_list' => $analysisRate,
                    'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                    'date_of_purchase' => $request->date_of_purchase,
                    'receipt_no' => $request->receipt_no,
                    'receipt_image' => '/storage/payment_analysis/'.$filename
                ]);
        
                Transaction::create([
                    'user_id' => $assist_manager->id,
                    'accountant_process_id' => $columbitePayment->id,
                    'amount' => $columbitePayment->price,
                    'reference' => config('app.name'),
                    'status' => 'Payment Receipt'
                ]);

                Notification::create([
                    'to' => $assist_manager->id,
                    'admin_id' => Auth::user()->id,
                    'title' => config('app.name'),
                    'body' => 'Admin has added a payment receipt, with Receipt No:'.$columbitePayment->receipt_no.' on your behalf.'
                ]);

                return redirect()->route('admin.payment.receipt.columbite.add', 'kg')->with([
                    'alertType' => 'success',
                    'back' => route('admin.payment.receipt.columbite.view', 'kg'),
                    'message' => 'Payment Receipt created successfully.'
                ]);
            } 

            return redirect()->route('admin.payment.receipt.columbite.add', 'kg')->with([
                'type' => 'danger',
                'message' => 'Please select weight type.'
            ]);
        }

        $this->validate($request, [
            'grade' => ['required', 'numeric'],
        ]);

        $berating = BeratingCalculation::find($request->grade);

        if(!$berating)
        {
            return redirect()->route('admin.payment.receipt.columbite.add', 'kg')->with([
                'type' => 'danger',
                'message' => 'Admin yet to add this berating value, try again later.'
            ]); 
        }
       
        if($request->kgweight == 'bag')
        {
            if($request->bag_kg == null)
            {
                $bag_kgs = 0;
            } else {
                $this->validate($request, [
                    'bag_kg' => ['required', 'numeric', 'max:49'],
                ]);

                $bag_kgs = $request->bag_kg;
            }

            $this->validate($request, [
                'bags' => ['required', 'numeric'],
                'percentage' => ['required', 'numeric', 'min:25'],
            ]);

            $analysis = AnalysisCalculation::get();

            foreach($analysis as $analyses)
            {
                if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                {
                    $myDollarRate = $analyses->dollar_rate;
                    $myExchangeRate = $analyses->exchange_rate;
                }
            }

            $dollarRate = $myDollarRate ?? 0;
            $exchangeRate = $myExchangeRate ?? 0;

            if($dollarRate == 0 && $exchangeRate == 0)
            {
                return redirect()->route('admin.payment.receipt.columbite.add', 'kg')->with([
                    'type' => 'danger',
                    'message' => 'Percentage Analysis entered not found in our database, try again.'
                ]);
            }

            if($bag_kgs < rate)
            {
                $per = $request->percentage / 100;

                $rateCalculation = $dollarRate * $exchangeRate;

                $subTotal = $per * $rateCalculation * fixed_rate;

                $subPrice = $request->bags * rate + $request->bag_kg;
                
                $total = floor($subTotal) * $subPrice;
                
                $totalPrice = number_format((float)$total, 0, '.', '');

                return redirect()->route('admin.payment.receipt.columbite.add', 'kg')->with([
                    'previewPrice' => 'success',
                    'message' => $this->replaceCharsInNumber($totalPrice, '0')
                ]);
            } else {
                return redirect()->route('admin.payment.receipt.columbite.add', 'kg')->with([
                    'type' => 'danger',
                    'message' => 'kg should not be greater or equal to '.rate
                ]);
            }
            
        } 

        if($request->kgweight == 'kg')
        {
            $this->validate($request, [
                'kg' => ['required', 'numeric']
            ]);

            $analysis = AnalysisCalculation::get();

            foreach($analysis as $analyses)
            {
                if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                {
                    $myDollarRate = $analyses->dollar_rate;
                    $myExchangeRate = $analyses->exchange_rate;
                }
            }

            $dollarRate = $myDollarRate ?? 0;
            $exchangeRate = $myExchangeRate ?? 0;

            if($dollarRate == 0 && $exchangeRate == 0)
            {
                return redirect()->route('admin.payment.receipt.columbite.add', 'kg')->with([
                    'type' => 'danger',
                    'message' => 'Percentage Analysis entered not found in our database, try again.'
                ]);
            }

            $per = $request->percentage / 100;

            $rateCalculation = $dollarRate * $exchangeRate;

            $subTotal = $per * $rateCalculation * fixed_rate;

            $total = floor($subTotal) * $request->kg;

            $totalPrice = number_format((float)$total, 0, '.', '');

            return redirect()->route('admin.payment.receipt.columbite.add', 'kg')->with([
                'previewPrice' => 'success',
                'message' => $this->replaceCharsInNumber($totalPrice, '0')
            ]);
        } 

        return redirect()->route('admin.payment.receipt.columbite.add', 'kg')->with([
            'type' => 'danger',
            'message' => 'Please select weight type.'
        ]);
    }

    public function payment_receipt_columbite_edit($id)
    {
        $finder = Crypt::decrypt($id);

        $columbitePayment = PaymentReceiptColumbite::find($finder);

        return view('admin.payment-receipt.edit_columbite', [
            'columbitePayment' => $columbitePayment
        ]);
    }

    public function payment_receipt_columbite_pound_update($id, Request $request)
    {
        $finder = Crypt::decrypt($id);

        $columbitePayment = PaymentReceiptColumbite::find($finder);

        if($request->save) 
        {
            $this->validate($request, [
                'supplier' => ['required', 'string', 'max:255'],
                'grade' => ['required', 'numeric'],
                'manager' => ['required', 'numeric'],
                'date_of_purchase' => ['required', 'date'],
                'receipt_no' => 'required|string',
            ]);

            $berating = BeratingCalculation::find($request->grade);
    
            if(!$berating)
            {
                return back()->with([
                    'type' => 'danger',
                    'message' => 'Admin yet to add this berating value, try again later.'
                ]); 
            }

            $response = [
                'grade' => $berating->grade,
                'price' => $berating->price,
                'unit_price' => $berating->unit_price
            ];

            $berate = json_encode($response);

            $manager = User::find($request->manager);

            if(!$manager)
            {
                return back()->with([
                    'type' => 'danger',
                    'message' => 'Manager not found in our database.'
                ]); 
            }

            if($request->poundweight == 'bag')
            {
                if($request->bag_pound == null)
                {
                    $bag_pounds = 0;
                } else {
                    $this->validate($request, [
                        'bag_pound' => ['required', 'numeric', 'max:79'],
                    ]);

                    $bag_pounds = $request->bag_pound;
                }

                $this->validate($request, [
                    'bags' => ['required', 'numeric'],
                    'percentage' => ['required', 'numeric', 'min:25'],
                ]);

                $analysis = AnalysisCalculation::get();

                foreach($analysis as $analyses)
                {
                    if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                    {
                        $myDollarRate = $analyses->dollar_rate;
                        $myExchangeRate = $analyses->exchange_rate;
                    }
                }

                $dollarRate = $myDollarRate ?? 0;
                $exchangeRate = $myExchangeRate ?? 0;

                if($dollarRate == 0 && $exchangeRate == 0)
                {
                    return back()->with([
                        'type' => 'danger',
                        'message' => 'Percentage Analysis entered not found in our database, try again.'
                    ]);
                }

                $res = [
                    'dollar_rate' => $dollarRate,
                    'exchange_rate' => $exchangeRate,
                ];
    
                $analysisRate = json_encode($res);

                if($bag_pounds < columbite_rate)
                {
                    $per = $request->percentage / 100;

                    $rateCalculation = $dollarRate * $exchangeRate;

                    $subTotal = $per * $rateCalculation;

                    $subPrice = $request->bags * columbite_rate + $request->bag_pound;
                    
                    $total = floor($subTotal) * $subPrice;

                    $totalPrice = number_format((float)$total, 0, '.', '');
                    
                    if (request()->hasFile('receipt_image')) 
                    {
                        $this->validate($request, [
                            'receipt_image' => 'required|mimes:jpeg,png,jpg'
                        ]);

                        $filename = uniqid(5).'-'.request()->receipt_image->getClientOriginalName();
                        if($columbitePayment->receipt_image) {
                            Storage::delete(str_replace("storage", "public", $columbitePayment->receipt_image));
                        }
                        request()->receipt_image->storeAs('payment_analysis', $filename, 'public');

                        $columbitePayment->update([
                            'type' => $request->type,
                            'supplier' => $request->supplier,
                            'staff' => $manager->id,
                            'grade' => $request->grade,
                            'bag' => $request->bags,
                            'pound' => $bag_pounds,
                            'total_in_pound' => $subPrice,
                            'berating_rate_list' => $berate,
                            'percentage_analysis' => $request->percentage,
                            'analysis_rate_list' => $analysisRate,
                            'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                            'date_of_purchase' => $request->date_of_purchase,
                            'receipt_no' => $request->receipt_no,
                            'receipt_image' => '/storage/payment_analysis/'.$filename
                        ]);
                    } else {
                        $columbitePayment->update([
                            'type' => $request->type,
                            'supplier' => $request->supplier,
                            'staff' => $manager->id,
                            'grade' => $request->grade,
                            'bag' => $request->bags,
                            'pound' => $bag_pounds,
                            'total_in_pound' => $subPrice,
                            'berating_rate_list' => $berate,
                            'percentage_analysis' => $request->percentage,
                            'analysis_rate_list' => $analysisRate,
                            'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                            'date_of_purchase' => $request->date_of_purchase,
                            'receipt_no' => $request->receipt_no,
                        ]);
                    }

                    $transaction = Transaction::where('accountant_process_id', $columbitePayment->id)->first();

                    if($transaction)
                    {
                        $transaction->update([
                            'amount' => $columbitePayment->price
                        ]);
                    }

                    Notification::create([
                        'to' => $columbitePayment->user_id,
                        'admin_id' => Auth::user()->id,
                        'title' => config('app.name'),
                        'body' => 'Admin has updated a payment receipt, with Receipt No:'.$columbitePayment->receipt_no
                    ]);
            
                    return back()->with([
                        'alertType' => 'success',
                        'back' => route('admin.payment.receipt.columbite.view', 'pound'),
                        'message' => 'Payment Receipt updated successfully'
                    ]);
                } else {
                    return back()->with([
                        'type' => 'danger',
                        'message' => 'kg should not be greater or equal to '.columbite_rate
                    ]);
                }
            } 

            if($request->poundweight == 'pound')
            {
                $this->validate($request, [
                    'pounds' => ['required', 'numeric']
                ]);
    
                $analysis = AnalysisCalculation::get();
    
                foreach($analysis as $analyses)
                {
                    if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                    {
                        $myDollarRate = $analyses->dollar_rate;
                        $myExchangeRate = $analyses->exchange_rate;
                    }
                }

                $dollarRate = $myDollarRate ?? 0;
                $exchangeRate = $myExchangeRate ?? 0;

                if($dollarRate == 0 && $exchangeRate == 0)
                {
                    return back()->with([
                        'type' => 'danger',
                        'message' => 'Percentage Analysis entered not found in our database, try again.'
                    ]);
                }

                $res = [
                    'dollar_rate' => $dollarRate,
                    'exchange_rate' => $exchangeRate,
                ];
    
                $analysisRate = json_encode($res);
    
                $per = $request->percentage / 100;
    
                $rateCalculation = $dollarRate * $exchangeRate;
    
                $subTotal = $per * $rateCalculation;
    
                $total = floor($subTotal) * $request->pounds;
    
                $totalPrice = number_format((float)$total, 0, '.', '');

                if (request()->hasFile('receipt_image')) 
                {
                    $this->validate($request, [
                        'receipt_image' => 'required|mimes:jpeg,png,jpg'
                    ]);

                    $filename = uniqid(5).'-'.request()->receipt_image->getClientOriginalName();
                    if($columbitePayment->receipt_image) {
                        Storage::delete(str_replace("storage", "public", $columbitePayment->receipt_image));
                    }
                    request()->receipt_image->storeAs('payment_analysis', $filename, 'public');

                    $columbitePayment->update([
                        'type' => $request->type,
                        'supplier' => $request->supplier,
                        'staff' => $manager->id,
                        'grade' => $request->grade,
                        'bag' => 0,
                        'pound' => $request->pounds,
                        'total_in_pound' => $request->pounds,
                        'berating_rate_list' => $berate,
                        'percentage_analysis' => $request->percentage,
                        'analysis_rate_list' => $analysisRate,
                        'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                        'date_of_purchase' => $request->date_of_purchase,
                        'receipt_no' => $request->receipt_no,
                        'receipt_image' => '/storage/payment_analysis/'.$filename
                    ]);
                } else {
                    $columbitePayment->update([
                        'type' => $request->type,
                        'supplier' => $request->supplier,
                        'staff' => $manager->id,
                        'grade' => $request->grade,
                        'bag' => 0,
                        'pound' => $request->pounds,
                        'total_in_pound' => $request->pounds,
                        'berating_rate_list' => $berate,
                        'percentage_analysis' => $request->percentage,
                        'analysis_rate_list' => $analysisRate,
                        'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                        'date_of_purchase' => $request->date_of_purchase,
                        'receipt_no' => $request->receipt_no
                    ]);
                }

                $transaction = Transaction::where('accountant_process_id', $columbitePayment->id)->first();

                if($transaction)
                {
                    $transaction->update([
                        'amount' => $columbitePayment->price
                    ]);
                }

                Notification::create([
                    'to' => $columbitePayment->user_id,
                    'admin_id' => Auth::user()->id,
                    'title' => config('app.name'),
                    'body' => 'Admin has updated a payment receipt, with Receipt No:'.$columbitePayment->receipt_no
                ]);
            
                return back()->with([
                    'alertType' => 'success',
                    'back' => route('admin.payment.receipt.columbite.view', 'pound'),
                    'message' => 'Payment Receipt updated successfully'
                ]);
            } 

            return back()->with([
                'type' => 'danger',
                'message' => 'Please select weight type.'
            ]);
        }

        $this->validate($request, [
            'grade' => ['required', 'numeric'],
        ]);

        $berating = BeratingCalculation::find($request->grade);

        if(!$berating)
        {
            return back()->with([
                'type' => 'danger',
                'message' => 'Admin yet to add this berating value, try again later.'
            ]); 
        }
       
        if($request->poundweight == 'bag')
        {
            if($request->bag_pound == null)
            {
                $bag_pounds = 0;
            } else {
                $this->validate($request, [
                    'bag_pound' => ['required', 'numeric', 'max:79'],
                ]);

                $bag_pounds = $request->bag_pound;
            }

            $this->validate($request, [
                'bags' => ['required', 'numeric'],
                'percentage' => ['required', 'numeric', 'min:25'],
            ]);

            $analysis = AnalysisCalculation::get();

            foreach($analysis as $analyses)
            {
                if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                {
                    $myDollarRate = $analyses->dollar_rate;
                    $myExchangeRate = $analyses->exchange_rate;
                }
            }

            $dollarRate = $myDollarRate ?? 0;
            $exchangeRate = $myExchangeRate ?? 0;

            if($dollarRate == 0 && $exchangeRate == 0)
            {
                return back()->with([
                    'type' => 'danger',
                    'message' => 'Percentage Analysis entered not found in our database, try again.'
                ]);
            }

            if($bag_pounds < columbite_rate)
            {
                $per = $request->percentage / 100;

                $rateCalculation = $dollarRate * $exchangeRate;

                $subTotal = $per * $rateCalculation;

                $subPrice = $request->bags * columbite_rate + $request->bag_pound;
                
                $total = floor($subTotal) * $subPrice;

                $totalPrice = number_format((float)$total, 0, '.', '');

                return back()->with([
                    'previewPrice' => 'success',
                    'message' => $this->replaceCharsInNumber($totalPrice, '0')
                ]);
            } else {
                return back()->with([
                    'type' => 'danger',
                    'message' => 'pound should not be greater or equal to '.columbite_rate
                ]);
            }
            
        } 

        if($request->poundweight == 'pound')
        {
            $this->validate($request, [
                'pounds' => ['required', 'numeric']
            ]);

            $analysis = AnalysisCalculation::get();

            foreach($analysis as $analyses)
            {
                if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                {
                    $myDollarRate = $analyses->dollar_rate;
                    $myExchangeRate = $analyses->exchange_rate;
                }
            }

            $dollarRate = $myDollarRate ?? 0;
            $exchangeRate = $myExchangeRate ?? 0;

            if($dollarRate == 0 && $exchangeRate == 0)
            {
                return back()->with([
                    'type' => 'danger',
                    'message' => 'Percentage Analysis entered not found in our database, try again.'
                ]);
            }

            $per = $request->percentage / 100;

            $rateCalculation = $dollarRate * $exchangeRate;

            $subTotal = $per * $rateCalculation;

            $total = floor($subTotal) * $request->pounds;

            $totalPrice = number_format((float)$total, 0, '.', '');

            return back()->with([
                'previewPrice' => 'success',
                'message' => $this->replaceCharsInNumber($totalPrice, '0')
            ]);
        } 

        return back()->with([
            'type' => 'danger',
            'message' => 'Please select weight type.'
        ]);
    }

    public function payment_receipt_columbite_kg_update($id, Request $request)
    {
        $finder = Crypt::decrypt($id);

        $columbitePayment = PaymentReceiptColumbite::find($finder);

        if($request->save) 
        {
            $this->validate($request, [
                'supplier' => ['required', 'string', 'max:255'],
                'grade' => ['required', 'numeric'],
                'manager' => ['required', 'numeric'],
                'date_of_purchase' => ['required', 'date'],
                'receipt_no' => 'required|string',
            ]);

            $berating = BeratingCalculation::find($request->grade);

            if(!$berating)
            {
                return back()->with([
                    'type' => 'danger',
                    'message' => 'Admin yet to add this berating value, try again later.'
                ]); 
            }

            $response = [
                'grade' => $berating->grade,
                'price' => $berating->price,
                'unit_price' => $berating->unit_price
            ];

            $berate = json_encode($response);

            $manager = User::find($request->manager);

            if(!$manager)
            {
                return back()->with([
                    'type' => 'danger',
                    'message' => 'Manager not found in our database.'
                ]); 
            }

            if($request->kgweight == 'bag')
            {
                if($request->bag_kg == null)
                {
                    $bag_kgs = 0;
                } else {
                    $this->validate($request, [
                        'bag_kg' => ['required', 'numeric', 'max:49'],
                    ]);

                    $bag_kgs = $request->bag_kg;
                }

                $this->validate($request, [
                    'bags' => ['required', 'numeric'],
                    'percentage' => ['required', 'numeric', 'min:25'],
                ]);

                $analysis = AnalysisCalculation::get();

                foreach($analysis as $analyses)
                {
                    if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                    {
                        $myDollarRate = $analyses->dollar_rate;
                        $myExchangeRate = $analyses->exchange_rate;
                    }
                }

                $dollarRate = $myDollarRate ?? 0;
                $exchangeRate = $myExchangeRate ?? 0;

                if($dollarRate == 0 && $exchangeRate == 0)
                {
                    return back()->with([
                        'type' => 'danger',
                        'message' => 'Percentage Analysis entered not found in our database, try again.'
                    ]);
                }

                $res = [
                    'dollar_rate' => $dollarRate,
                    'exchange_rate' => $exchangeRate,
                ];
    
                $analysisRate = json_encode($res);

                if($bag_kgs < rate)
                {
                    $per = $request->percentage / 100;

                    $rateCalculation = $dollarRate * $exchangeRate;

                    $subTotal = $per * $rateCalculation * fixed_rate;

                    $subPrice = $request->bags * rate + $request->bag_kg;
                    
                    $total = floor($subTotal) * $subPrice;

                    $totalPrice = number_format((float)$total, 0, '.', '');
                    
                    if (request()->hasFile('receipt_image')) 
                    {
                        $this->validate($request, [
                            'receipt_image' => 'required|mimes:jpeg,png,jpg'
                        ]);

                        $filename = uniqid(5).'-'.request()->receipt_image->getClientOriginalName();
                        if($columbitePayment->receipt_image) {
                            Storage::delete(str_replace("storage", "public", $columbitePayment->receipt_image));
                        }
                        request()->receipt_image->storeAs('payment_analysis', $filename, 'public');

                        $columbitePayment ->update([
                            'type' => $request->type,
                            'supplier' => $request->supplier,
                            'staff' => $manager->id,
                            'grade' => $request->grade,
                            'bag' => $request->bags,
                            'kg' => $bag_kgs,
                            'total_in_kg' => $subPrice,
                            'berating_rate_list' => $berate,
                            'percentage_analysis' => $request->percentage,
                            'analysis_rate_list' => $analysisRate,
                            'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                            'date_of_purchase' => $request->date_of_purchase,
                            'receipt_no' => $request->receipt_no,
                            'receipt_image' => '/storage/payment_analysis/'.$filename
                        ]);
                    } else {
                        $columbitePayment ->update([
                            'type' => $request->type,
                            'supplier' => $request->supplier,
                            'staff' => $manager->id,
                            'grade' => $request->grade,
                            'bag' => $request->bags,
                            'kg' => $bag_kgs,
                            'total_in_kg' => $subPrice,
                            'berating_rate_list' => $berate,
                            'percentage_analysis' => $request->percentage,
                            'analysis_rate_list' => $analysisRate,
                            'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                            'date_of_purchase' => $request->date_of_purchase,
                            'receipt_no' => $request->receipt_no,
                        ]);
                    }

                    $transaction = Transaction::where('accountant_process_id', $columbitePayment->id)->first();

                    if($transaction)
                    {
                        $transaction->update([
                            'amount' => $columbitePayment->price
                        ]);
                    }

                    Notification::create([
                        'to' => $columbitePayment->user_id,
                        'admin_id' => Auth::user()->id,
                        'title' => config('app.name'),
                        'body' => 'Admin has updated a payment receipt, with Receipt No:'.$columbitePayment->receipt_no
                    ]);
            
                    return back()->with([
                        'alertType' => 'success',
                        'back' => route('admin.payment.receipt.columbite.view', 'kg'),
                        'message' => 'Payment Receipt created successfully.'
                    ]);
                } else {
                    return back()->with([
                        'type' => 'danger',
                        'message' => 'kg should not be greater or equal to '.rate
                    ]);
                }
            } 

            if($request->kgweight == 'kg')
            {
                $this->validate($request, [
                    'kg' => ['required', 'numeric']
                ]);
    
                $analysis = AnalysisCalculation::get();
    
                foreach($analysis as $analyses)
                {
                    if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                    {
                        $myDollarRate = $analyses->dollar_rate;
                        $myExchangeRate = $analyses->exchange_rate;
                    }
                }

                $dollarRate = $myDollarRate ?? 0;
                $exchangeRate = $myExchangeRate ?? 0;

                if($dollarRate == 0 && $exchangeRate == 0)
                {
                    return back()->with([
                        'type' => 'danger',
                        'message' => 'Percentage Analysis entered not found in our database, try again.'
                    ]);
                }

                $res = [
                    'dollar_rate' => $dollarRate,
                    'exchange_rate' => $exchangeRate,
                ];
    
                $analysisRate = json_encode($res);

                $per = $request->percentage / 100;
    
                $rateCalculation = $dollarRate * $exchangeRate;
    
                $subTotal = $per * $rateCalculation * fixed_rate;
    
                $total = floor($subTotal) * $request->kg;
    
                $totalPrice = number_format((float)$total, 0, '.', '');

                if (request()->hasFile('receipt_image')) 
                {
                    $this->validate($request, [
                        'receipt_image' => 'required|mimes:jpeg,png,jpg'
                    ]);

                    $filename = uniqid(5).'-'.request()->receipt_image->getClientOriginalName();
                    if($columbitePayment->receipt_image) {
                        Storage::delete(str_replace("storage", "public", $columbitePayment->receipt_image));
                    }
                    request()->receipt_image->storeAs('payment_analysis', $filename, 'public');

                    $columbitePayment->updated([
                        'type' => $request->type,
                        'supplier' => $request->supplier,
                        'staff' => $manager->id,
                        'grade' => $request->grade,
                        'kg' => $request->kg,
                        'total_in_kg' => $request->kg,
                        'berating_rate_list' => $berate,
                        'percentage_analysis' => $request->percentage,
                        'analysis_rate_list' => $analysisRate,
                        'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                        'date_of_purchase' => $request->date_of_purchase,
                        'receipt_no' => $request->receipt_no,
                        'receipt_image' => '/storage/payment_analysis/'.$filename
                    ]);
                } else {
                    $columbitePayment->updated([
                        'type' => $request->type,
                        'supplier' => $request->supplier,
                        'staff' => $manager->id,
                        'grade' => $request->grade,
                        'kg' => $request->kg,
                        'total_in_kg' => $request->kg,
                        'berating_rate_list' => $berate,
                        'percentage_analysis' => $request->percentage,
                        'analysis_rate_list' => $analysisRate,
                        'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                        'date_of_purchase' => $request->date_of_purchase,
                        'receipt_no' => $request->receipt_no,
                    ]);
                }
        
                $transaction = Transaction::where('accountant_process_id', $columbitePayment->id)->first();

                if($transaction)
                {
                    $transaction->update([
                        'amount' => $columbitePayment->price
                    ]);
                }

                Notification::create([
                    'to' => $columbitePayment->user_id,
                    'admin_id' => Auth::user()->id,
                    'title' => config('app.name'),
                    'body' => 'Admin has updated a payment receipt, with Receipt No:'.$columbitePayment->receipt_no
                ]);
            
                return back()->with([
                    'alertType' => 'success',
                    'back' => route('admin.payment.receipt.columbite.view', 'kg'),
                    'message' => 'Payment Receipt created successfully.'
                ]);
            } 

            return back()->with([
                'type' => 'danger',
                'message' => 'Please select weight type.'
            ]);
        }

        $this->validate($request, [
            'grade' => ['required', 'numeric'],
        ]);

        $berating = BeratingCalculation::find($request->grade);

        if(!$berating)
        {
            return back()->with([
                'type' => 'danger',
                'message' => 'Admin yet to add this berating value, try again later.'
            ]); 
        }
       
        if($request->kgweight == 'bag')
        {
            if($request->bag_kg == null)
            {
                $bag_kgs = 0;
            } else {
                $this->validate($request, [
                    'bag_kg' => ['required', 'numeric', 'max:49'],
                ]);

                $bag_kgs = $request->bag_kg;
            }

            $this->validate($request, [
                'bags' => ['required', 'numeric'],
                'percentage' => ['required', 'numeric', 'min:25'],
            ]);

            $analysis = AnalysisCalculation::get();

            foreach($analysis as $analyses)
            {
                if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                {
                    $myDollarRate = $analyses->dollar_rate;
                    $myExchangeRate = $analyses->exchange_rate;
                }
            }

            $dollarRate = $myDollarRate ?? 0;
            $exchangeRate = $myExchangeRate ?? 0;

            if($dollarRate == 0 && $exchangeRate == 0)
            {
                return back()->with([
                    'type' => 'danger',
                    'message' => 'Percentage Analysis entered not found in our database, try again.'
                ]);
            }

            if($bag_kgs < rate)
            {
                $per = $request->percentage / 100;

                $rateCalculation = $dollarRate * $exchangeRate;

                $subTotal = $per * $rateCalculation * fixed_rate;

                $subPrice = $request->bags * rate + $request->bag_kg;
                
                $total = floor($subTotal) * $subPrice;

                $totalPrice = number_format((float)$total, 0, '.', '');

                return back()->with([
                    'previewPrice' => 'success',
                    'message' => $this->replaceCharsInNumber($totalPrice, '0')
                ]);
            } else {
                return back()->with([
                    'type' => 'danger',
                    'message' => 'kg should not be greater or equal to '.rate
                ]);
            }
            
        } 

        if($request->kgweight == 'kg')
        {
            $this->validate($request, [
                'kg' => ['required', 'numeric']
            ]);

            $analysis = AnalysisCalculation::get();

            foreach($analysis as $analyses)
            {
                if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                {
                    $myDollarRate = $analyses->dollar_rate;
                    $myExchangeRate = $analyses->exchange_rate;
                }
            }

            $dollarRate = $myDollarRate ?? 0;
            $exchangeRate = $myExchangeRate ?? 0;

            if($dollarRate == 0 && $exchangeRate == 0)
            {
                return back()->with([
                    'type' => 'danger',
                    'message' => 'Percentage Analysis entered not found in our database, try again.'
                ]);
            }

            $per = $request->percentage / 100;

            $rateCalculation = $dollarRate * $exchangeRate;

            $subTotal = $per * $rateCalculation * fixed_rate;

            $total = floor($subTotal) * $request->kg;

            $totalPrice = number_format((float)$total, 0, '.', '');

            return back()->with([
                'previewPrice' => 'success',
                'message' => $this->replaceCharsInNumber($totalPrice, '0')
            ]);
        } 

        return back()->with([
            'type' => 'danger',
            'message' => 'Please select weight type.'
        ]);
    }

    public function payment_receipt_columbite_delete($id, $type)
    {
        $finder = Crypt::decrypt($id);

        $columbitePayment = PaymentReceiptColumbite::find($finder);

        $transaction = Transaction::where('accountant_process_id', $columbitePayment->id)->first();

        if($transaction)
        {
            $transaction->delete();
        }

        if($columbitePayment->receipt_image) {
            Storage::delete(str_replace("storage", "public", $columbitePayment->receipt_image));
        }

        $columbitePayment->delete();

        return redirect()->route('admin.payment.receipt.columbite.view', $type)->with([
            'alertType' => 'success',
            'message' => 'Payment receipt deleted successfully!'
        ]);
    }

    // Lower Grade Columbite
    public function payment_receipt_lower_grade_columbite_view($id, Request $request)
    {
        if($id == 'kg')
        {
            if($request->start_date == null && $request->end_date == null)
            {
                $lowergradecolumbitePaymentReceiptKg = PaymentReceiptLowerGradeColumbite::latest()->where('type', 'kg')->get();
                $lowergradecolumbitePaymentReceiptPound = PaymentReceiptLowerGradeColumbite::latest()->where('type', 'pound')->get();
            } else {
                $lowergradecolumbitePaymentReceiptKg = PaymentReceiptLowerGradeColumbite::latest()->where('type', 'kg')->whereBetween('date_of_purchase', [$request->start_date, $request->end_date])->get();
                $lowergradecolumbitePaymentReceiptPound = PaymentReceiptLowerGradeColumbite::latest()->where('type', 'pound')->get();
            }

            $active_tab = $id;

            if($active_tab == 'pound') {
                return view('admin.payment-receipt.view_lower_grade_columbite', [
                    'lowergradecolumbitePaymentReceiptKg' => $lowergradecolumbitePaymentReceiptKg,
                    'lowergradecolumbitePaymentReceiptPound' => $lowergradecolumbitePaymentReceiptPound,
                    'active_tab' => $active_tab,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date
                ]);
            } elseif($active_tab == 'kg') {
                return view('admin.payment-receipt.view_lower_grade_columbite', [
                    'lowergradecolumbitePaymentReceiptKg' => $lowergradecolumbitePaymentReceiptKg,
                    'lowergradecolumbitePaymentReceiptPound' => $lowergradecolumbitePaymentReceiptPound,
                    'active_tab' => $active_tab,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date
                ]);
            } else {
                $active_tab == 'kg';
                return view('admin.payment-receipt.view_lower_grade_columbite', [
                    'lowergradecolumbitePaymentReceiptKg' => $lowergradecolumbitePaymentReceiptKg,
                    'lowergradecolumbitePaymentReceiptPound' => $lowergradecolumbitePaymentReceiptPound,
                    'active_tab' => $active_tab,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date
                ]);
            }
        }

        if($id == 'pound')
        {
            if($request->start_date == null && $request->end_date == null)
            {
                $lowergradecolumbitePaymentReceiptKg = PaymentReceiptLowerGradeColumbite::latest()->where('type', 'kg')->get();
                $lowergradecolumbitePaymentReceiptPound = PaymentReceiptLowerGradeColumbite::latest()->where('type', 'pound')->get();
            } else {
                $lowergradecolumbitePaymentReceiptPound = PaymentReceiptLowerGradeColumbite::latest()->where('type', 'pound')->whereBetween('date_of_purchase', [$request->start_date, $request->end_date])->get();
                $lowergradecolumbitePaymentReceiptKg = PaymentReceiptLowerGradeColumbite::latest()->where('type', 'kg')->get();
            }

            $active_tab = $id;

            if($active_tab == 'pound') {
                return view('admin.payment-receipt.view_lower_grade_columbite', [
                    'lowergradecolumbitePaymentReceiptKg' => $lowergradecolumbitePaymentReceiptKg,
                    'lowergradecolumbitePaymentReceiptPound' => $lowergradecolumbitePaymentReceiptPound,
                    'active_tab' => $active_tab,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date
                ]);
            } elseif($active_tab == 'kg') {
                return view('admin.payment-receipt.view_lower_grade_columbite', [
                    'lowergradecolumbitePaymentReceiptKg' => $lowergradecolumbitePaymentReceiptKg,
                    'lowergradecolumbitePaymentReceiptPound' => $lowergradecolumbitePaymentReceiptPound,
                    'active_tab' => $active_tab,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date
                ]);
            } else {
                $active_tab == 'kg';
                return view('admin.payment-receipt.view_lower_grade_columbite', [
                    'lowergradecolumbitePaymentReceiptKg' => $lowergradecolumbitePaymentReceiptKg,
                    'lowergradecolumbitePaymentReceiptPound' => $lowergradecolumbitePaymentReceiptPound,
                    'active_tab' => $active_tab,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date
                ]);
            }
        }

    }

    public function payment_receipt_lower_grade_columbite_add($id)
    {
        $active_tab = $id;

        if($active_tab == 'pound') {
            return view ('admin.payment-receipt.add_lower_grade_columbite', compact('active_tab'));
        } elseif($active_tab == 'kg') {
            return view ('admin.payment-receipt.add_lower_grade_columbite', compact('active_tab'));
        } else {
            $active_tab == 'kg';
            return view ('admin.payment-receipt.add_lower_grade_columbite', compact('active_tab'));
        }
    }

    public function payment_receipt_lower_grade_columbite_pound_post(Request $request)
    {
        if($request->save) 
        {
            $this->validate($request, [
                'assist_manager' => ['required', 'string', 'max:255'],
                'supplier' => ['required', 'string', 'max:255'],
                'grade' => ['required', 'numeric'],
                'manager' => ['required', 'numeric'],
                'date_of_purchase' => ['required', 'date'],
                'receipt_no' => 'required|string',
                'receipt_image' => 'required|mimes:jpeg,png,jpg'
            ]);

            $berating = BeratingCalculation::find($request->grade);
    
            if(!$berating)
            {
                return redirect()->route('admin.payment.receipt.lower.grade.columbite.add', 'pound')->with([
                    'type' => 'danger',
                    'message' => 'Admin yet to add this berating value, try again later.'
                ]); 
            }

            $response = [
                'grade' => $berating->grade,
                'price' => $berating->price,
                'unit_price' => $berating->unit_price
            ];

            $berate = json_encode($response);

            $manager = User::find($request->manager);

            if(!$manager)
            {
                return redirect()->route('admin.payment.receipt.lower.grade.columbite.add', 'pound')->with([
                    'type' => 'danger',
                    'message' => 'Manager not found in our database.'
                ]); 
            }

            $assist_manager = User::find($request->assist_manager);

            if(!$assist_manager)
            {
                return redirect()->route('admin.payment.receipt.lower.grade.columbite.add', 'pound')->with([
                    'type' => 'danger',
                    'message' => 'Assistant Manager not found in our database.'
                ]); 
            }

            if($request->poundweight == 'bag')
            {
                if($request->bag_pound == null)
                {
                    $bag_pounds = 0;
                } else {
                    $this->validate($request, [
                        'bag_pound' => ['required', 'numeric', 'max:79'],
                    ]);

                    $bag_pounds = $request->bag_pound;
                }

                $this->validate($request, [
                    'bags' => ['required', 'numeric'],
                    'percentage' => ['required', 'numeric', 'min:25'],
                ]);

                $analysis = AnalysisCalculation::get();

                foreach($analysis as $analyses)
                {
                    if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                    {
                        $myDollarRate = $analyses->dollar_rate;
                        $myExchangeRate = $analyses->exchange_rate;
                    }
                }

                $dollarRate = $myDollarRate ?? 0;
                $exchangeRate = $myExchangeRate ?? 0;

                if($dollarRate == 0 && $exchangeRate == 0)
                {
                    return redirect()->route('admin.payment.receipt.lower.grade.columbite.add', 'pound')->with([
                        'type' => 'danger',
                        'message' => 'Percentage Analysis entered not found in our database, try again.'
                    ]);
                }

                $res = [
                    'dollar_rate' => $dollarRate,
                    'exchange_rate' => $exchangeRate,
                ];
    
                $analysisRate = json_encode($res);

                if($bag_pounds < columbite_rate)
                {
                    $per = $request->percentage / 100;

                    $rateCalculation = $dollarRate * $exchangeRate;

                    $subTotal = $per * $rateCalculation;

                    $subPrice = $request->bags * columbite_rate + $request->bag_pound;
                    
                    $total = floor($subTotal) * $subPrice;

                    $totalPrice = number_format((float)$total, 0, '.', '');
                    
                    $filename = request()->receipt_image->getClientOriginalName();
                    request()->receipt_image->storeAs('payment_analysis', $filename, 'public');

                    $lgcolumbitePayment = PaymentReceiptLowerGradeColumbite::create([
                        'type' => $request->type,
                        'user_id' => $assist_manager->id,
                        'supplier' => $request->supplier,
                        'staff' => $manager->id,
                        'grade' => $request->grade,
                        'bag' => $request->bags,
                        'pound' => $bag_pounds,
                        'total_in_pound' => $subPrice,
                        'berating_rate_list' => $berate,
                        'percentage_analysis' => $request->percentage,
                        'analysis_rate_list' => $analysisRate,
                        'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                        'date_of_purchase' => $request->date_of_purchase,
                        'receipt_no' => $request->receipt_no,
                        'receipt_image' => '/storage/payment_analysis/'.$filename
                    ]);
            
                    Transaction::create([
                        'user_id' => $assist_manager->id,
                        'accountant_process_id' => $lgcolumbitePayment->id,
                        'amount' => $lgcolumbitePayment->price,
                        'reference' => config('app.name'),
                        'status' => 'Payment Receipt'
                    ]);

                    Notification::create([
                        'to' => $assist_manager->id,
                        'admin_id' => Auth::user()->id,
                        'title' => config('app.name'),
                        'body' => 'Admin has added a payment receipt, with Receipt No:'.$lgcolumbitePayment->receipt_no.' on your behalf.'
                    ]);

                    return redirect()->route('admin.payment.receipt.lower.grade.columbite.add', 'pound')->with([
                        'alertType' => 'success',
                        'back' => route('admin.payment.receipt.lower.grade.columbite.view', 'pound'),
                        'message' => 'Payment Receipt created successfully.'
                    ]);
                } else {
                    return redirect()->route('admin.payment.receipt.lower.grade.columbite.add', 'pound')->with([
                        'type' => 'danger',
                        'message' => 'kg should not be greater or equal to '.columbite_rate
                    ]);
                }
            } 

            if($request->poundweight == 'pound')
            {
                $this->validate($request, [
                    'pounds' => ['required', 'numeric']
                ]);
    
                $analysis = AnalysisCalculation::get();
    
                foreach($analysis as $analyses)
                {
                    if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                    {
                        $myDollarRate = $analyses->dollar_rate;
                        $myExchangeRate = $analyses->exchange_rate;
                    }
                }

                $dollarRate = $myDollarRate ?? 0;
                $exchangeRate = $myExchangeRate ?? 0;

                if($dollarRate == 0 && $exchangeRate == 0)
                {
                    return redirect()->route('admin.payment.receipt.lower.grade.columbite.add', 'pound')->with([
                        'type' => 'danger',
                        'message' => 'Percentage Analysis entered not found in our database, try again.'
                    ]);
                }
    
                $res = [
                    'dollar_rate' => $dollarRate,
                    'exchange_rate' => $exchangeRate,
                ];
    
                $analysisRate = json_encode($res);

                $per = $request->percentage / 100;
    
                $rateCalculation = $dollarRate * $exchangeRate;
    
                $subTotal = $per * $rateCalculation;
    
                $total = floor($subTotal) * $request->pounds;
    
                $totalPrice = number_format((float)$total, 0, '.', '');

                $filename = uniqid(5).'-'.request()->receipt_image->getClientOriginalName();
                request()->receipt_image->storeAs('payment_analysis', $filename, 'public');

                $lgcolumbitePayment = PaymentReceiptLowerGradeColumbite::create([
                    'type' => $request->type,
                    'user_id' => $assist_manager->id,
                    'supplier' => $request->supplier,
                    'staff' => $manager->id,
                    'grade' => $request->grade,
                    'pound' => $request->pounds,
                    'total_in_pound' => $request->pounds,
                    'berating_rate_list' => $berate,
                    'percentage_analysis' => $request->percentage,
                    'analysis_rate_list' => $analysisRate,
                    'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                    'date_of_purchase' => $request->date_of_purchase,
                    'receipt_no' => $request->receipt_no,
                    'receipt_image' => '/storage/payment_analysis/'.$filename
                ]);
        
                Transaction::create([
                    'user_id' => $assist_manager->id,
                    'accountant_process_id' => $lgcolumbitePayment->id,
                    'amount' => $lgcolumbitePayment->price,
                    'reference' => config('app.name'),
                    'status' => 'Payment Receipt'
                ]);

                Notification::create([
                    'to' => $assist_manager->id,
                    'admin_id' => Auth::user()->id,
                    'title' => config('app.name'),
                    'body' => 'Admin has added a payment receipt, with Receipt No:'.$lgcolumbitePayment->receipt_no.' on your behalf.'
                ]);


                return redirect()->route('admin.payment.receipt.lower.grade.columbite.add', 'pound')->with([
                    'alertType' => 'success',
                    'back' => route('admin.payment.receipt.lower.grade.columbite.view', 'pound'),
                    'message' => 'Payment Receipt created successfully.'
                ]);
            } 

            return redirect()->route('admin.payment.receipt.lower.grade.columbite.add', 'pound')->with([
                'type' => 'danger',
                'message' => 'Please select weight type.'
            ]);
        }

        $this->validate($request, [
            'grade' => ['required', 'numeric'],
        ]);

        $berating = BeratingCalculation::find($request->grade);

        if(!$berating)
        {
            return redirect()->route('admin.payment.receipt.lower.grade.columbite.add', 'pound')->with([
                'type' => 'danger',
                'message' => 'Admin yet to add this berating value, try again later.'
            ]); 
        }
       
        if($request->poundweight == 'bag')
        {
            if($request->bag_pound == null)
            {
                $bag_pounds = 0;
            } else {
                $this->validate($request, [
                    'bag_pound' => ['required', 'numeric', 'max:79'],
                ]);

                $bag_pounds = $request->bag_pound;
            }

            $this->validate($request, [
                'bags' => ['required', 'numeric'],
                'percentage' => ['required', 'numeric', 'min:25'],
            ]);

            $analysis = AnalysisCalculation::get();

            foreach($analysis as $analyses)
            {
                if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                {
                    $myDollarRate = $analyses->dollar_rate;
                    $myExchangeRate = $analyses->exchange_rate;
                }
            }

            $dollarRate = $myDollarRate ?? 0;
            $exchangeRate = $myExchangeRate ?? 0;

            if($dollarRate == 0 && $exchangeRate == 0)
            {
                return redirect()->route('admin.payment.receipt.lower.grade.columbite.add', 'pound')->with([
                    'type' => 'danger',
                    'message' => 'Percentage Analysis entered not found in our database, try again.'
                ]);
            }

            if($bag_pounds < columbite_rate)
            {
                $per = $request->percentage / 100;

                $rateCalculation = $dollarRate * $exchangeRate;

                $subTotal = $per * $rateCalculation;

                $subPrice = $request->bags * columbite_rate + $request->bag_pound;
                
                $total = floor($subTotal) * $subPrice;

                $totalPrice = number_format((float)$total, 0, '.', '');

                return redirect()->route('admin.payment.receipt.lower.grade.columbite.add', 'pound')->with([
                    'previewPrice' => 'success',
                    'message' => $this->replaceCharsInNumber($totalPrice, '0')
                ]);
            } else {
                return redirect()->route('admin.payment.receipt.lower.grade.columbite.add', 'pound')->with([
                    'type' => 'danger',
                    'message' => 'pound should not be greater or equal to '.columbite_rate
                ]);
            }
            
        } 

        if($request->poundweight == 'pound')
        {
            $this->validate($request, [
                'pounds' => ['required', 'numeric']
            ]);

            $analysis = AnalysisCalculation::get();

            foreach($analysis as $analyses)
            {
                if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                {
                    $myDollarRate = $analyses->dollar_rate;
                    $myExchangeRate = $analyses->exchange_rate;
                }
            }

            $dollarRate = $myDollarRate ?? 0;
            $exchangeRate = $myExchangeRate ?? 0;

            if($dollarRate == 0 && $exchangeRate == 0)
            {
                return redirect()->route('admin.payment.receipt.lower.grade.columbite.add', 'pound')->with([
                    'type' => 'danger',
                    'message' => 'Percentage Analysis entered not found in our database, try again.'
                ]);
            }

            $per = $request->percentage / 100;

            $rateCalculation = $dollarRate * $exchangeRate;

            $subTotal = $per * $rateCalculation;

            $total = floor($subTotal) * $request->pounds;

            $totalPrice = number_format((float)$total, 0, '.', '');

            return redirect()->route('admin.payment.receipt.lower.grade.columbite.add', 'pound')->with([
                'previewPrice' => 'success',
                'message' => $this->replaceCharsInNumber($totalPrice, '0')
            ]);
        } 

        return redirect()->route('admin.payment.receipt.lower.grade.columbite.add', 'pound')->with([
            'type' => 'danger',
            'message' => 'Please select weight type.'
        ]);
    }

    public function payment_receipt_lower_grade_columbite_kg_post(Request $request)
    {
        if($request->save) 
        {
            $this->validate($request, [
                'assist_manager' => ['required', 'string', 'max:255'],
                'supplier' => ['required', 'string', 'max:255'],
                'grade' => ['required', 'numeric'],
                'manager' => ['required', 'numeric'],
                'date_of_purchase' => ['required', 'date'],
                'receipt_no' => 'required|string',
                'receipt_image' => 'required|mimes:jpeg,png,jpg'
            ]);

            $berating = BeratingCalculation::find($request->grade);

            if(!$berating)
            {
                return redirect()->route('admin.payment.receipt.lower.grade.columbite.add', 'kg')->with([
                    'type' => 'danger',
                    'message' => 'Admin yet to add this berating value, try again later.'
                ]); 
            }

            $response = [
                'grade' => $berating->grade,
                'price' => $berating->price,
                'unit_price' => $berating->unit_price
            ];

            $berate = json_encode($response);

            $manager = User::find($request->manager);

            if(!$manager)
            {
                return redirect()->route('admin.payment.receipt.lower.grade.columbite.add', 'kg')->with([
                    'type' => 'danger',
                    'message' => 'Manager not found in our database.'
                ]); 
            }

            $assist_manager = User::find($request->assist_manager);

            if(!$assist_manager)
            {
                return redirect()->route('admin.payment.receipt.lower.grade.columbite.add', 'kg')->with([
                    'type' => 'danger',
                    'message' => 'Assistant Manager not found in our database.'
                ]); 
            }

            if($request->kgweight == 'bag')
            {
                if($request->bag_kg == null)
                {
                    $bag_kgs = 0;
                } else {
                    $this->validate($request, [
                        'bag_kg' => ['required', 'numeric', 'max:49'],
                    ]);

                    $bag_kgs = $request->bag_kg;
                }

                $this->validate($request, [
                    'bags' => ['required', 'numeric'],
                    'percentage' => ['required', 'numeric', 'min:25'],
                ]);

                $analysis = AnalysisCalculation::get();

                foreach($analysis as $analyses)
                {
                    if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                    {
                        $myDollarRate = $analyses->dollar_rate;
                        $myExchangeRate = $analyses->exchange_rate;
                    }
                }

                $dollarRate = $myDollarRate ?? 0;
                $exchangeRate = $myExchangeRate ?? 0;

                if($dollarRate == 0 && $exchangeRate == 0)
                {
                    return redirect()->route('admin.payment.receipt.lower.grade.columbite.add', 'kg')->with([
                        'type' => 'danger',
                        'message' => 'Percentage Analysis entered not found in our database, try again.'
                    ]);
                }

                $res = [
                    'dollar_rate' => $dollarRate,
                    'exchange_rate' => $exchangeRate,
                ];
    
                $analysisRate = json_encode($res);

                if($bag_kgs < rate)
                {
                    $per = $request->percentage / 100;

                    $rateCalculation = $dollarRate * $exchangeRate;

                    $subTotal = $per * $rateCalculation * fixed_rate;

                    $subPrice = $request->bags * rate + $request->bag_kg;
                    
                    $total = floor($subTotal) * $subPrice;

                    $totalPrice = number_format((float)$total, 0, '.', '');
                    
                    $filename = uniqid(5).'-'.request()->receipt_image->getClientOriginalName();
                    request()->receipt_image->storeAs('payment_analysis', $filename, 'public');

                    $lgcolumbitePayment = PaymentReceiptLowerGradeColumbite::create([
                        'type' => $request->type,
                        'user_id' => $assist_manager->id,
                        'supplier' => $request->supplier,
                        'staff' => $manager->id,
                        'grade' => $request->grade,
                        'bag' => $request->bags,
                        'kg' => $bag_kgs,
                        'total_in_kg' => $subPrice,
                        'berating_rate_list' => $berate,
                        'percentage_analysis' => $request->percentage,
                        'analysis_rate_list' => $analysisRate,
                        'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                        'date_of_purchase' => $request->date_of_purchase,
                        'receipt_no' => $request->receipt_no,
                        'receipt_image' => '/storage/payment_analysis/'.$filename
                    ]);

                    Transaction::create([
                        'user_id' => $assist_manager->id,
                        'accountant_process_id' => $lgcolumbitePayment->id,
                        'amount' => $lgcolumbitePayment->price,
                        'reference' => config('app.name'),
                        'status' => 'Payment Receipt'
                    ]);

                    Notification::create([
                        'to' => $assist_manager->id,
                        'admin_id' => Auth::user()->id,
                        'title' => config('app.name'),
                        'body' => 'Admin has added a payment receipt, with Receipt No:'.$lgcolumbitePayment->receipt_no.' on your behalf.'
                    ]);
    
                    return redirect()->route('admin.payment.receipt.lower.grade.columbite.add', 'kg')->with([
                        'alertType' => 'success',
                        'back' => route('admin.payment.receipt.lower.grade.columbite.view', 'kg'),
                        'message' => 'Payment Receipt created successfully.'
                    ]);
                } else {
                    return redirect()->route('admin.payment.receipt.lower.grade.columbite.add', 'kg')->with([
                        'type' => 'danger',
                        'message' => 'kg should not be greater or equal to '.rate
                    ]);
                }
            } 

            if($request->kgweight == 'kg')
            {
                $this->validate($request, [
                    'kg' => ['required', 'numeric']
                ]);
    
                $analysis = AnalysisCalculation::get();
    
                foreach($analysis as $analyses)
                {
                    if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                    {
                        $myDollarRate = $analyses->dollar_rate;
                        $myExchangeRate = $analyses->exchange_rate;
                    }
                }

                $dollarRate = $myDollarRate ?? 0;
                $exchangeRate = $myExchangeRate ?? 0;

                if($dollarRate == 0 && $exchangeRate == 0)
                {
                    return redirect()->route('admin.payment.receipt.lower.grade.columbite.add', 'kg')->with([
                        'type' => 'danger',
                        'message' => 'Percentage Analysis entered not found in our database, try again.'
                    ]);
                }

                $res = [
                    'dollar_rate' => $dollarRate,
                    'exchange_rate' => $exchangeRate,
                ];
    
                $analysisRate = json_encode($res);

                $per = $request->percentage / 100;
    
                $rateCalculation = $dollarRate * $exchangeRate;
    
                $subTotal = $per * $rateCalculation * fixed_rate;
    
                $total = floor($subTotal) * $request->kg;
    
                $totalPrice = number_format((float)$total, 0, '.', '');

                $filename = uniqid(5).'-'.request()->receipt_image->getClientOriginalName();
                request()->receipt_image->storeAs('payment_analysis', $filename, 'public');

                $lgcolumbitePayment = PaymentReceiptLowerGradeColumbite::create([
                    'type' => $request->type,
                    'user_id' => $assist_manager->id,
                    'supplier' => $request->supplier,
                    'staff' => $manager->id,
                    'grade' => $request->grade,
                    'kg' => $request->kg,
                    'total_in_kg' => $request->kg,
                    'berating_rate_list' => $berate,
                    'percentage_analysis' => $request->percentage,
                    'analysis_rate_list' => $analysisRate,
                    'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                    'date_of_purchase' => $request->date_of_purchase,
                    'receipt_no' => $request->receipt_no,
                    'receipt_image' => '/storage/payment_analysis/'.$filename
                ]);
        
                Transaction::create([
                    'user_id' => $assist_manager->id,
                    'accountant_process_id' => $lgcolumbitePayment->id,
                    'amount' => $lgcolumbitePayment->price,
                    'reference' => config('app.name'),
                    'status' => 'Payment Receipt'
                ]);

                Notification::create([
                    'to' => $assist_manager->id,
                    'admin_id' => Auth::user()->id,
                    'title' => config('app.name'),
                    'body' => 'Admin has added a payment receipt, with Receipt No:'.$lgcolumbitePayment->receipt_no.' on your behalf.'
                ]);

                return redirect()->route('admin.payment.receipt.lower.grade.columbite.add', 'kg')->with([
                    'alertType' => 'success',
                    'back' => route('admin.payment.receipt.lower.grade.columbite.view', 'kg'),
                    'message' => 'Payment Receipt created successfully.'
                ]);
            } 

            return redirect()->route('admin.payment.receipt.lower.grade.columbite.add', 'kg')->with([
                'type' => 'danger',
                'message' => 'Please select weight type.'
            ]);
        }

        $this->validate($request, [
            'grade' => ['required', 'numeric'],
        ]);

        $berating = BeratingCalculation::find($request->grade);

        if(!$berating)
        {
            return redirect()->route('admin.payment.receipt.lower.grade.columbite.add', 'kg')->with([
                'type' => 'danger',
                'message' => 'Admin yet to add this berating value, try again later.'
            ]); 
        }
       
        if($request->kgweight == 'bag')
        {
            if($request->bag_kg == null)
            {
                $bag_kgs = 0;
            } else {
                $this->validate($request, [
                    'bag_kg' => ['required', 'numeric', 'max:49'],
                ]);

                $bag_kgs = $request->bag_kg;
            }

            $this->validate($request, [
                'bags' => ['required', 'numeric'],
                'percentage' => ['required', 'numeric', 'min:25'],
            ]);

            $analysis = AnalysisCalculation::get();

            foreach($analysis as $analyses)
            {
                if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                {
                    $myDollarRate = $analyses->dollar_rate;
                    $myExchangeRate = $analyses->exchange_rate;
                }
            }

            $dollarRate = $myDollarRate ?? 0;
            $exchangeRate = $myExchangeRate ?? 0;

            if($dollarRate == 0 && $exchangeRate == 0)
            {
                return redirect()->route('admin.payment.receipt.lower.grade.columbite.add', 'kg')->with([
                    'type' => 'danger',
                    'message' => 'Percentage Analysis entered not found in our database, try again.'
                ]);
            }

            if($bag_kgs < rate)
            {
                $per = $request->percentage / 100;

                $rateCalculation = $dollarRate * $exchangeRate;

                $subTotal = $per * $rateCalculation * fixed_rate;

                $subPrice = $request->bags * rate + $request->bag_kg;
                
                $total = floor($subTotal) * $subPrice;

                $totalPrice = number_format((float)$total, 0, '.', '');

                return redirect()->route('admin.payment.receipt.lower.grade.columbite.add', 'kg')->with([
                    'previewPrice' => 'success',
                    'message' => $this->replaceCharsInNumber($totalPrice, '0')
                ]);
            } else {
                return redirect()->route('admin.payment.receipt.lower.grade.columbite.add', 'kg')->with([
                    'type' => 'danger',
                    'message' => 'kg should not be greater or equal to '.rate
                ]);
            }
            
        } 

        if($request->kgweight == 'kg')
        {
            $this->validate($request, [
                'kg' => ['required', 'numeric']
            ]);

            $analysis = AnalysisCalculation::get();

            foreach($analysis as $analyses)
            {
                if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                {
                    $myDollarRate = $analyses->dollar_rate;
                    $myExchangeRate = $analyses->exchange_rate;
                }
            }

            $dollarRate = $myDollarRate ?? 0;
            $exchangeRate = $myExchangeRate ?? 0;

            if($dollarRate == 0 && $exchangeRate == 0)
            {
                return redirect()->route('admin.payment.receipt.lower.grade.columbite.add', 'kg')->with([
                    'type' => 'danger',
                    'message' => 'Percentage Analysis entered not found in our database, try again.'
                ]);
            }

            $per = $request->percentage / 100;

            $rateCalculation = $dollarRate * $exchangeRate;

            $subTotal = $per * $rateCalculation * fixed_rate;

            $total = floor($subTotal) * $request->kg;

            $totalPrice = number_format((float)$total, 0, '.', '');

            return redirect()->route('admin.payment.receipt.lower.grade.columbite.add', 'kg')->with([
                'previewPrice' => 'success',
                'message' => $this->replaceCharsInNumber($totalPrice, '0')
            ]);
        } 

        return redirect()->route('admin.payment.receipt.lower.grade.columbite.add', 'kg')->with([
            'type' => 'danger',
            'message' => 'Please select weight type.'
        ]);
    }

    public function payment_receipt_lower_grade_columbite_edit($id)
    {
        $finder = Crypt::decrypt($id);

        $lgcolumbitePayment = PaymentReceiptLowerGradeColumbite::find($finder);

        return view('admin.payment-receipt.edit_lower_grade_columbite', [
            'lgcolumbitePayment' => $lgcolumbitePayment
        ]);
    }

    public function payment_receipt_lower_grade_columbite_pound_update($id, Request $request)
    {
        $finder = Crypt::decrypt($id);

        $lgcolumbitePayment = PaymentReceiptLowerGradeColumbite::find($finder);

        if($request->save) 
        {
            $this->validate($request, [
                'supplier' => ['required', 'string', 'max:255'],
                'grade' => ['required', 'numeric'],
                'manager' => ['required', 'numeric'],
                'date_of_purchase' => ['required', 'date'],
                'receipt_no' => 'required|string',
            ]);

            $berating = BeratingCalculation::find($request->grade);
    
            if(!$berating)
            {
                return back()->with([
                    'type' => 'danger',
                    'message' => 'Admin yet to add this berating value, try again later.'
                ]); 
            }

            $response = [
                'grade' => $berating->grade,
                'price' => $berating->price,
                'unit_price' => $berating->unit_price
            ];

            $berate = json_encode($response);

            $manager = User::find($request->manager);

            if(!$manager)
            {
                return back()->with([
                    'type' => 'danger',
                    'message' => 'Manager not found in our database.'
                ]); 
            }

            if($request->poundweight == 'bag')
            {
                if($request->bag_pound == null)
                {
                    $bag_pounds = 0;
                } else {
                    $this->validate($request, [
                        'bag_pound' => ['required', 'numeric', 'max:79'],
                    ]);

                    $bag_pounds = $request->bag_pound;
                }

                $this->validate($request, [
                    'bags' => ['required', 'numeric'],
                    'percentage' => ['required', 'numeric', 'min:25'],
                ]);

                $analysis = AnalysisCalculation::get();

                foreach($analysis as $analyses)
                {
                    if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                    {
                        $myDollarRate = $analyses->dollar_rate;
                        $myExchangeRate = $analyses->exchange_rate;
                    }
                }

                $dollarRate = $myDollarRate ?? 0;
                $exchangeRate = $myExchangeRate ?? 0;

                if($dollarRate == 0 && $exchangeRate == 0)
                {
                    return back()->with([
                        'type' => 'danger',
                        'message' => 'Percentage Analysis entered not found in our database, try again.'
                    ]);
                }

                $res = [
                    'dollar_rate' => $dollarRate,
                    'exchange_rate' => $exchangeRate,
                ];
    
                $analysisRate = json_encode($res);

                if($bag_pounds < columbite_rate)
                {
                    $per = $request->percentage / 100;

                    $rateCalculation = $dollarRate * $exchangeRate;

                    $subTotal = $per * $rateCalculation;

                    $subPrice = $request->bags * columbite_rate + $request->bag_pound;
                    
                    $total = floor($subTotal) * $subPrice;

                    $totalPrice = number_format((float)$total, 0, '.', '');
                    
                    if (request()->hasFile('receipt_image')) 
                    {
                        $this->validate($request, [
                            'receipt_image' => 'required|mimes:jpeg,png,jpg'
                        ]);

                        $filename = uniqid(5).'-'.request()->receipt_image->getClientOriginalName();
                        if($lgcolumbitePayment->receipt_image) {
                            Storage::delete(str_replace("storage", "public", $lgcolumbitePayment->receipt_image));
                        }
                        request()->receipt_image->storeAs('payment_analysis', $filename, 'public');

                        $lgcolumbitePayment->update([
                            'type' => $request->type,
                            'supplier' => $request->supplier,
                            'staff' => $manager->id,
                            'grade' => $request->grade,
                            'bag' => $request->bags,
                            'pound' => $bag_pounds,
                            'total_in_pound' => $subPrice,
                            'berating_rate_list' => $berate,
                            'percentage_analysis' => $request->percentage,
                            'analysis_rate_list' => $analysisRate,
                            'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                            'date_of_purchase' => $request->date_of_purchase,
                            'receipt_no' => $request->receipt_no,
                            'receipt_image' => '/storage/payment_analysis/'.$filename
                        ]);
                    } else {
                        $lgcolumbitePayment->update([
                            'type' => $request->type,
                            'supplier' => $request->supplier,
                            'staff' => $manager->id,
                            'grade' => $request->grade,
                            'bag' => $request->bags,
                            'pound' => $bag_pounds,
                            'total_in_pound' => $subPrice,
                            'berating_rate_list' => $berate,
                            'percentage_analysis' => $request->percentage,
                            'analysis_rate_list' => $analysisRate,
                            'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                            'date_of_purchase' => $request->date_of_purchase,
                            'receipt_no' => $request->receipt_no,
                        ]);
                    }

                    $transaction = Transaction::where('accountant_process_id', $lgcolumbitePayment->id)->first();

                    if($transaction)
                    {
                        $transaction->update([
                            'amount' => $lgcolumbitePayment->price
                        ]);
                    }

                    Notification::create([
                        'to' => $lgcolumbitePayment->user_id,
                        'admin_id' => Auth::user()->id,
                        'title' => config('app.name'),
                        'body' => 'Admin has updated a payment receipt, with Receipt No:'.$lgcolumbitePayment->receipt_no
                    ]);
            
                    return back()->with([
                        'alertType' => 'success',
                        'back' => route('admin.payment.receipt.columbite.view', 'pound'),
                        'message' => 'Payment Receipt updated successfully'
                    ]);
                } else {
                    return back()->with([
                        'type' => 'danger',
                        'message' => 'kg should not be greater or equal to '.columbite_rate
                    ]);
                }
            } 

            if($request->poundweight == 'pound')
            {
                $this->validate($request, [
                    'pounds' => ['required', 'numeric']
                ]);
    
                $analysis = AnalysisCalculation::get();
    
                foreach($analysis as $analyses)
                {
                    if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                    {
                        $myDollarRate = $analyses->dollar_rate;
                        $myExchangeRate = $analyses->exchange_rate;
                    }
                }

                $dollarRate = $myDollarRate ?? 0;
                $exchangeRate = $myExchangeRate ?? 0;

                if($dollarRate == 0 && $exchangeRate == 0)
                {
                    return back()->with([
                        'type' => 'danger',
                        'message' => 'Percentage Analysis entered not found in our database, try again.'
                    ]);
                }

                $res = [
                    'dollar_rate' => $dollarRate,
                    'exchange_rate' => $exchangeRate,
                ];
    
                $analysisRate = json_encode($res);
    
                $per = $request->percentage / 100;
    
                $rateCalculation = $dollarRate * $exchangeRate;
    
                $subTotal = $per * $rateCalculation;
    
                $total = floor($subTotal) * $request->pounds;
    
                $totalPrice = number_format((float)$total, 0, '.', '');

                if (request()->hasFile('receipt_image')) 
                {
                    $this->validate($request, [
                        'receipt_image' => 'required|mimes:jpeg,png,jpg'
                    ]);

                    $filename = uniqid(5).'-'.request()->receipt_image->getClientOriginalName();
                    if($lgcolumbitePayment->receipt_image) {
                        Storage::delete(str_replace("storage", "public", $lgcolumbitePayment->receipt_image));
                    }
                    request()->receipt_image->storeAs('payment_analysis', $filename, 'public');

                    $lgcolumbitePayment->update([
                        'type' => $request->type,
                        'supplier' => $request->supplier,
                        'staff' => $manager->id,
                        'grade' => $request->grade,
                        'bag' => 0,
                        'pound' => $request->pounds,
                        'total_in_pound' => $request->pounds,
                        'berating_rate_list' => $berate,
                        'percentage_analysis' => $request->percentage,
                        'analysis_rate_list' => $analysisRate,
                        'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                        'date_of_purchase' => $request->date_of_purchase,
                        'receipt_no' => $request->receipt_no,
                        'receipt_image' => '/storage/payment_analysis/'.$filename
                    ]);
                } else {
                    $lgcolumbitePayment->update([
                        'type' => $request->type,
                        'supplier' => $request->supplier,
                        'staff' => $manager->id,
                        'grade' => $request->grade,
                        'bag' => 0,
                        'pound' => $request->pounds,
                        'total_in_pound' => $request->pounds,
                        'berating_rate_list' => $berate,
                        'percentage_analysis' => $request->percentage,
                        'analysis_rate_list' => $analysisRate,
                        'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                        'date_of_purchase' => $request->date_of_purchase,
                        'receipt_no' => $request->receipt_no
                    ]);
                }

                $transaction = Transaction::where('accountant_process_id', $lgcolumbitePayment->id)->first();

                if($transaction)
                {
                    $transaction->update([
                        'amount' => $lgcolumbitePayment->price
                    ]);
                }

                Notification::create([
                    'to' => $lgcolumbitePayment->user_id,
                    'admin_id' => Auth::user()->id,
                    'title' => config('app.name'),
                    'body' => 'Admin has updated a payment receipt, with Receipt No:'.$lgcolumbitePayment->receipt_no
                ]);
            
                return back()->with([
                    'alertType' => 'success',
                    'back' => route('admin.payment.receipt.columbite.view', 'pound'),
                    'message' => 'Payment Receipt updated successfully'
                ]);
            } 

            return back()->with([
                'type' => 'danger',
                'message' => 'Please select weight type.'
            ]);
        }

        $this->validate($request, [
            'grade' => ['required', 'numeric'],
        ]);

        $berating = BeratingCalculation::find($request->grade);

        if(!$berating)
        {
            return back()->with([
                'type' => 'danger',
                'message' => 'Admin yet to add this berating value, try again later.'
            ]); 
        }
       
        if($request->poundweight == 'bag')
        {
            if($request->bag_pound == null)
            {
                $bag_pounds = 0;
            } else {
                $this->validate($request, [
                    'bag_pound' => ['required', 'numeric', 'max:79'],
                ]);

                $bag_pounds = $request->bag_pound;
            }

            $this->validate($request, [
                'bags' => ['required', 'numeric'],
                'percentage' => ['required', 'numeric', 'min:25'],
            ]);

            $analysis = AnalysisCalculation::get();

            foreach($analysis as $analyses)
            {
                if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                {
                    $myDollarRate = $analyses->dollar_rate;
                    $myExchangeRate = $analyses->exchange_rate;
                }
            }

            $dollarRate = $myDollarRate ?? 0;
            $exchangeRate = $myExchangeRate ?? 0;

            if($dollarRate == 0 && $exchangeRate == 0)
            {
                return back()->with([
                    'type' => 'danger',
                    'message' => 'Percentage Analysis entered not found in our database, try again.'
                ]);
            }

            if($bag_pounds < columbite_rate)
            {
                $per = $request->percentage / 100;

                $rateCalculation = $dollarRate * $exchangeRate;

                $subTotal = $per * $rateCalculation;

                $subPrice = $request->bags * columbite_rate + $request->bag_pound;
                
                $total = floor($subTotal) * $subPrice;

                $totalPrice = number_format((float)$total, 0, '.', '');

                return back()->with([
                    'previewPrice' => 'success',
                    'message' => $this->replaceCharsInNumber($totalPrice, '0')
                ]);
            } else {
                return back()->with([
                    'type' => 'danger',
                    'message' => 'pound should not be greater or equal to '.columbite_rate
                ]);
            }
            
        } 

        if($request->poundweight == 'pound')
        {
            $this->validate($request, [
                'pounds' => ['required', 'numeric']
            ]);

            $analysis = AnalysisCalculation::get();

            foreach($analysis as $analyses)
            {
                if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                {
                    $myDollarRate = $analyses->dollar_rate;
                    $myExchangeRate = $analyses->exchange_rate;
                }
            }

            $dollarRate = $myDollarRate ?? 0;
            $exchangeRate = $myExchangeRate ?? 0;

            if($dollarRate == 0 && $exchangeRate == 0)
            {
                return back()->with([
                    'type' => 'danger',
                    'message' => 'Percentage Analysis entered not found in our database, try again.'
                ]);
            }

            $per = $request->percentage / 100;

            $rateCalculation = $dollarRate * $exchangeRate;

            $subTotal = $per * $rateCalculation;

            $total = floor($subTotal) * $request->pounds;

            $totalPrice = number_format((float)$total, 0, '.', '');

            return back()->with([
                'previewPrice' => 'success',
                'message' => $this->replaceCharsInNumber($totalPrice, '0')
            ]);
        } 

        return back()->with([
            'type' => 'danger',
            'message' => 'Please select weight type.'
        ]);
    }

    public function payment_receipt_lower_grade_columbite_kg_update($id, Request $request)
    {
        $finder = Crypt::decrypt($id);

        $lgcolumbitePayment = PaymentReceiptLowerGradeColumbite::find($finder);

        if($request->save) 
        {
            $this->validate($request, [
                'supplier' => ['required', 'string', 'max:255'],
                'grade' => ['required', 'numeric'],
                'manager' => ['required', 'numeric'],
                'date_of_purchase' => ['required', 'date'],
                'receipt_no' => 'required|string',
            ]);

            $berating = BeratingCalculation::find($request->grade);

            if(!$berating)
            {
                return back()->with([
                    'type' => 'danger',
                    'message' => 'Admin yet to add this berating value, try again later.'
                ]); 
            }

            $response = [
                'grade' => $berating->grade,
                'price' => $berating->price,
                'unit_price' => $berating->unit_price
            ];

            $berate = json_encode($response);

            $manager = User::find($request->manager);

            if(!$manager)
            {
                return back()->with([
                    'type' => 'danger',
                    'message' => 'Manager not found in our database.'
                ]); 
            }

            if($request->kgweight == 'bag')
            {
                if($request->bag_kg == null)
                {
                    $bag_kgs = 0;
                } else {
                    $this->validate($request, [
                        'bag_kg' => ['required', 'numeric', 'max:49'],
                    ]);

                    $bag_kgs = $request->bag_kg;
                }

                $this->validate($request, [
                    'bags' => ['required', 'numeric'],
                    'percentage' => ['required', 'numeric', 'min:25'],
                ]);

                $analysis = AnalysisCalculation::get();

                foreach($analysis as $analyses)
                {
                    if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                    {
                        $myDollarRate = $analyses->dollar_rate;
                        $myExchangeRate = $analyses->exchange_rate;
                    }
                }

                $dollarRate = $myDollarRate ?? 0;
                $exchangeRate = $myExchangeRate ?? 0;

                if($dollarRate == 0 && $exchangeRate == 0)
                {
                    return back()->with([
                        'type' => 'danger',
                        'message' => 'Percentage Analysis entered not found in our database, try again.'
                    ]);
                }

                $res = [
                    'dollar_rate' => $dollarRate,
                    'exchange_rate' => $exchangeRate,
                ];
    
                $analysisRate = json_encode($res);

                if($bag_kgs < rate)
                {
                    $per = $request->percentage / 100;

                    $rateCalculation = $dollarRate * $exchangeRate;

                    $subTotal = $per * $rateCalculation * fixed_rate;

                    $subPrice = $request->bags * rate + $request->bag_kg;
                    
                    $total = floor($subTotal) * $subPrice;

                    $totalPrice = number_format((float)$total, 0, '.', '');
                    
                    if (request()->hasFile('receipt_image')) 
                    {
                        $this->validate($request, [
                            'receipt_image' => 'required|mimes:jpeg,png,jpg'
                        ]);

                        $filename = uniqid(5).'-'.request()->receipt_image->getClientOriginalName();
                        if($lgcolumbitePayment->receipt_image) {
                            Storage::delete(str_replace("storage", "public", $lgcolumbitePayment->receipt_image));
                        }
                        request()->receipt_image->storeAs('payment_analysis', $filename, 'public');

                        $lgcolumbitePayment ->update([
                            'type' => $request->type,
                            'supplier' => $request->supplier,
                            'staff' => $manager->id,
                            'grade' => $request->grade,
                            'bag' => $request->bags,
                            'kg' => $bag_kgs,
                            'total_in_kg' => $subPrice,
                            'berating_rate_list' => $berate,
                            'percentage_analysis' => $request->percentage,
                            'analysis_rate_list' => $analysisRate,
                            'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                            'date_of_purchase' => $request->date_of_purchase,
                            'receipt_no' => $request->receipt_no,
                            'receipt_image' => '/storage/payment_analysis/'.$filename
                        ]);
                    } else {
                        $lgcolumbitePayment ->update([
                            'type' => $request->type,
                            'supplier' => $request->supplier,
                            'staff' => $manager->id,
                            'grade' => $request->grade,
                            'bag' => $request->bags,
                            'kg' => $bag_kgs,
                            'total_in_kg' => $subPrice,
                            'berating_rate_list' => $berate,
                            'percentage_analysis' => $request->percentage,
                            'analysis_rate_list' => $analysisRate,
                            'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                            'date_of_purchase' => $request->date_of_purchase,
                            'receipt_no' => $request->receipt_no,
                        ]);
                    }

                    $transaction = Transaction::where('accountant_process_id', $lgcolumbitePayment->id)->first();

                    if($transaction)
                    {
                        $transaction->update([
                            'amount' => $lgcolumbitePayment->price
                        ]);
                    }

                    Notification::create([
                        'to' => $lgcolumbitePayment->user_id,
                        'admin_id' => Auth::user()->id,
                        'title' => config('app.name'),
                        'body' => 'Admin has updated a payment receipt, with Receipt No:'.$lgcolumbitePayment->receipt_no
                    ]);
            
                    return back()->with([
                        'alertType' => 'success',
                        'back' => route('admin.payment.receipt.columbite.view', 'kg'),
                        'message' => 'Payment Receipt created successfully.'
                    ]);
                } else {
                    return back()->with([
                        'type' => 'danger',
                        'message' => 'kg should not be greater or equal to '.rate
                    ]);
                }
            } 

            if($request->kgweight == 'kg')
            {
                $this->validate($request, [
                    'kg' => ['required', 'numeric']
                ]);
    
                $analysis = AnalysisCalculation::get();
    
                foreach($analysis as $analyses)
                {
                    if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                    {
                        $myDollarRate = $analyses->dollar_rate;
                        $myExchangeRate = $analyses->exchange_rate;
                    }
                }

                $dollarRate = $myDollarRate ?? 0;
                $exchangeRate = $myExchangeRate ?? 0;

                if($dollarRate == 0 && $exchangeRate == 0)
                {
                    return back()->with([
                        'type' => 'danger',
                        'message' => 'Percentage Analysis entered not found in our database, try again.'
                    ]);
                }

                $res = [
                    'dollar_rate' => $dollarRate,
                    'exchange_rate' => $exchangeRate,
                ];
    
                $analysisRate = json_encode($res);

                $per = $request->percentage / 100;
    
                $rateCalculation = $dollarRate * $exchangeRate;
    
                $subTotal = $per * $rateCalculation * fixed_rate;
    
                $total = floor($subTotal) * $request->kg;
    
                $totalPrice = number_format((float)$total, 0, '.', '');

                if (request()->hasFile('receipt_image')) 
                {
                    $this->validate($request, [
                        'receipt_image' => 'required|mimes:jpeg,png,jpg'
                    ]);

                    $filename = uniqid(5).'-'.request()->receipt_image->getClientOriginalName();
                    if($lgcolumbitePayment->receipt_image) {
                        Storage::delete(str_replace("storage", "public", $lgcolumbitePayment->receipt_image));
                    }
                    request()->receipt_image->storeAs('payment_analysis', $filename, 'public');

                    $lgcolumbitePayment->updated([
                        'type' => $request->type,
                        'supplier' => $request->supplier,
                        'staff' => $manager->id,
                        'grade' => $request->grade,
                        'kg' => $request->kg,
                        'total_in_kg' => $request->kg,
                        'berating_rate_list' => $berate,
                        'percentage_analysis' => $request->percentage,
                        'analysis_rate_list' => $analysisRate,
                        'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                        'date_of_purchase' => $request->date_of_purchase,
                        'receipt_no' => $request->receipt_no,
                        'receipt_image' => '/storage/payment_analysis/'.$filename
                    ]);
                } else {
                    $lgcolumbitePayment->updated([
                        'type' => $request->type,
                        'supplier' => $request->supplier,
                        'staff' => $manager->id,
                        'grade' => $request->grade,
                        'kg' => $request->kg,
                        'total_in_kg' => $request->kg,
                        'berating_rate_list' => $berate,
                        'percentage_analysis' => $request->percentage,
                        'analysis_rate_list' => $analysisRate,
                        'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                        'date_of_purchase' => $request->date_of_purchase,
                        'receipt_no' => $request->receipt_no,
                    ]);
                }
        
                $transaction = Transaction::where('accountant_process_id', $lgcolumbitePayment->id)->first();

                if($transaction)
                {
                    $transaction->update([
                        'amount' => $lgcolumbitePayment->price
                    ]);
                }

                Notification::create([
                    'to' => $lgcolumbitePayment->user_id,
                    'admin_id' => Auth::user()->id,
                    'title' => config('app.name'),
                    'body' => 'Admin has updated a payment receipt, with Receipt No:'.$lgcolumbitePayment->receipt_no
                ]);
            
                return back()->with([
                    'alertType' => 'success',
                    'back' => route('admin.payment.receipt.columbite.view', 'kg'),
                    'message' => 'Payment Receipt created successfully.'
                ]);
            } 

            return back()->with([
                'type' => 'danger',
                'message' => 'Please select weight type.'
            ]);
        }

        $this->validate($request, [
            'grade' => ['required', 'numeric'],
        ]);

        $berating = BeratingCalculation::find($request->grade);

        if(!$berating)
        {
            return back()->with([
                'type' => 'danger',
                'message' => 'Admin yet to add this berating value, try again later.'
            ]); 
        }
       
        if($request->kgweight == 'bag')
        {
            if($request->bag_kg == null)
            {
                $bag_kgs = 0;
            } else {
                $this->validate($request, [
                    'bag_kg' => ['required', 'numeric', 'max:49'],
                ]);

                $bag_kgs = $request->bag_kg;
            }

            $this->validate($request, [
                'bags' => ['required', 'numeric'],
                'percentage' => ['required', 'numeric', 'min:25'],
            ]);

            $analysis = AnalysisCalculation::get();

            foreach($analysis as $analyses)
            {
                if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                {
                    $myDollarRate = $analyses->dollar_rate;
                    $myExchangeRate = $analyses->exchange_rate;
                }
            }

            $dollarRate = $myDollarRate ?? 0;
            $exchangeRate = $myExchangeRate ?? 0;

            if($dollarRate == 0 && $exchangeRate == 0)
            {
                return back()->with([
                    'type' => 'danger',
                    'message' => 'Percentage Analysis entered not found in our database, try again.'
                ]);
            }

            if($bag_kgs < rate)
            {
                $per = $request->percentage / 100;

                $rateCalculation = $dollarRate * $exchangeRate;

                $subTotal = $per * $rateCalculation * fixed_rate;

                $subPrice = $request->bags * rate + $request->bag_kg;
                
                $total = floor($subTotal) * $subPrice;

                $totalPrice = number_format((float)$total, 0, '.', '');

                return back()->with([
                    'previewPrice' => 'success',
                    'message' => $this->replaceCharsInNumber($totalPrice, '0')
                ]);
            } else {
                return back()->with([
                    'type' => 'danger',
                    'message' => 'kg should not be greater or equal to '.rate
                ]);
            }
            
        } 

        if($request->kgweight == 'kg')
        {
            $this->validate($request, [
                'kg' => ['required', 'numeric']
            ]);

            $analysis = AnalysisCalculation::get();

            foreach($analysis as $analyses)
            {
                if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                {
                    $myDollarRate = $analyses->dollar_rate;
                    $myExchangeRate = $analyses->exchange_rate;
                }
            }

            $dollarRate = $myDollarRate ?? 0;
            $exchangeRate = $myExchangeRate ?? 0;

            if($dollarRate == 0 && $exchangeRate == 0)
            {
                return back()->with([
                    'type' => 'danger',
                    'message' => 'Percentage Analysis entered not found in our database, try again.'
                ]);
            }

            $per = $request->percentage / 100;

            $rateCalculation = $dollarRate * $exchangeRate;

            $subTotal = $per * $rateCalculation * fixed_rate;

            $total = floor($subTotal) * $request->kg;

            $totalPrice = number_format((float)$total, 0, '.', '');

            return back()->with([
                'previewPrice' => 'success',
                'message' => $this->replaceCharsInNumber($totalPrice, '0')
            ]);
        } 

        return back()->with([
            'type' => 'danger',
            'message' => 'Please select weight type.'
        ]);
    }

    public function payment_receipt_lower_grade_columbite_delete($id, $type)
    {
        $finder = Crypt::decrypt($id);

        $lgcolumbitePayment = PaymentReceiptLowerGradeColumbite::find($finder);

        $transaction = Transaction::where('accountant_process_id', $lgcolumbitePayment->id)->first();

        if($transaction)
        {
            $transaction->delete();
        }

        if($lgcolumbitePayment->receipt_image) {
            Storage::delete(str_replace("storage", "public", $lgcolumbitePayment->receipt_image));
        }

        $lgcolumbitePayment->delete();

        return redirect()->route('admin.payment.receipt.columbite.view', $type)->with([
            'alertType' => 'success',
            'message' => 'Payment receipt deleted successfully!'
        ]);
    }

    // Expenses
    public function expenses(Request $request)
    {
        if($request->start_date == null && $request->end_date == null)
        {
            $expenses = Expenses::latest()->get();
        } else {
            $expenses = Expenses::latest()->whereBetween('date', [$request->start_date, $request->end_date])->get();
        }

        return view('admin.expenses', [
            'expenses' => $expenses,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date
        ]);
    }

    public function update_expense($id, Request $request)
    {
        $this->validate($request, [
            'payment_source' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric'],
            'supplier' => ['required', 'numeric'],
            'date' => ['required', 'date'],
        ]);

        $finder = Crypt::decrypt($id);

        $expense = Expenses::find($finder);

        $transaction = Transaction::where('accountant_process_id', $expense->id)->first();

        $supplier = User::find($request->supplier);

        if(!$supplier)
        {
            return back()->with([
                'type' => 'danger',
                'message' => 'Supplier not found in our database.'
            ]);
        }

        if($request->amount == $expense->amount)
        {
            if (request()->hasFile('receipt')) 
            {
                $this->validate($request, [
                    'receipt' => 'required|mimes:jpeg,png,jpg'
                ]);
                
                $filename = request()->receipt->getClientOriginalName();

                if($expense->receipt) {
                    Storage::delete(str_replace("storage", "public", $expense->receipt));
                }

                request()->receipt->storeAs('expenses_receipts', $filename, 'public');

                $expense->update([
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
                $expense->update([
                    'supplier' => $supplier->id,
                    'payment_source' => $request->payment_source,
                    'category' => $request->category,
                    'description' => $request->description,
                    'amount' => $request->amount,
                    'date' => $request->date,
                    'recurring_expense' => $request->recurring_expense
                ]);
            }

            Notification::create([
                'to' => $expense->user_id,
                'admin_id' => Auth::user()->id,
                'title' => config('app.name'),
                'body' => 'Admin has updated an expense added by you with catgory - '.$expense->category
            ]);

            return back()->with([
                'alertType' => 'success',
                'message' => 'Expense updated successfully!'
            ]);
        } 

        if($request->amount < $expense->amount)
        {
            if (request()->hasFile('receipt')) 
            {
                $this->validate($request, [
                    'receipt' => 'required|mimes:jpeg,png,jpg'
                ]);
                
                $filename = request()->receipt->getClientOriginalName();

                if($expense->receipt) {
                    Storage::delete(str_replace("storage", "public", $expense->receipt));
                }

                request()->receipt->storeAs('expenses_receipts', $filename, 'public');
                
                $expense->update([
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
                $expense->update([
                    'supplier' => $supplier->id,
                    'payment_source' => $request->payment_source,
                    'category' => $request->category,
                    'description' => $request->description,
                    'amount' => $request->amount,
                    'date' => $request->date,
                    'recurring_expense' => $request->recurring_expense
                ]);
            }
    
            $transaction->update([
                'amount' => $expense->amount,
            ]);

            Notification::create([
                'to' => $expense->user_id,
                'admin_id' => Auth::user()->id,
                'title' => config('app.name'),
                'body' => 'Admin has updated an expense added by you with catgory - '.$expense->category
            ]);
    
            return back()->with([
                'alertType' => 'success',
                'message' => 'Expense updated successfully!'
            ]);
        }

        if (request()->hasFile('receipt')) 
        {
            $this->validate($request, [
                'receipt' => 'required|mimes:jpeg,png,jpg'
            ]);
            
            $filename = request()->receipt->getClientOriginalName();

            if($expense->receipt) {
                Storage::delete(str_replace("storage", "public", $expense->receipt));
            }

            request()->receipt->storeAs('expenses_receipts', $filename, 'public');

            $expense->update([
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
            $expense->update([
                'supplier' => $supplier->id,
                'payment_source' => $request->payment_source,
                'category' => $request->category,
                'description' => $request->description,
                'amount' => $request->amount,
                'date' => $request->date,
                'recurring_expense' => $request->recurring_expense
            ]);
        }

        $transaction->update([
            'amount' => $expense->amount,
        ]);

        return back()->with([
            'alertType' => 'success',
            'message' => 'Expense updated successfully!'
        ]);
    }

    public function delete_expense($id)
    {
        $finder = Crypt::decrypt($id);

        $expense = Expenses::find($finder);

        $transaction = Transaction::where('accountant_process_id', $expense->id)->first();

        if($transaction)
        {
            $transaction->delete();
        }

        if($expense->receipt) {
            Storage::delete(str_replace("storage", "public", $expense->receipt));
        }

        $expense->delete();

        return back()->with([
            'alertType' => 'success',
            'message' => 'Expense deleted successfully!'
        ]);
    }

    // Weekly Analysis
    public function weekly_analysis_tin_pound(Request $request)
    {
        if($request->start_date == null && $request->end_date == null && $request->manager == null)
        {
            $tinPayment = PaymentReceiptTin::latest()->where('type', 'pound')->get();
        } elseif($request->start_date !== null && $request->end_date !== null && $request->manager == null)
        {
            $tinPayment = PaymentReceiptTin::latest()->where('type', 'pound')->whereBetween('date_of_purchase', [$request->start_date, $request->end_date])->get();
        } elseif($request->start_date == null && $request->end_date == null && $request->manager !== null)
        {
            $tinPayment = PaymentReceiptTin::latest()->where('type', 'pound')->where('staff', $request->manager)->get();
        } else {
            $tinPayment = PaymentReceiptTin::latest()->where('type', 'pound')->where('staff', $request->manager)->whereBetween('date_of_purchase', [$request->start_date, $request->end_date])->get();
        }

        if($tinPayment->isEmpty())
        {
            $analysis = [];

        } else {
            
            $beratingCalculation = BeratingCalculation::latest()->get();

            foreach($tinPayment as $tinpound)
            {
                $beratingpayment = BeratingCalculation::find($tinpound->grade);

                foreach($beratingCalculation as $berating)
                {
                    if($berating->grade == $beratingpayment->grade)
                    {
                        $data[] = ['date' => $tinpound->date_of_purchase, 'berating' => $berating->grade, 'total' => $tinpound->total_in_pound];

                        $analysis = array_values(array_unique($data, 0));
                                    
                        rsort($analysis);
                    }
                }
            }
        }

        // Calculation Starts
        if($request->start_date == null && $request->end_date == null && $request->manager == null)
        {
            $result =  PaymentReceiptTin::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_tins.grade')->latest()->where('payment_receipt_tins.type', 'pound')  
                                ->get(['payment_receipt_tins.date_of_purchase', 'payment_receipt_tins.total_in_pound', 'berating_calculations.grade', 'payment_receipt_tins.created_at', 'payment_receipt_tins.updated_at']);
        } elseif($request->start_date !== null && $request->end_date !== null && $request->manager == null)
        {
            $result =  PaymentReceiptTin::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_tins.grade')->latest()->where('payment_receipt_tins.type', 'pound')  
                                ->whereBetween('payment_receipt_tins.date_of_purchase', [$request->start_date, $request->end_date])
                                ->get(['payment_receipt_tins.date_of_purchase', 'payment_receipt_tins.total_in_pound', 'berating_calculations.grade', 'payment_receipt_tins.created_at', 'payment_receipt_tins.updated_at']);
        } elseif($request->start_date == null && $request->end_date == null && $request->manager !== null)
        {
            $result =  PaymentReceiptTin::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_tins.grade')->latest()->where('payment_receipt_tins.type', 'pound')  
                                ->where('payment_receipt_tins.staff', $request->manager)
                                ->get(['payment_receipt_tins.date_of_purchase', 'payment_receipt_tins.total_in_pound', 'berating_calculations.grade', 'payment_receipt_tins.created_at', 'payment_receipt_tins.updated_at']);
        } else {
            $result =  PaymentReceiptTin::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_tins.grade')->latest()->where('payment_receipt_tins.type', 'pound')  
                                ->where('payment_receipt_tins.staff', $request->manager)->whereBetween('payment_receipt_tins.date_of_purchase', [$request->start_date, $request->end_date])
                                ->get(['payment_receipt_tins.date_of_purchase', 'payment_receipt_tins.total_in_pound', 'berating_calculations.grade', 'payment_receipt_tins.created_at', 'payment_receipt_tins.updated_at']);
        }

        if($result->isEmpty())
        {
            $totalBags = [
                'bags' => 0,
                'pounds' => 0
            ];

            $totalBags18 = [
                'bags' => 0,
                'pounds' => 0
            ];

            $totalBags19 = [
                'bags' => 0,
                'pounds' => 0
            ];

            $totalBags20 = [
                'bags' => 0,
                'pounds' => 0
            ];

            $totalBeratingAverage = 0;

            $data = ['18M' => $totalBags18, '19M' => $totalBags19, '20M' => $totalBags20, 'TOTAL_BAGS' => $totalBags, 'AB' => $totalBeratingAverage];

        } else {
            $sum188 = $result->where('grade', '18.8')->sum('total_in_pound');
            $sum189 = $result->where('grade', '18.9')->sum('total_in_pound');
            $sum190 = $result->where('grade', '19.0')->sum('total_in_pound');
            $sum191 = $result->where('grade', '19.1')->sum('total_in_pound');
            $sum192 = $result->where('grade', '19.2')->sum('total_in_pound');
            $sum193 = $result->where('grade', '19.3')->sum('total_in_pound');
            $sum194 = $result->where('grade', '19.4')->sum('total_in_pound');
            $sum195 = $result->where('grade', '19.5')->sum('total_in_pound');
            $sum196 = $result->where('grade', '19.6')->sum('total_in_pound');
            $sum197 = $result->where('grade', '19.7')->sum('total_in_pound');
            $sum198 = $result->where('grade', '19.8')->sum('total_in_pound');
            $sum199 = $result->where('grade', '19.9')->sum('total_in_pound');
            $sum200 = $result->where('grade', '20.0')->sum('total_in_pound');
            $sum201 = $result->where('grade', '20.1')->sum('total_in_pound');
            $sum202 = $result->where('grade', '20.2')->sum('total_in_pound');
            $sum203 = $result->where('grade', '20.3')->sum('total_in_pound');
            $sum204 = $result->where('grade', '20.4')->sum('total_in_pound');
            $sum205 = $result->where('grade', '20.5')->sum('total_in_pound');

            $totalPound = $sum188 + $sum189 + $sum190 + $sum191 + $sum192 + $sum193 + $sum194 + $sum195 + $sum196 + $sum197 + $sum198 + $sum199 + $sum200 + $sum201 + $sum202 + $sum203 + $sum204 + $sum205;

            $b188 = $sum188 * 18.8; $b189 = $sum189 * 18.9; $b190 = $sum190 * 19.0; $b191 = $sum191 * 19.1; $b192 = $sum192 * 19.2; $b193 = $sum193 * 19.3; $b194 = $sum194 * 19.4; $b195 = $sum195 * 19.5;  
            $b196 = $sum196 * 19.6; $b197 = $sum197 * 19.7;  $b198 = $sum198 * 19.8; $b199 = $sum199 * 19.9; $b200 = $sum200 * 20.0; $b201 = $sum201 * 20.1; $b202 = $sum202 * 20.2; $b203 = $sum203 * 20.3; 
            $b204 = $sum204 * 20.4; $b205 = $sum205 * 20.5;

            $totalBerating =  $b188 + $b189 + $b190 + $b191 + $b192 + $b193 + $b194 + $b195 + $b196 + $b197 + $b198 + $b199 + $b200 + $b201 +  $b202 + $b203 + $b204 + $b205;

            $beratingAverage = $totalBerating / $totalPound;

            $totalBeratingAverage = number_format((float)$beratingAverage, 2, '.', '');

            $bags = $totalPound / 70;
            $str_arr = explode('.',$bags);
            $str = str_replace($str_arr[0], '0.', $str_arr[0]);
            $strP = $str_arr[1] ?? 0;
            $substr = $str.''.$strP;
            $answer = $substr * 70;
            $totalBags = [
                'bags' => $str_arr[0],
                'pounds' => number_format((float)$answer, 0, '.', '')
            ];
  
            $bag18 = $sum188 + $sum189;
            $bag19 = $sum190 + $sum191 + $sum192 + $sum193;
            $bag20 = $sum194 + $sum195 + $sum196 + $sum197 + $sum198 + $sum199 + $sum200 + $sum201 + $sum202 + $sum203 + $sum204 + $sum205;

            if($bag18 > 0)
            {
                $bag18Bags = $bag18 / 70;
                $str_arr18 = explode('.',$bag18Bags);
                $str18 = str_replace($str_arr18[0], '0.', $str_arr18[0]);
                $strPound = $str_arr18[1] ?? 0;
                $substr18 = $str18.''.$strPound;
                $answer18 = $substr18 * 70;
                $totalBags18 = [
                    'bags' => $str_arr18[0],
                    'pounds' => number_format((float)$answer18, 0, '.', '')
                ];
            } else {
                $totalBags18 = [
                    'bags' => 0,
                    'pounds' => 0
                ];
            }

            if($bag19 > 0)
            {
                $bag19Bags = $bag19 / 70;
                $str_arr19 = explode('.',$bag19Bags);
                $str19 = str_replace($str_arr19[0], '0.', $str_arr19[0]);
                $strPound = $str_arr19[1] ?? 0;
                $substr19 = $str19.''.$strPound;
                $answer19 = $substr19 * 70;
                $totalBags19 = [
                    'bags' => $str_arr19[0],
                    'pounds' => number_format((float)$answer19, 0, '.', '')
                ];
            } else {
                $totalBags19 = [
                    'bags' => 0,
                    'pounds' => 0
                ];
            }

            if($bag20 > 0)
            {
                $bag20Bags = $bag20 / 70;
                $str_arr20 = explode('.',$bag20Bags);
                $str20 = str_replace($str_arr20[0], '0.', $str_arr20[0]);
                $strPound = $str_arr20[1] ?? 0;
                $substr20 = $str20.''.$strPound;
                $answer20 = $substr20 * 70;
                $totalBags20 = [
                    'bags' => $str_arr20[0],
                    'pounds' => number_format((float)$answer20, 0, '.', '')
                ];
            } else {
                $totalBags20 = [
                    'bags' => 0,
                    'pounds' => 0
                ];
            }
            
            $data = ['18M' => $totalBags18, '19M' => $totalBags19, '20M' => $totalBags20, 'TOTAL_BAGS' => $totalBags, 'AB' => $totalBeratingAverage];
        }

        if (request()->ajax()) {
            return DataTables::of($analysis)->make(true);
        }

        return view('admin.weekly_analysis.tin_pound', [
            'analysis' => $analysis,
            'data' => $data,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'manager' => $request->manager
        ]);
    }

    public function weekly_analysis_tin_kg(Request $request)
    {
        if($request->start_date == null && $request->end_date == null && $request->manager == null)
        {
            $tinPayment = PaymentReceiptTin::latest()->where('type', 'kg')->get();
        } elseif($request->start_date !== null && $request->end_date !== null && $request->manager == null)
        {
            $tinPayment = PaymentReceiptTin::latest()->where('type', 'kg')->whereBetween('date_of_purchase', [$request->start_date, $request->end_date])->get();
        } elseif($request->start_date == null && $request->end_date == null && $request->manager !== null)
        {
            $tinPayment = PaymentReceiptTin::latest()->where('type', 'kg')->where('staff', $request->manager)->get();
        } else {
            $tinPayment = PaymentReceiptTin::latest()->where('type', 'kg')->where('staff', $request->manager)->whereBetween('date_of_purchase', [$request->start_date, $request->end_date])->get();
        }

        if($tinPayment->isEmpty())
        {
            $analysis = [];

        } else {
            
            $beratingCalculation = BeratingCalculation::latest()->get();

            foreach($tinPayment as $tinpound)
            {
                $beratingpayment = BeratingCalculation::find($tinpound->grade);

                foreach($beratingCalculation as $berating)
                {
                    if($berating->grade == $beratingpayment->grade)
                    {
                        $data[] = ['date' => $tinpound->date_of_purchase, 'berating' => $berating->grade, 'total' => $tinpound->total_in_kg];

                        $analysis = array_values(array_unique($data, 0));
                                    
                        rsort($analysis);
                    }
                }
            }
        }

        // Calculation Starts
        if($request->start_date == null && $request->end_date == null && $request->manager == null)
        {
            $result =  PaymentReceiptTin::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_tins.grade')->latest()->where('payment_receipt_tins.type', 'kg')  
                                ->get(['payment_receipt_tins.date_of_purchase', 'payment_receipt_tins.total_in_kg', 'payment_receipt_tins.percentage_analysis', 'berating_calculations.grade', 'payment_receipt_tins.created_at', 'payment_receipt_tins.updated_at']);
        } elseif($request->start_date !== null && $request->end_date !== null && $request->manager == null)
        {
            $result =  PaymentReceiptTin::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_tins.grade')->latest()->where('payment_receipt_tins.type', 'kg')  
                                ->whereBetween('payment_receipt_tins.date_of_purchase', [$request->start_date, $request->end_date])
                                ->get(['payment_receipt_tins.date_of_purchase', 'payment_receipt_tins.total_in_kg', 'payment_receipt_tins.percentage_analysis', 'berating_calculations.grade', 'payment_receipt_tins.created_at', 'payment_receipt_tins.updated_at']);
        } elseif($request->start_date == null && $request->end_date == null && $request->manager !== null)
        { 
            $result =  PaymentReceiptTin::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_tins.grade')->latest()->where('payment_receipt_tins.type', 'kg')  
                                ->where('payment_receipt_tins.staff', $request->manager)
                                ->get(['payment_receipt_tins.date_of_purchase', 'payment_receipt_tins.total_in_kg', 'payment_receipt_tins.percentage_analysis', 'berating_calculations.grade', 'payment_receipt_tins.created_at', 'payment_receipt_tins.updated_at']);
        } else {
            $result =  PaymentReceiptTin::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_tins.grade')->latest()->where('payment_receipt_tins.type', 'kg')  
                                ->where('payment_receipt_tins.staff', $request->manager)->whereBetween('payment_receipt_tins.date_of_purchase', [$request->start_date, $request->end_date])
                                ->get(['payment_receipt_tins.date_of_purchase', 'payment_receipt_tins.total_in_kg', 'payment_receipt_tins.percentage_analysis', 'berating_calculations.grade', 'payment_receipt_tins.created_at', 'payment_receipt_tins.updated_at']);
        }
        
        if($result->isEmpty())
        {
            $totalBags = [
                'bags' => 0,
                'kgs' => 0
            ];

            $totalBags18 = [
                'bags' => 0,
                'kgs' => 0
            ];

            $totalBags19 = [
                'bags' => 0,
                'kgs' => 0
            ];

            $totalBags20 = [
                'bags' => 0,
                'kgs' => 0
            ];

            $totalBeratingAverage = 0;

            $totalPercentageAverage = 0;

            $data = ['18M' => $totalBags18, '19M' => $totalBags19, '20M' => $totalBags20, 'TOTAL_BAGS' => $totalBags, 'AB' => $totalBeratingAverage, 'AP' => $totalPercentageAverage];

        } else {
            // Total Kg and Total Average Berating
            $sum188 = $result->where('grade', '18.8')->sum('total_in_kg');
            $sum189 = $result->where('grade', '18.9')->sum('total_in_kg');
            $sum190 = $result->where('grade', '19.0')->sum('total_in_kg');
            $sum191 = $result->where('grade', '19.1')->sum('total_in_kg');
            $sum192 = $result->where('grade', '19.2')->sum('total_in_kg');
            $sum193 = $result->where('grade', '19.3')->sum('total_in_kg');
            $sum194 = $result->where('grade', '19.4')->sum('total_in_kg');
            $sum195 = $result->where('grade', '19.5')->sum('total_in_kg');
            $sum196 = $result->where('grade', '19.6')->sum('total_in_kg');
            $sum197 = $result->where('grade', '19.7')->sum('total_in_kg');
            $sum198 = $result->where('grade', '19.8')->sum('total_in_kg');
            $sum199 = $result->where('grade', '19.9')->sum('total_in_kg');
            $sum200 = $result->where('grade', '20.0')->sum('total_in_kg');
            $sum201 = $result->where('grade', '20.1')->sum('total_in_kg');
            $sum202 = $result->where('grade', '20.2')->sum('total_in_kg');
            $sum203 = $result->where('grade', '20.3')->sum('total_in_kg');
            $sum204 = $result->where('grade', '20.4')->sum('total_in_kg');
            $sum205 = $result->where('grade', '20.5')->sum('total_in_kg');

            $totalKg = $sum188 + $sum189 + $sum190 + $sum191 + $sum192 + $sum193 + $sum194 + $sum195 + $sum196 + $sum197 + $sum198 + $sum199 + $sum200 + $sum201 + $sum202 + $sum203 + $sum204 + $sum205;

            $b188 = $sum188 * 18.8; $b189 = $sum189 * 18.9; $b190 = $sum190 * 19.0; $b191 = $sum191 * 19.1; $b192 = $sum192 * 19.2; $b193 = $sum193 * 19.3; $b194 = $sum194 * 19.4; $b195 = $sum195 * 19.5;  
            $b196 = $sum196 * 19.6; $b197 = $sum197 * 19.7;  $b198 = $sum198 * 19.8; $b199 = $sum199 * 19.9; $b200 = $sum200 * 20.0; $b201 = $sum201 * 20.1; $b202 = $sum202 * 20.2; $b203 = $sum203 * 20.3; 
            $b204 = $sum204 * 20.4; $b205 = $sum205 * 20.5;

            $totalBerating =  $b188 + $b189 + $b190 + $b191 + $b192 + $b193 + $b194 + $b195 + $b196 + $b197 + $b198 + $b199 + $b200 + $b201 +  $b202 + $b203 + $b204 + $b205;

            $beratingAverage = $totalBerating / $totalKg;

            $totalBeratingAverage = number_format((float)$beratingAverage, 2, '.', '');

            // Percentage Average
            $percentage188 = $result->where('grade', '18.8');
            if($percentage188->isEmpty())
            {
                $sumPercentage188[] = 0;
            } else {
                foreach($percentage188 as $per188)
                {
                    $sumPercentage188[] = $per188->percentage_analysis * $per188->total_in_kg;
                }
            }
            $percentage189 = $result->where('grade', '18.9');
            if($percentage189->isEmpty())
            {
                $sumPercentage189[] = 0;
            } else {
                foreach($percentage189 as $per189)
                {
                    $sumPercentage189[] = $per189->percentage_analysis * $per189->total_in_kg;
                }
            }
            $percentage190 = $result->where('grade', '19.0');
            if($percentage190->isEmpty())
            {
                $sumPercentage190[] = 0;
            } else {
                foreach($percentage190 as $per190)
                {
                    $sumPercentage190[] = $per190->percentage_analysis * $per190->total_in_kg;
                }
            }
            $percentage191 = $result->where('grade', '19.1');
            if($percentage191->isEmpty())
            {
                $sumPercentage191[] = 0;
            } else {
                foreach($percentage191 as $per191)
                {
                    $sumPercentage191[] = $per191->percentage_analysis * $per191->total_in_kg;
                }
            }
            $percentage192 = $result->where('grade', '19.2');
            if($percentage192->isEmpty())
            {
                $sumPercentage192[] = 0;
            } else {
                foreach($percentage192 as $per192)
                {
                    $sumPercentage192[] = $per192->percentage_analysis * $per192->total_in_kg;
                }
            }
            $percentage193 = $result->where('grade', '19.3');
            if($percentage193->isEmpty())
            {
                $sumPercentage193[] = 0;
            } else {
                foreach($percentage193 as $per193)
                {
                    $sumPercentage193[] = $per193->percentage_analysis * $per193->total_in_kg;
                }
            }
            $percentage194 = $result->where('grade', '19.4');
            if($percentage194->isEmpty())
            {
                $sumPercentage194[] = 0;
            } else {
                foreach($percentage194 as $per194)
                {
                    $sumPercentage194[] = $per194->percentage_analysis * $per194->total_in_kg;
                }
            }
            $percentage195 = $result->where('grade', '19.5');
            if($percentage195->isEmpty())
            {
                $sumPercentage195[] = 0;
            } else {
                foreach($percentage195 as $per195)
                {
                    $sumPercentage195[] = $per195->percentage_analysis * $per195->total_in_kg;
                }
            }
            $percentage196 = $result->where('grade', '19.6');
            if($percentage196->isEmpty())
            {
                $sumPercentage196[] = 0;
            } else {
                foreach($percentage196 as $per196)
                {
                    $sumPercentage196[] = $per196->percentage_analysis * $per196->total_in_kg;
                }
            }
            $percentage197 = $result->where('grade', '19.7');
            if($percentage197->isEmpty())
            {
                $sumPercentage197[] = 0;
            } else {
                foreach($percentage197 as $per197)
                {
                    $sumPercentage197[] = $per197->percentage_analysis * $per197->total_in_kg;
                }
            }
            $percentage198 = $result->where('grade', '19.8');
            if($percentage198->isEmpty())
            {
                $sumPercentage198[] = 0;
            } else {
                foreach($percentage198 as $per198)
                {
                    $sumPercentage198[] = $per198->percentage_analysis * $per198->total_in_kg;
                }
            }
            $percentage199 = $result->where('grade', '19.9');
            if($percentage199->isEmpty())
            {
                $sumPercentage199[] = 0;
            } else {
                foreach($percentage199 as $per199)
                {
                    $sumPercentage199[] = $per199->percentage_analysis * $per199->total_in_kg;
                }
            }
            $percentage200 = $result->where('grade', '20.0');
            if($percentage200->isEmpty())
            {
                $sumPercentage200[] = 0;
            } else {
                foreach($percentage200 as $per200)
                {
                    $sumPercentage200[] = $per200->percentage_analysis * $per200->total_in_kg;
                }
            }
            $percentage201 = $result->where('grade', '20.1');
            if($percentage201->isEmpty())
            {
                $sumPercentage201[] = 0;
            } else {
                foreach($percentage201 as $per201)
                {
                    $sumPercentage201[] = $per201->percentage_analysis * $per201->total_in_kg;
                }
            }
            $percentage202 = $result->where('grade', '20.2');
            if($percentage202->isEmpty())
            {
                $sumPercentage202[] = 0;
            } else {
                foreach($percentage202 as $per202)
                {
                    $sumPercentage202[] = $per202->percentage_analysis * $per202->total_in_kg;
                }
            }
            $percentage203 = $result->where('grade', '20.3');
            if($percentage203->isEmpty())
            {
                $sumPercentage203[] = 0;
            } else {
                foreach($percentage203 as $per203)
                {
                    $sumPercentage203[] = $per203->percentage_analysis * $per203->total_in_kg;
                }
            }
            $percentage204 = $result->where('grade', '20.4');
            if($percentage204->isEmpty())
            {
                $sumPercentage204[] = 0;
            } else {
                foreach($percentage204 as $per204)
                {
                    $sumPercentage204[] = $per204->percentage_analysis * $per204->total_in_kg;
                }
            }
            $percentage205 = $result->where('grade', '20.5');
            if($percentage205->isEmpty())
            {
                $sumPercentage205[] = 0;
            } else {
                foreach($percentage205 as $per205)
                {
                    $sumPercentage205[] = $per205->percentage_analysis * $per205->total_in_kg;
                }
            }

            $totalPercentage188 = array_sum($sumPercentage188); 
            $totalPercentage189 = array_sum($sumPercentage189); 
            $totalPercentage190 = array_sum($sumPercentage190);
            $totalPercentage191 = array_sum($sumPercentage191);
            $totalPercentage192 = array_sum($sumPercentage192);
            $totalPercentage193 = array_sum($sumPercentage193);
            $totalPercentage194 = array_sum($sumPercentage194); 
            $totalPercentage195 = array_sum($sumPercentage195); 
            $totalPercentage196 = array_sum($sumPercentage196);
            $totalPercentage197 = array_sum($sumPercentage197);
            $totalPercentage198 = array_sum($sumPercentage198); 
            $totalPercentage199 = array_sum($sumPercentage199);
            $totalPercentage200 = array_sum($sumPercentage200); 
            $totalPercentage201 = array_sum($sumPercentage201);
            $totalPercentage202 = array_sum($sumPercentage202);
            $totalPercentage203 = array_sum($sumPercentage203);
            $totalPercentage204 = array_sum($sumPercentage204); 
            $totalPercentage205 = array_sum($sumPercentage205);

            $totalPercentage = $totalPercentage188 + $totalPercentage189 + $totalPercentage190 + $totalPercentage191 + $totalPercentage192 + $totalPercentage193 + $totalPercentage194 + $totalPercentage195 + $totalPercentage196 + $totalPercentage197 + $totalPercentage198 + $totalPercentage199 + $totalPercentage200 + $totalPercentage201 + $totalPercentage202 + $totalPercentage203 + $totalPercentage204 + $totalPercentage205;

            // return $totalPercentage;

            // $p188 = $sum188 * $percentage188; $p189 = $sum189 * $percentage189; $p190 = $sum190 * $percentage190; $p191 = $sum191 * $percentage191; $p192 = $sum192 * $percentage192; $p193 = $sum193 * $percentage193; $p194 = $sum194 * $percentage194; $p195 = $sum195 * $percentage195;  
            // $p196 = $sum196 * $percentage196; $p197 = $sum197 * $percentage197;  $p198 = $sum198 * $percentage198; $p199 = $sum199 * $percentage199; $p200 = $sum200 * $percentage200; $p201 = $sum201 * $percentage201; $p202 = $sum202 * $percentage202; $p203 = $sum203 * $percentage203; 
            // $p204 = $sum204 * $percentage204; $p205 = $sum205 * $percentage205;

            // $totalPercentage =  $p188 + $p189 + $p190 + $p191 + $p192 + $p193 + $p194 + $p195 + $p196 + $p197 + $p198 + $p199 + $p200 + $p201 +  $p202 + $p203 + $p204 + $p205;

            $percentageAverage = $totalPercentage / $totalKg;

            $totalPercentageAverage = number_format((float)$percentageAverage, 2, '.', '');

            $bags = $totalKg / 50;
            $str_arr = explode('.',$bags);
            $str = str_replace($str_arr[0], '0.', $str_arr[0]);
            $strP = $str_arr[1] ?? 0;
            $substr = $str.''.$strP;
            $answer = $substr * 50;
            $totalBags = [
                'bags' => $str_arr[0],
                'kgs' => number_format((float)$answer, 0, '.', '')
            ];
  
            $bag18 = $sum188 + $sum189;
            $bag19 = $sum190 + $sum191 + $sum192 + $sum193;
            $bag20 = $sum194 + $sum195 + $sum196 + $sum197 + $sum198 + $sum199 + $sum200 + $sum201 + $sum202 + $sum203 + $sum204 + $sum205;

            if($bag18 > 0)
            {
                $bag18Bags = $bag18 / 50;
                $str_arr18 = explode('.',$bag18Bags);
                $str18 = str_replace($str_arr18[0], '0.', $str_arr18[0]);
                $strPound = $str_arr18[1] ?? 0;
                $substr18 = $str18.''.$strPound;
                $answer18 = $substr18 * 50;
                $totalBags18 = [
                    'bags' => $str_arr18[0],
                    'kgs' => number_format((float)$answer18, 0, '.', '')
                ];
            } else {
                $totalBags18 = [
                    'bags' => 0,
                    'kgs' => 0
                ];
            }

            if($bag19 > 0)
            {
                $bag19Bags = $bag19 / 50;
                $str_arr19 = explode('.',$bag19Bags);
                $str19 = str_replace($str_arr19[0], '0.', $str_arr19[0]);
                $strPound = $str_arr19[1] ?? 0;
                $substr19 = $str19.''.$strPound;
                $answer19 = $substr19 * 50;
                $totalBags19 = [
                    'bags' => $str_arr19[0],
                    'kgs' => number_format((float)$answer19, 0, '.', '')
                ];
            } else {
                $totalBags19 = [
                    'bags' => 0,
                    'kgs' => 0
                ];
            }

            if($bag20 > 0)
            {
                $bag20Bags = $bag20 / 50;
                $str_arr20 = explode('.',$bag20Bags);
                $str20 = str_replace($str_arr20[0], '0.', $str_arr20[0]);
                $strPound = $str_arr20[1] ?? 0;
                $substr20 = $str20.''.$strPound;
                $answer20 = $substr20 * 50;
                $totalBags20 = [
                    'bags' => $str_arr20[0],
                    'kgs' => number_format((float)$answer20, 0, '.', '')
                ];
            } else {
                $totalBags20 = [
                    'bags' => 0,
                    'kgs' => 0
                ];
            }
            
            $data = ['18M' => $totalBags18, '19M' => $totalBags19, '20M' => $totalBags20, 'TOTAL_BAGS' => $totalBags, 'AB' => $totalBeratingAverage, 'AP' => $totalPercentageAverage];
        }

        if (request()->ajax()) {
            return DataTables::of($analysis)->make(true);
        }

        return view('admin.weekly_analysis.tin_kg', [
            'analysis' => $analysis,
            'data' => $data,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'manager' => $request->manager
        ]);
    }

    public function weekly_analysis_columbite_pound(Request $request)
    {
        if($request->start_date == null && $request->end_date == null && $request->manager == null)
        {
            $columbitePayment = PaymentReceiptColumbite::latest()->where('type', 'pound')->get();
        } elseif($request->start_date !== null && $request->end_date !== null && $request->manager == null)
        {
            $columbitePayment = PaymentReceiptColumbite::latest()->where('type', 'pound')->whereBetween('date_of_purchase', [$request->start_date, $request->end_date])->get();
        } elseif($request->start_date == null && $request->end_date == null && $request->manager !== null)
        { 
            $columbitePayment = PaymentReceiptColumbite::latest()->where('type', 'pound')->where('staff', $request->manager)->get();
        }else {
            $columbitePayment = PaymentReceiptColumbite::latest()->where('type', 'pound')->where('staff', $request->manager)->whereBetween('date_of_purchase', [$request->start_date, $request->end_date])->get();
        }

        if($columbitePayment->isEmpty())
        {
            $analysis = [];

        } else {
            
            $beratingCalculation = BeratingCalculation::latest()->get();

            foreach($columbitePayment as $tinpound)
            {
                $beratingpayment = BeratingCalculation::find($tinpound->grade);

                foreach($beratingCalculation as $berating)
                {
                    if($berating->grade == $beratingpayment->grade)
                    {
                        $data[] = ['date' => $tinpound->date_of_purchase, 'berating' => $berating->grade, 'total' => $tinpound->total_in_pound];

                        $analysis = array_values(array_unique($data, 0));
                                    
                        rsort($analysis);
                    }
                }
            }
        }

        // Calculation Starts
        if($request->start_date == null && $request->end_date == null && $request->manager == null)
        {
            $result =  PaymentReceiptColumbite::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_columbites.grade')->latest()->where('payment_receipt_columbites.type', 'pound')  
                                ->get(['payment_receipt_columbites.date_of_purchase', 'payment_receipt_columbites.total_in_pound', 'payment_receipt_columbites.percentage_analysis', 'berating_calculations.grade', 'payment_receipt_columbites.created_at', 'payment_receipt_columbites.updated_at']);
        } elseif($request->start_date !== null && $request->end_date !== null && $request->manager == null)
        {
            $result =  PaymentReceiptColumbite::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_columbites.grade')->latest()->where('payment_receipt_columbites.type', 'pound')  
                                ->whereBetween('payment_receipt_columbites.date_of_purchase', [$request->start_date, $request->end_date])
                                ->get(['payment_receipt_columbites.date_of_purchase', 'payment_receipt_columbites.total_in_pound', 'payment_receipt_columbites.percentage_analysis', 'berating_calculations.grade', 'payment_receipt_columbites.created_at', 'payment_receipt_columbites.updated_at']);
        } elseif($request->start_date == null && $request->end_date == null && $request->manager !== null)
        { 
            $result =  PaymentReceiptColumbite::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_columbites.grade')->latest()->where('payment_receipt_columbites.type', 'pound')  
                                ->where('payment_receipt_columbites.staff', $request->manager)
                                ->get(['payment_receipt_columbites.date_of_purchase', 'payment_receipt_columbites.total_in_pound', 'payment_receipt_columbites.percentage_analysis', 'berating_calculations.grade', 'payment_receipt_columbites.created_at', 'payment_receipt_columbites.updated_at']);
        } else {
            $result =  PaymentReceiptColumbite::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_columbites.grade')->latest()->where('payment_receipt_columbites.type', 'pound')  
                                ->where('payment_receipt_columbites.staff', $request->manager)->whereBetween('payment_receipt_columbites.date_of_purchase', [$request->start_date, $request->end_date])
                                ->get(['payment_receipt_columbites.date_of_purchase', 'payment_receipt_columbites.total_in_pound', 'payment_receipt_columbites.percentage_analysis', 'berating_calculations.grade', 'payment_receipt_columbites.created_at', 'payment_receipt_columbites.updated_at']);
        }
        
        if($result->isEmpty())
        {
            $totalBags = [
                'bags' => 0,
                'pounds' => 0
            ];

            $totalBags18 = [
                'bags' => 0,
                'pounds' => 0
            ];

            $totalBags19 = [
                'bags' => 0,
                'pounds' => 0
            ];

            $totalBags20 = [
                'bags' => 0,
                'pounds' => 0
            ];

            $totalBeratingAverage = 0;

            $totalPercentageAverage = 0;

            $data = ['18M' => $totalBags18, '19M' => $totalBags19, '20M' => $totalBags20, 'TOTAL_BAGS' => $totalBags, 'AB' => $totalBeratingAverage, 'AP' => $totalPercentageAverage];

        } else {
            // Total Kg and Total Average Berating
            $sum185 = $result->where('grade', '18.5')->sum('total_in_pound');
            $sum186 = $result->where('grade', '18.6')->sum('total_in_pound');
            $sum187 = $result->where('grade', '18.7')->sum('total_in_pound');
            $sum188 = $result->where('grade', '18.8')->sum('total_in_pound');
            $sum189 = $result->where('grade', '18.9')->sum('total_in_pound');
            $sum190 = $result->where('grade', '19.0')->sum('total_in_pound');
            $sum191 = $result->where('grade', '19.1')->sum('total_in_pound');
            $sum192 = $result->where('grade', '19.2')->sum('total_in_pound');
            $sum193 = $result->where('grade', '19.3')->sum('total_in_pound');
            $sum194 = $result->where('grade', '19.4')->sum('total_in_pound');
            $sum195 = $result->where('grade', '19.5')->sum('total_in_pound');
            $sum196 = $result->where('grade', '19.6')->sum('total_in_pound');
            $sum197 = $result->where('grade', '19.7')->sum('total_in_pound');
            $sum198 = $result->where('grade', '19.8')->sum('total_in_pound');
            $sum199 = $result->where('grade', '19.9')->sum('total_in_pound');
            $sum200 = $result->where('grade', '20.0')->sum('total_in_pound');
            $sum201 = $result->where('grade', '20.1')->sum('total_in_pound');
            $sum202 = $result->where('grade', '20.2')->sum('total_in_pound');
            $sum203 = $result->where('grade', '20.3')->sum('total_in_pound');
            $sum204 = $result->where('grade', '20.4')->sum('total_in_pound');
            $sum205 = $result->where('grade', '20.5')->sum('total_in_pound');
            $sum206 = $result->where('grade', '20.6')->sum('total_in_pound');
            $sum207 = $result->where('grade', '20.7')->sum('total_in_pound');
            $sum208 = $result->where('grade', '20.8')->sum('total_in_pound');
            $sum209 = $result->where('grade', '20.9')->sum('total_in_pound');

            $totalPound = $sum185 + $sum186 + $sum187 + $sum188 + $sum189 + $sum190 + $sum191 + $sum192 + $sum193 + $sum194 + $sum195 + $sum196 + $sum197 + $sum198 + $sum199 + $sum200 + $sum201 + $sum202 + $sum203 + $sum204 + $sum205 + $sum206 + $sum207 + $sum208 + $sum209;

            $b185 = $sum185 * 18.5; $b186 = $sum186 * 18.6; $b187 = $sum187 * 18.7; $b188 = $sum188 * 18.8; $b189 = $sum189 * 18.9; $b190 = $sum190 * 19.0; $b191 = $sum191 * 19.1; $b192 = $sum192 * 19.2; $b193 = $sum193 * 19.3; $b194 = $sum194 * 19.4; $b195 = $sum195 * 19.5;  
            $b196 = $sum196 * 19.6; $b197 = $sum197 * 19.7;  $b198 = $sum198 * 19.8; $b199 = $sum199 * 19.9; $b200 = $sum200 * 20.0; $b201 = $sum201 * 20.1; $b202 = $sum202 * 20.2; $b203 = $sum203 * 20.3; 
            $b204 = $sum204 * 20.4; $b205 = $sum205 * 20.5; $b206 = $sum206 * 20.6; $b207 = $sum207 * 20.7; $b208 = $sum208 * 20.8; $b209 = $sum209 * 20.9;

            $totalBerating = $b185 + $b186 + $b187 + $b188 + $b189 + $b190 + $b191 + $b192 + $b193 + $b194 + $b195 + $b196 + $b197 + $b198 + $b199 + $b200 + $b201 +  $b202 + $b203 + $b204 + $b205 + $b206 + $b207 + $b208 + $b209;

            $beratingAverage = $totalBerating / $totalPound; 

            $totalBeratingAverage = number_format((float)$beratingAverage, 2, '.', '');

            // Percentage Average
            $percentage185 = $result->where('grade', '18.5');
            if($percentage185->isEmpty())
            {
                $sumPercentage185[] = 0;
            } else {
                foreach($percentage185 as $per185)
                {
                    $sumPercentage185[] = $per185->percentage_analysis * $per185->total_in_pound;
                }
            }
            $percentage186 = $result->where('grade', '18.6');
            if($percentage186->isEmpty())
            {
                $sumPercentage186[] = 0;
            } else {
                foreach($percentage186 as $per186)
                {
                    $sumPercentage186[] = $per186->percentage_analysis * $per186->total_in_pound;
                }
            }
            $percentage187 = $result->where('grade', '18.7');
            if($percentage187->isEmpty())
            {
                $sumPercentage187[] = 0;
            } else {
                foreach($percentage187 as $per187)
                {
                    $sumPercentage187[] = $per187->percentage_analysis * $per187->total_in_pound;
                }
            }
            $percentage188 = $result->where('grade', '18.8');
            if($percentage188->isEmpty())
            {
                $sumPercentage188[] = 0;
            } else {
                foreach($percentage188 as $per188)
                {
                    $sumPercentage188[] = $per188->percentage_analysis * $per188->total_in_pound;
                }
            }
            $percentage189 = $result->where('grade', '18.9');
            if($percentage189->isEmpty())
            {
                $sumPercentage189[] = 0;
            } else {
                foreach($percentage189 as $per189)
                {
                    $sumPercentage189[] = $per189->percentage_analysis * $per189->total_in_pound;
                }
            }
            $percentage190 = $result->where('grade', '19.0');
            if($percentage190->isEmpty())
            {
                $sumPercentage190[] = 0;
            } else {
                foreach($percentage190 as $per190)
                {
                    $sumPercentage190[] = $per190->percentage_analysis * $per190->total_in_pound;
                }
            }
            $percentage191 = $result->where('grade', '19.1');
            if($percentage191->isEmpty())
            {
                $sumPercentage191[] = 0;
            } else {
                foreach($percentage191 as $per191)
                {
                    $sumPercentage191[] = $per191->percentage_analysis * $per191->total_in_pound;
                }
            }
            $percentage192 = $result->where('grade', '19.2');
            if($percentage192->isEmpty())
            {
                $sumPercentage192[] = 0;
            } else {
                foreach($percentage192 as $per192)
                {
                    $sumPercentage192[] = $per192->percentage_analysis * $per192->total_in_pound;
                }
            }
            $percentage193 = $result->where('grade', '19.3');
            if($percentage193->isEmpty())
            {
                $sumPercentage193[] = 0;
            } else {
                foreach($percentage193 as $per193)
                {
                    $sumPercentage193[] = $per193->percentage_analysis * $per193->total_in_pound;
                }
            }
            $percentage194 = $result->where('grade', '19.4');
            if($percentage194->isEmpty())
            {
                $sumPercentage194[] = 0;
            } else {
                foreach($percentage194 as $per194)
                {
                    $sumPercentage194[] = $per194->percentage_analysis * $per194->total_in_pound;
                }
            }
            $percentage195 = $result->where('grade', '19.5');
            if($percentage195->isEmpty())
            {
                $sumPercentage195[] = 0;
            } else {
                foreach($percentage195 as $per195)
                {
                    $sumPercentage195[] = $per195->percentage_analysis * $per195->total_in_pound;
                }
            }
            $percentage196 = $result->where('grade', '19.6');
            if($percentage196->isEmpty())
            {
                $sumPercentage196[] = 0;
            } else {
                foreach($percentage196 as $per196)
                {
                    $sumPercentage196[] = $per196->percentage_analysis * $per196->total_in_pound;
                }
            }
            $percentage197 = $result->where('grade', '19.7');
            if($percentage197->isEmpty())
            {
                $sumPercentage197[] = 0;
            } else {
                foreach($percentage197 as $per197)
                {
                    $sumPercentage197[] = $per197->percentage_analysis * $per197->total_in_pound;
                }
            }
            $percentage198 = $result->where('grade', '19.8');
            if($percentage198->isEmpty())
            {
                $sumPercentage198[] = 0;
            } else {
                foreach($percentage198 as $per198)
                {
                    $sumPercentage198[] = $per198->percentage_analysis * $per198->total_in_pound;
                }
            }
            $percentage199 = $result->where('grade', '19.9');
            if($percentage199->isEmpty())
            {
                $sumPercentage199[] = 0;
            } else {
                foreach($percentage199 as $per199)
                {
                    $sumPercentage199[] = $per199->percentage_analysis * $per199->total_in_pound;
                }
            }
            $percentage200 = $result->where('grade', '20.0');
            if($percentage200->isEmpty())
            {
                $sumPercentage200[] = 0;
            } else {
                foreach($percentage200 as $per200)
                {
                    $sumPercentage200[] = $per200->percentage_analysis * $per200->total_in_pound;
                }
            }
            $percentage201 = $result->where('grade', '20.1');
            if($percentage201->isEmpty())
            {
                $sumPercentage201[] = 0;
            } else {
                foreach($percentage201 as $per201)
                {
                    $sumPercentage201[] = $per201->percentage_analysis * $per201->total_in_pound;
                }
            }
            $percentage202 = $result->where('grade', '20.2');
            if($percentage202->isEmpty())
            {
                $sumPercentage202[] = 0;
            } else {
                foreach($percentage202 as $per202)
                {
                    $sumPercentage202[] = $per202->percentage_analysis * $per202->total_in_pound;
                }
            }
            $percentage203 = $result->where('grade', '20.3');
            if($percentage203->isEmpty())
            {
                $sumPercentage203[] = 0;
            } else {
                foreach($percentage203 as $per203)
                {
                    $sumPercentage203[] = $per203->percentage_analysis * $per203->total_in_pound;
                }
            }
            $percentage204 = $result->where('grade', '20.4');
            if($percentage204->isEmpty())
            {
                $sumPercentage204[] = 0;
            } else {
                foreach($percentage204 as $per204)
                {
                    $sumPercentage204[] = $per204->percentage_analysis * $per204->total_in_pound;
                }
            }
            $percentage205 = $result->where('grade', '20.5');
            if($percentage205->isEmpty())
            {
                $sumPercentage205[] = 0;
            } else {
                foreach($percentage205 as $per205)
                {
                    $sumPercentage205[] = $per205->percentage_analysis * $per205->total_in_pound;
                }
            }
            $percentage206 = $result->where('grade', '20.6');
            if($percentage206->isEmpty())
            {
                $sumPercentage206[] = 0;
            } else {
                foreach($percentage206 as $per206)
                {
                    $sumPercentage206[] = $per206->percentage_analysis * $per206->total_in_pound;
                }
            }
            $percentage207 = $result->where('grade', '20.7');
            if($percentage207->isEmpty())
            {
                $sumPercentage207[] = 0;
            } else {
                foreach($percentage207 as $per207)
                {
                    $sumPercentage207[] = $per207->percentage_analysis * $per207->total_in_pound;
                }
            }
            $percentage208 = $result->where('grade', '20.8');
            if($percentage208->isEmpty())
            {
                $sumPercentage208[] = 0;
            } else {
                foreach($percentage208 as $per208)
                {
                    $sumPercentage208[] = $per208->percentage_analysis * $per208->total_in_pound;
                }
            }
            $percentage209 = $result->where('grade', '20.9');
            if($percentage209->isEmpty())
            {
                $sumPercentage209[] = 0;
            } else {
                foreach($percentage209 as $per209)
                {
                    $sumPercentage209[] = $per209->percentage_analysis * $per209->total_in_pound;
                }
            }

            $totalPercentage185 = array_sum($sumPercentage185); 
            $totalPercentage186 = array_sum($sumPercentage186); 
            $totalPercentage187 = array_sum($sumPercentage187); 
            $totalPercentage188 = array_sum($sumPercentage188); 
            $totalPercentage189 = array_sum($sumPercentage189); 
            $totalPercentage190 = array_sum($sumPercentage190);
            $totalPercentage191 = array_sum($sumPercentage191);
            $totalPercentage192 = array_sum($sumPercentage192);
            $totalPercentage193 = array_sum($sumPercentage193);
            $totalPercentage194 = array_sum($sumPercentage194); 
            $totalPercentage195 = array_sum($sumPercentage195); 
            $totalPercentage196 = array_sum($sumPercentage196);
            $totalPercentage197 = array_sum($sumPercentage197);
            $totalPercentage198 = array_sum($sumPercentage198); 
            $totalPercentage199 = array_sum($sumPercentage199);
            $totalPercentage200 = array_sum($sumPercentage200); 
            $totalPercentage201 = array_sum($sumPercentage201);
            $totalPercentage202 = array_sum($sumPercentage202);
            $totalPercentage203 = array_sum($sumPercentage203);
            $totalPercentage204 = array_sum($sumPercentage204); 
            $totalPercentage205 = array_sum($sumPercentage205);
            $totalPercentage206 = array_sum($sumPercentage206);
            $totalPercentage207 = array_sum($sumPercentage207);
            $totalPercentage208 = array_sum($sumPercentage208); 
            $totalPercentage209 = array_sum($sumPercentage209);

            $totalPercentage = $totalPercentage185 + $totalPercentage186 + $totalPercentage187 + $totalPercentage188 + $totalPercentage189 + $totalPercentage190 + $totalPercentage191 + $totalPercentage192 + $totalPercentage193 + $totalPercentage194 + $totalPercentage195 + $totalPercentage196 + $totalPercentage197 + $totalPercentage198 + $totalPercentage199 + $totalPercentage200 + $totalPercentage201 + $totalPercentage202 + $totalPercentage203 + $totalPercentage204 + $totalPercentage205 + $totalPercentage206 + $totalPercentage207 + $totalPercentage208 + $totalPercentage209;

            // $p185 = $sum185 * $percentage185; $p186 = $sum186 * $percentage186; $p187 = $sum187 * $percentage187; $p188 = $sum188 * $percentage188; $p189 = $sum189 * $percentage189; $p190 = $sum190 * $percentage190; $p191 = $sum191 * $percentage191; $p192 = $sum192 * $percentage192; $p193 = $sum193 * $percentage193; $p194 = $sum194 * $percentage194; $p195 = $sum195 * $percentage195;  
            // $p196 = $sum196 * $percentage196; $p197 = $sum197 * $percentage197;  $p198 = $sum198 * $percentage198; $p199 = $sum199 * $percentage199; $p200 = $sum200 * $percentage200; $p201 = $sum201 * $percentage201; $p202 = $sum202 * $percentage202; $p203 = $sum203 * $percentage203; 
            // $p204 = $sum204 * $percentage204; $p205 = $sum205 * $percentage205; $p206 = $sum206 * $percentage206; $p207 = $sum207 * $percentage207; $p208 = $sum208 * $percentage208; $p209 = $sum209 * $percentage209;

            // $totalPercentage =  $p185 + $p186 + $p187 + $p188 + $p189 + $p190 + $p191 + $p192 + $p193 + $p194 + $p195 + $p196 + $p197 + $p198 + $p199 + $p200 + $p201 +  $p202 + $p203 + $p204 + $p205 + $p206 + $p207 + $p208 + $p209;
            
            $percentageAverage = $totalPercentage / $totalPound;

            $totalPercentageAverage = number_format((float)$percentageAverage, 2, '.', '');

            // 
            $bags = $totalPound / 80;
            $str_arr = explode('.',$bags);
            $str = str_replace($str_arr[0], '0.', $str_arr[0]);
            $strP = $str_arr[1] ?? 0;
            $substr = $str.''.$strP;
            $answer = $substr * 80;
            $totalBags = [
                'bags' => $str_arr[0],
                'pounds' => number_format((float)$answer, 0, '.', '')
            ];
  
            $bag18 = $sum185 + $sum186 + $sum187 + $sum188 + $sum189;
            $bag19 = $sum190 + $sum191 + $sum192 + $sum193 + $sum194 + $sum195 + $sum196 + $sum197 + $sum198 + $sum199;
            $bag20 = $sum200 + $sum201 + $sum202 + $sum203 + $sum204 + $sum205 + $sum206 + $sum207 + $sum208 + $sum209;

            if($bag18 > 0)
            {
                $bag18Bags = $bag18 / 80;
                $str_arr18 = explode('.',$bag18Bags);
                $str18 = str_replace($str_arr18[0], '0.', $str_arr18[0]);
                $strPound = $str_arr18[1] ?? 0;
                $substr18 = $str18.''.$strPound;
                $answer18 = $substr18 * 80;
                $totalBags18 = [
                    'bags' => $str_arr18[0],
                    'pounds' => number_format((float)$answer18, 0, '.', '')
                ];
            } else {
                $totalBags18 = [
                    'bags' => 0,
                    'pounds' => 0
                ];
            }

            if($bag19 > 0)
            {
                $bag19Bags = $bag19 / 80;
                $str_arr19 = explode('.',$bag19Bags);
                $str19 = str_replace($str_arr19[0], '0.', $str_arr19[0]);
                $strPound = $str_arr19[1] ?? 0;
                $substr19 = $str19.''.$strPound;
                $answer19 = $substr19 * 80;
                $totalBags19 = [
                    'bags' => $str_arr19[0],
                    'pounds' => number_format((float)$answer19, 0, '.', '')
                ];
            } else {
                $totalBags19 = [
                    'bags' => 0,
                    'pounds' => 0
                ];
            }

            if($bag20 > 0)
            {
                $bag20Bags = $bag20 / 80;
                $str_arr20 = explode('.',$bag20Bags);
                $str20 = str_replace($str_arr20[0], '0.', $str_arr20[0]);
                $strPound = $str_arr20[1] ?? 0;
                $substr20 = $str20.''.$strPound;
                $answer20 = $substr20 * 80;
                $totalBags20 = [
                    'bags' => $str_arr20[0],
                    'pounds' => number_format((float)$answer20, 0, '.', '')
                ];
            } else {
                $totalBags20 = [
                    'bags' => 0,
                    'pounds' => 0
                ];
            }
            
            $data = ['18M' => $totalBags18, '19M' => $totalBags19, '20M' => $totalBags20, 'TOTAL_BAGS' => $totalBags, 'AB' => $totalBeratingAverage, 'AP' => $totalPercentageAverage];
        }

        if (request()->ajax()) {
            return DataTables::of($analysis)->make(true);
        }

        return view('admin.weekly_analysis.columbite_pound', [
            'analysis' => $analysis,
            'data' => $data,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'manager' => $request->manager
        ]);
    }

    public function weekly_analysis_columbite_kg(Request $request)
    {
        if($request->start_date == null && $request->end_date == null && $request->manager == null)
        {
            $columbitePayment = PaymentReceiptColumbite::latest()->where('type', 'kg')->get();
        } elseif($request->start_date !== null && $request->end_date !== null && $request->manager == null)
        {
            $columbitePayment = PaymentReceiptColumbite::latest()->where('type', 'kg')->whereBetween('date_of_purchase', [$request->start_date, $request->end_date])->get();
        } elseif($request->start_date == null && $request->end_date == null && $request->manager !== null)
        {
            $columbitePayment = PaymentReceiptColumbite::latest()->where('type', 'kg')->where('staff', $request->manager)->get();
        } else {
            $columbitePayment = PaymentReceiptColumbite::latest()->where('type', 'kg')->where('staff', $request->manager)->whereBetween('date_of_purchase', [$request->start_date, $request->end_date])->get();
        }

        if($columbitePayment->isEmpty())
        {
            $analysis = [];

        } else {
            
            $beratingCalculation = BeratingCalculation::latest()->get();

            foreach($columbitePayment as $colpayment)
            {
                $beratingpayment = BeratingCalculation::find($colpayment->grade);

                foreach($beratingCalculation as $berating)
                {
                    if($berating->grade == $beratingpayment->grade)
                    {
                        $data[] = ['date' => $colpayment->date_of_purchase, 'berating' => $berating->grade, 'total' => $colpayment->total_in_kg];

                        $analysis = array_values(array_unique($data, 0));
                                    
                        rsort($analysis);
                    }
                }
            }
        }

        // Calculation Starts
        if($request->start_date == null && $request->end_date == null && $request->manager == null)
        {
            $result =  PaymentReceiptColumbite::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_columbites.grade')->latest()->where('payment_receipt_columbites.type', 'kg')  
                                ->get(['payment_receipt_columbites.date_of_purchase', 'payment_receipt_columbites.total_in_kg', 'payment_receipt_columbites.percentage_analysis', 'berating_calculations.grade', 'payment_receipt_columbites.created_at', 'payment_receipt_columbites.updated_at']);
        } elseif($request->start_date !== null && $request->end_date !== null && $request->manager == null)
        {
            $result =  PaymentReceiptColumbite::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_columbites.grade')->latest()->where('payment_receipt_columbites.type', 'kg')  
                                ->whereBetween('payment_receipt_columbites.date_of_purchase', [$request->start_date, $request->end_date])
                                ->get(['payment_receipt_columbites.date_of_purchase', 'payment_receipt_columbites.total_in_kg', 'payment_receipt_columbites.percentage_analysis', 'berating_calculations.grade', 'payment_receipt_columbites.created_at', 'payment_receipt_columbites.updated_at']);
        } elseif($request->start_date == null && $request->end_date == null && $request->manager !== null)
        { 
            $result =  PaymentReceiptColumbite::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_columbites.grade')->latest()->where('payment_receipt_columbites.type', 'kg')  
                                ->where('payment_receipt_columbites.staff', $request->manager)
                                ->get(['payment_receipt_columbites.date_of_purchase', 'payment_receipt_columbites.total_in_kg', 'payment_receipt_columbites.percentage_analysis', 'berating_calculations.grade', 'payment_receipt_columbites.created_at', 'payment_receipt_columbites.updated_at']);
        } else {
            $result =  PaymentReceiptColumbite::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_columbites.grade')->latest()->where('payment_receipt_columbites.type', 'kg')  
                                ->where('payment_receipt_columbites.staff', $request->manager)->whereBetween('payment_receipt_columbites.date_of_purchase', [$request->start_date, $request->end_date])
                                ->get(['payment_receipt_columbites.date_of_purchase', 'payment_receipt_columbites.total_in_kg', 'payment_receipt_columbites.percentage_analysis', 'berating_calculations.grade', 'payment_receipt_columbites.created_at', 'payment_receipt_columbites.updated_at']);
        }
        
        if($result->isEmpty())
        {
            $totalBags = [
                'bags' => 0,
                'pounds' => 0
            ];

            $totalBags18 = [
                'bags' => 0,
                'pounds' => 0
            ];

            $totalBags19 = [
                'bags' => 0,
                'pounds' => 0
            ];

            $totalBags20 = [
                'bags' => 0,
                'pounds' => 0
            ];

            $totalBeratingAverage = 0;

            $totalPercentageAverage = 0;

            $data = ['18M' => $totalBags18, '19M' => $totalBags19, '20M' => $totalBags20, 'TOTAL_BAGS' => $totalBags, 'AB' => $totalBeratingAverage, 'AP' => $totalPercentageAverage];

        } else {
            // Total Kg and Total Average Berating
            $sum185 = $result->where('grade', '18.5')->sum('total_in_kg');
            $sum186 = $result->where('grade', '18.6')->sum('total_in_kg');
            $sum187 = $result->where('grade', '18.7')->sum('total_in_kg');
            $sum188 = $result->where('grade', '18.8')->sum('total_in_kg');
            $sum189 = $result->where('grade', '18.9')->sum('total_in_kg');
            $sum190 = $result->where('grade', '19.0')->sum('total_in_kg');
            $sum191 = $result->where('grade', '19.1')->sum('total_in_kg');
            $sum192 = $result->where('grade', '19.2')->sum('total_in_kg');
            $sum193 = $result->where('grade', '19.3')->sum('total_in_kg');
            $sum194 = $result->where('grade', '19.4')->sum('total_in_kg');
            $sum195 = $result->where('grade', '19.5')->sum('total_in_kg');
            $sum196 = $result->where('grade', '19.6')->sum('total_in_kg');
            $sum197 = $result->where('grade', '19.7')->sum('total_in_kg');
            $sum198 = $result->where('grade', '19.8')->sum('total_in_kg');
            $sum199 = $result->where('grade', '19.9')->sum('total_in_kg');
            $sum200 = $result->where('grade', '20.0')->sum('total_in_kg');
            $sum201 = $result->where('grade', '20.1')->sum('total_in_kg');
            $sum202 = $result->where('grade', '20.2')->sum('total_in_kg');
            $sum203 = $result->where('grade', '20.3')->sum('total_in_kg');
            $sum204 = $result->where('grade', '20.4')->sum('total_in_kg');
            $sum205 = $result->where('grade', '20.5')->sum('total_in_kg');
            $sum206 = $result->where('grade', '20.6')->sum('total_in_kg');
            $sum207 = $result->where('grade', '20.7')->sum('total_in_kg');
            $sum208 = $result->where('grade', '20.8')->sum('total_in_kg');
            $sum209 = $result->where('grade', '20.9')->sum('total_in_kg');

            $totalKg = $sum185 + $sum186 + $sum187 + $sum188 + $sum189 + $sum190 + $sum191 + $sum192 + $sum193 + $sum194 + $sum195 + $sum196 + $sum197 + $sum198 + $sum199 + $sum200 + $sum201 + $sum202 + $sum203 + $sum204 + $sum205 + $sum206 + $sum207 + $sum208 + $sum209;

            $b185 = $sum185 * 18.5; $b186 = $sum186 * 18.6; $b187 = $sum187 * 18.7; $b188 = $sum188 * 18.8; $b189 = $sum189 * 18.9; $b190 = $sum190 * 19.0; $b191 = $sum191 * 19.1; $b192 = $sum192 * 19.2; $b193 = $sum193 * 19.3; $b194 = $sum194 * 19.4; $b195 = $sum195 * 19.5;  
            $b196 = $sum196 * 19.6; $b197 = $sum197 * 19.7;  $b198 = $sum198 * 19.8; $b199 = $sum199 * 19.9; $b200 = $sum200 * 20.0; $b201 = $sum201 * 20.1; $b202 = $sum202 * 20.2; $b203 = $sum203 * 20.3; 
            $b204 = $sum204 * 20.4; $b205 = $sum205 * 20.5; $b206 = $sum206 * 20.6; $b207 = $sum207 * 20.7; $b208 = $sum208 * 20.8; $b209 = $sum209 * 20.9;

            $totalBerating = $b185 + $b186 + $b187 + $b188 + $b189 + $b190 + $b191 + $b192 + $b193 + $b194 + $b195 + $b196 + $b197 + $b198 + $b199 + $b200 + $b201 +  $b202 + $b203 + $b204 + $b205 + $b206 + $b207 + $b208 + $b209;

            $beratingAverage = $totalBerating / $totalKg; 

            $totalBeratingAverage = number_format((float)$beratingAverage, 2, '.', '');

            // Percentage Average
            $percentage185 = $result->where('grade', '18.5');
            if($percentage185->isEmpty())
            {
                $sumPercentage185[] = 0;
            } else {
                foreach($percentage185 as $per185)
                {
                    $sumPercentage185[] = $per185->percentage_analysis * $per185->total_in_kg;
                }
            }
            $percentage186 = $result->where('grade', '18.6');
            if($percentage186->isEmpty())
            {
                $sumPercentage186[] = 0;
            } else {
                foreach($percentage186 as $per186)
                {
                    $sumPercentage186[] = $per186->percentage_analysis * $per186->total_in_kg;
                }
            }
            $percentage187 = $result->where('grade', '18.7');
            if($percentage187->isEmpty())
            {
                $sumPercentage187[] = 0;
            } else {
                foreach($percentage187 as $per187)
                {
                    $sumPercentage187[] = $per187->percentage_analysis * $per187->total_in_kg;
                }
            }
            $percentage188 = $result->where('grade', '18.8');
            if($percentage188->isEmpty())
            {
                $sumPercentage188[] = 0;
            } else {
                foreach($percentage188 as $per188)
                {
                    $sumPercentage188[] = $per188->percentage_analysis * $per188->total_in_kg;
                }
            }
            $percentage189 = $result->where('grade', '18.9');
            if($percentage189->isEmpty())
            {
                $sumPercentage189[] = 0;
            } else {
                foreach($percentage189 as $per189)
                {
                    $sumPercentage189[] = $per189->percentage_analysis * $per189->total_in_kg;
                }
            }
            $percentage190 = $result->where('grade', '19.0');
            if($percentage190->isEmpty())
            {
                $sumPercentage190[] = 0;
            } else {
                foreach($percentage190 as $per190)
                {
                    $sumPercentage190[] = $per190->percentage_analysis * $per190->total_in_kg;
                }
            }
            $percentage191 = $result->where('grade', '19.1');
            if($percentage191->isEmpty())
            {
                $sumPercentage191[] = 0;
            } else {
                foreach($percentage191 as $per191)
                {
                    $sumPercentage191[] = $per191->percentage_analysis * $per191->total_in_kg;
                }
            }
            $percentage192 = $result->where('grade', '19.2');
            if($percentage192->isEmpty())
            {
                $sumPercentage192[] = 0;
            } else {
                foreach($percentage192 as $per192)
                {
                    $sumPercentage192[] = $per192->percentage_analysis * $per192->total_in_kg;
                }
            }
            $percentage193 = $result->where('grade', '19.3');
            if($percentage193->isEmpty())
            {
                $sumPercentage193[] = 0;
            } else {
                foreach($percentage193 as $per193)
                {
                    $sumPercentage193[] = $per193->percentage_analysis * $per193->total_in_kg;
                }
            }
            $percentage194 = $result->where('grade', '19.4');
            if($percentage194->isEmpty())
            {
                $sumPercentage194[] = 0;
            } else {
                foreach($percentage194 as $per194)
                {
                    $sumPercentage194[] = $per194->percentage_analysis * $per194->total_in_kg;
                }
            }
            $percentage195 = $result->where('grade', '19.5');
            if($percentage195->isEmpty())
            {
                $sumPercentage195[] = 0;
            } else {
                foreach($percentage195 as $per195)
                {
                    $sumPercentage195[] = $per195->percentage_analysis * $per195->total_in_kg;
                }
            }
            $percentage196 = $result->where('grade', '19.6');
            if($percentage196->isEmpty())
            {
                $sumPercentage196[] = 0;
            } else {
                foreach($percentage196 as $per196)
                {
                    $sumPercentage196[] = $per196->percentage_analysis * $per196->total_in_kg;
                }
            }
            $percentage197 = $result->where('grade', '19.7');
            if($percentage197->isEmpty())
            {
                $sumPercentage197[] = 0;
            } else {
                foreach($percentage197 as $per197)
                {
                    $sumPercentage197[] = $per197->percentage_analysis * $per197->total_in_kg;
                }
            }
            $percentage198 = $result->where('grade', '19.8');
            if($percentage198->isEmpty())
            {
                $sumPercentage198[] = 0;
            } else {
                foreach($percentage198 as $per198)
                {
                    $sumPercentage198[] = $per198->percentage_analysis * $per198->total_in_kg;
                }
            }
            $percentage199 = $result->where('grade', '19.9');
            if($percentage199->isEmpty())
            {
                $sumPercentage199[] = 0;
            } else {
                foreach($percentage199 as $per199)
                {
                    $sumPercentage199[] = $per199->percentage_analysis * $per199->total_in_kg;
                }
            }
            $percentage200 = $result->where('grade', '20.0');
            if($percentage200->isEmpty())
            {
                $sumPercentage200[] = 0;
            } else {
                foreach($percentage200 as $per200)
                {
                    $sumPercentage200[] = $per200->percentage_analysis * $per200->total_in_kg;
                }
            }
            $percentage201 = $result->where('grade', '20.1');
            if($percentage201->isEmpty())
            {
                $sumPercentage201[] = 0;
            } else {
                foreach($percentage201 as $per201)
                {
                    $sumPercentage201[] = $per201->percentage_analysis * $per201->total_in_kg;
                }
            }
            $percentage202 = $result->where('grade', '20.2');
            if($percentage202->isEmpty())
            {
                $sumPercentage202[] = 0;
            } else {
                foreach($percentage202 as $per202)
                {
                    $sumPercentage202[] = $per202->percentage_analysis * $per202->total_in_kg;
                }
            }
            $percentage203 = $result->where('grade', '20.3');
            if($percentage203->isEmpty())
            {
                $sumPercentage203[] = 0;
            } else {
                foreach($percentage203 as $per203)
                {
                    $sumPercentage203[] = $per203->percentage_analysis * $per203->total_in_kg;
                }
            }
            $percentage204 = $result->where('grade', '20.4');
            if($percentage204->isEmpty())
            {
                $sumPercentage204[] = 0;
            } else {
                foreach($percentage204 as $per204)
                {
                    $sumPercentage204[] = $per204->percentage_analysis * $per204->total_in_kg;
                }
            }
            $percentage205 = $result->where('grade', '20.5');
            if($percentage205->isEmpty())
            {
                $sumPercentage205[] = 0;
            } else {
                foreach($percentage205 as $per205)
                {
                    $sumPercentage205[] = $per205->percentage_analysis * $per205->total_in_kg;
                }
            }
            $percentage206 = $result->where('grade', '20.6');
            if($percentage206->isEmpty())
            {
                $sumPercentage206[] = 0;
            } else {
                foreach($percentage206 as $per206)
                {
                    $sumPercentage206[] = $per206->percentage_analysis * $per206->total_in_kg;
                }
            }
            $percentage207 = $result->where('grade', '20.7');
            if($percentage207->isEmpty())
            {
                $sumPercentage207[] = 0;
            } else {
                foreach($percentage207 as $per207)
                {
                    $sumPercentage207[] = $per207->percentage_analysis * $per207->total_in_kg;
                }
            }
            $percentage208 = $result->where('grade', '20.8');
            if($percentage208->isEmpty())
            {
                $sumPercentage208[] = 0;
            } else {
                foreach($percentage208 as $per208)
                {
                    $sumPercentage208[] = $per208->percentage_analysis * $per208->total_in_kg;
                }
            }
            $percentage209 = $result->where('grade', '20.9');
            if($percentage209->isEmpty())
            {
                $sumPercentage209[] = 0;
            } else {
                foreach($percentage209 as $per209)
                {
                    $sumPercentage209[] = $per209->percentage_analysis * $per209->total_in_kg;
                }
            }

            $totalPercentage185 = array_sum($sumPercentage185); 
            $totalPercentage186 = array_sum($sumPercentage186); 
            $totalPercentage187 = array_sum($sumPercentage187); 
            $totalPercentage188 = array_sum($sumPercentage188); 
            $totalPercentage189 = array_sum($sumPercentage189); 
            $totalPercentage190 = array_sum($sumPercentage190);
            $totalPercentage191 = array_sum($sumPercentage191);
            $totalPercentage192 = array_sum($sumPercentage192);
            $totalPercentage193 = array_sum($sumPercentage193);
            $totalPercentage194 = array_sum($sumPercentage194); 
            $totalPercentage195 = array_sum($sumPercentage195); 
            $totalPercentage196 = array_sum($sumPercentage196);
            $totalPercentage197 = array_sum($sumPercentage197);
            $totalPercentage198 = array_sum($sumPercentage198); 
            $totalPercentage199 = array_sum($sumPercentage199);
            $totalPercentage200 = array_sum($sumPercentage200); 
            $totalPercentage201 = array_sum($sumPercentage201);
            $totalPercentage202 = array_sum($sumPercentage202);
            $totalPercentage203 = array_sum($sumPercentage203);
            $totalPercentage204 = array_sum($sumPercentage204); 
            $totalPercentage205 = array_sum($sumPercentage205);
            $totalPercentage206 = array_sum($sumPercentage206);
            $totalPercentage207 = array_sum($sumPercentage207);
            $totalPercentage208 = array_sum($sumPercentage208); 
            $totalPercentage209 = array_sum($sumPercentage209);

            $totalPercentage = $totalPercentage185 + $totalPercentage186 + $totalPercentage187 + $totalPercentage188 + $totalPercentage189 + $totalPercentage190 + $totalPercentage191 + $totalPercentage192 + $totalPercentage193 + $totalPercentage194 + $totalPercentage195 + $totalPercentage196 + $totalPercentage197 + $totalPercentage198 + $totalPercentage199 + $totalPercentage200 + $totalPercentage201 + $totalPercentage202 + $totalPercentage203 + $totalPercentage204 + $totalPercentage205 + $totalPercentage206 + $totalPercentage207 + $totalPercentage208 + $totalPercentage209;

            // $p185 = $sum185 * $percentage185; $p186 = $sum186 * $percentage186; $p187 = $sum187 * $percentage187; $p188 = $sum188 * $percentage188; $p189 = $sum189 * $percentage189; $p190 = $sum190 * $percentage190; $p191 = $sum191 * $percentage191; $p192 = $sum192 * $percentage192; $p193 = $sum193 * $percentage193; $p194 = $sum194 * $percentage194; $p195 = $sum195 * $percentage195;  
            // $p196 = $sum196 * $percentage196; $p197 = $sum197 * $percentage197;  $p198 = $sum198 * $percentage198; $p199 = $sum199 * $percentage199; $p200 = $sum200 * $percentage200; $p201 = $sum201 * $percentage201; $p202 = $sum202 * $percentage202; $p203 = $sum203 * $percentage203; 
            // $p204 = $sum204 * $percentage204; $p205 = $sum205 * $percentage205; $p206 = $sum206 * $percentage206; $p207 = $sum207 * $percentage207; $p208 = $sum208 * $percentage208; $p209 = $sum209 * $percentage209;

            // $totalPercentage =  $p185 + $p186 + $p187 + $p188 + $p189 + $p190 + $p191 + $p192 + $p193 + $p194 + $p195 + $p196 + $p197 + $p198 + $p199 + $p200 + $p201 +  $p202 + $p203 + $p204 + $p205 + $p206 + $p207 + $p208 + $p209;
            
            $percentageAverage = $totalPercentage / $totalKg;

            $totalPercentageAverage = number_format((float)$percentageAverage, 2, '.', '');

            // 
            $bags = $totalKg / 80;
            $str_arr = explode('.',$bags);
            $str = str_replace($str_arr[0], '0.', $str_arr[0]);
            $strP = $str_arr[1] ?? 0;
            $substr = $str.''.$strP;
            $answer = $substr * 80;
            $totalBags = [
                'bags' => $str_arr[0],
                'pounds' => number_format((float)$answer, 0, '.', '')
            ];
  
            $bag18 = $sum185 + $sum186 + $sum187 + $sum188 + $sum189;
            $bag19 = $sum190 + $sum191 + $sum192 + $sum193 + $sum194 + $sum195 + $sum196 + $sum197 + $sum198 + $sum199;
            $bag20 = $sum200 + $sum201 + $sum202 + $sum203 + $sum204 + $sum205 + $sum206 + $sum207 + $sum208 + $sum209;

            if($bag18 > 0)
            {
                $bag18Bags = $bag18 / 80;
                $str_arr18 = explode('.',$bag18Bags);
                $str18 = str_replace($str_arr18[0], '0.', $str_arr18[0]);
                $strPound = $str_arr18[1] ?? 0;
                $substr18 = $str18.''.$strPound;
                $answer18 = $substr18 * 80;
                $totalBags18 = [
                    'bags' => $str_arr18[0],
                    'pounds' => number_format((float)$answer18, 0, '.', '')
                ];
            } else {
                $totalBags18 = [
                    'bags' => 0,
                    'pounds' => 0
                ];
            }

            if($bag19 > 0)
            {
                $bag19Bags = $bag19 / 80;
                $str_arr19 = explode('.',$bag19Bags);
                $str19 = str_replace($str_arr19[0], '0.', $str_arr19[0]);
                $strPound = $str_arr19[1] ?? 0;
                $substr19 = $str19.''.$strPound;
                $answer19 = $substr19 * 80;
                $totalBags19 = [
                    'bags' => $str_arr19[0],
                    'pounds' => number_format((float)$answer19, 0, '.', '')
                ];
            } else {
                $totalBags19 = [
                    'bags' => 0,
                    'pounds' => 0
                ];
            }

            if($bag20 > 0)
            {
                $bag20Bags = $bag20 / 80;
                $str_arr20 = explode('.',$bag20Bags);
                $str20 = str_replace($str_arr20[0], '0.', $str_arr20[0]);
                $strPound = $str_arr20[1] ?? 0;
                $substr20 = $str20.''.$strPound;
                $answer20 = $substr20 * 80;
                $totalBags20 = [
                    'bags' => $str_arr20[0],
                    'pounds' => number_format((float)$answer20, 0, '.', '')
                ];
            } else {
                $totalBags20 = [
                    'bags' => 0,
                    'pounds' => 0
                ];
            }
            
            $data = ['18M' => $totalBags18, '19M' => $totalBags19, '20M' => $totalBags20, 'TOTAL_BAGS' => $totalBags, 'AB' => $totalBeratingAverage, 'AP' => $totalPercentageAverage];
        }

        if (request()->ajax()) {
            return DataTables::of($analysis)->make(true);
        }

        return view('admin.weekly_analysis.columbite_kg', [
            'analysis' => $analysis,
            'data' => $data,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'manager' => $request->manager
        ]);
    }   

    // Monthly Analysis
    public function monthly_analysis_lower_grade_columbite_pound(Request $request)
    {
        if($request->start_date == null && $request->end_date == null && $request->manager == null)
        {
            $lgcolumbitePayment = PaymentReceiptLowerGradeColumbite::latest()->where('type', 'pound')->get();
        } elseif($request->start_date !== null && $request->end_date !== null && $request->manager == null)
        {
            $lgcolumbitePayment = PaymentReceiptLowerGradeColumbite::latest()->where('type', 'pound')->whereBetween('date_of_purchase', [$request->start_date, $request->end_date])->get();
        } elseif($request->start_date == null && $request->end_date == null && $request->manager !== null)
        { 
            $lgcolumbitePayment = PaymentReceiptLowerGradeColumbite::latest()->where('type', 'pound')->where('staff', $request->manager)->get();
        }else {
            $lgcolumbitePayment = PaymentReceiptLowerGradeColumbite::latest()->where('type', 'pound')->where('staff', $request->manager)->whereBetween('date_of_purchase', [$request->start_date, $request->end_date])->get();
        }

        if($lgcolumbitePayment->isEmpty())
        {
            $analysis = [];

        } else {
            
            $beratingCalculation = BeratingCalculation::latest()->get();

            foreach($lgcolumbitePayment as $pound)
            {
                $beratingpayment = BeratingCalculation::find($pound->grade);

                foreach($beratingCalculation as $berating)
                {
                    if($berating->grade == $beratingpayment->grade)
                    {
                        $data[] = ['date' => $pound->date_of_purchase, 'berating' => $berating->grade, 'total' => $pound->total_in_pound];

                        $analysis = array_values(array_unique($data, 0));
                                    
                        rsort($analysis);
                    }
                }
            }
        }

        // Calculation Starts
        if($request->start_date == null && $request->end_date == null && $request->manager == null)
        {
            $result =  PaymentReceiptLowerGradeColumbite::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_lower_grade_columbites.grade')->latest()->where('payment_receipt_lower_grade_columbites.type', 'pound')  
                                ->get(['payment_receipt_lower_grade_columbites.date_of_purchase', 'payment_receipt_lower_grade_columbites.total_in_pound', 'payment_receipt_lower_grade_columbites.percentage_analysis', 'berating_calculations.grade', 'payment_receipt_lower_grade_columbites.created_at', 'payment_receipt_lower_grade_columbites.updated_at']);
        } elseif($request->start_date !== null && $request->end_date !== null && $request->manager == null)
        {
            $result =  PaymentReceiptLowerGradeColumbite::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_lower_grade_columbites.grade')->latest()->where('payment_receipt_lower_grade_columbites.type', 'pound')  
                                ->whereBetween('payment_receipt_lower_grade_columbites.date_of_purchase', [$request->start_date, $request->end_date])
                                ->get(['payment_receipt_lower_grade_columbites.date_of_purchase', 'payment_receipt_lower_grade_columbites.total_in_pound', 'payment_receipt_lower_grade_columbites.percentage_analysis', 'berating_calculations.grade', 'payment_receipt_lower_grade_columbites.created_at', 'payment_receipt_lower_grade_columbites.updated_at']);
        } elseif($request->start_date == null && $request->end_date == null && $request->manager !== null)
        { 
            $result =  PaymentReceiptLowerGradeColumbite::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_lower_grade_columbites.grade')->latest()->where('payment_receipt_lower_grade_columbites.type', 'pound')  
                                ->where('payment_receipt_lower_grade_columbites.staff', $request->manager)
                                ->get(['payment_receipt_lower_grade_columbites.date_of_purchase', 'payment_receipt_lower_grade_columbites.total_in_pound', 'payment_receipt_lower_grade_columbites.percentage_analysis', 'berating_calculations.grade', 'payment_receipt_lower_grade_columbites.created_at', 'payment_receipt_lower_grade_columbites.updated_at']);
        } else {
            $result =  PaymentReceiptLowerGradeColumbite::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_lower_grade_columbites.grade')->latest()->where('payment_receipt_lower_grade_columbites.type', 'pound')  
                                ->where('payment_receipt_lower_grade_columbites.staff', $request->manager)->whereBetween('payment_receipt_lower_grade_columbites.date_of_purchase', [$request->start_date, $request->end_date])
                                ->get(['payment_receipt_lower_grade_columbites.date_of_purchase', 'payment_receipt_lower_grade_columbites.total_in_pound', 'payment_receipt_lower_grade_columbites.percentage_analysis', 'berating_calculations.grade', 'payment_receipt_lower_grade_columbites.created_at', 'payment_receipt_lower_grade_columbites.updated_at']);
        }
        
        if($result->isEmpty())
        {
            $totalBags = [
                'bags' => 0,
                'pounds' => 0
            ];

            $totalBags18 = [
                'bags' => 0,
                'pounds' => 0
            ];

            $totalBags19 = [
                'bags' => 0,
                'pounds' => 0
            ];

            $totalBags20 = [
                'bags' => 0,
                'pounds' => 0
            ];

            $totalBeratingAverage = 0;

            $totalPercentageAverage = 0;

            $data = ['18M' => $totalBags18, '19M' => $totalBags19, '20M' => $totalBags20, 'TOTAL_BAGS' => $totalBags, 'AB' => $totalBeratingAverage, 'AP' => $totalPercentageAverage];

        } else {
            // Total Pound and Total Average Berating
            $sum210 = $result->where('grade', '21.0')->sum('total_in_pound');
            $sum211 = $result->where('grade', '21.1')->sum('total_in_pound');
            $sum212 = $result->where('grade', '21.2')->sum('total_in_pound');
            $sum213 = $result->where('grade', '21.3')->sum('total_in_pound');
            $sum214 = $result->where('grade', '21.4')->sum('total_in_pound');
            $sum215 = $result->where('grade', '21.5')->sum('total_in_pound');
            $sum216 = $result->where('grade', '21.6')->sum('total_in_pound');
            $sum217 = $result->where('grade', '21.7')->sum('total_in_pound');
            $sum218 = $result->where('grade', '21.8')->sum('total_in_pound');
            $sum219 = $result->where('grade', '21.9')->sum('total_in_pound');
            $sum220 = $result->where('grade', '22.0')->sum('total_in_pound');
            $sum221 = $result->where('grade', '22.1')->sum('total_in_pound');
            $sum222 = $result->where('grade', '22.2')->sum('total_in_pound');
            $sum223 = $result->where('grade', '22.3')->sum('total_in_pound');
            $sum224 = $result->where('grade', '22.4')->sum('total_in_pound');
            $sum225 = $result->where('grade', '22.5')->sum('total_in_pound');
            $sum226 = $result->where('grade', '22.6')->sum('total_in_pound5');
            $sum227 = $result->where('grade', '22.7')->sum('total_in_pound');
            $sum228 = $result->where('grade', '22.8')->sum('total_in_pound');
            $sum229 = $result->where('grade', '22.9')->sum('total_in_pound');
            $sum230 = $result->where('grade', '23.0')->sum('total_in_pound');
            $sum231 = $result->where('grade', '23.1')->sum('total_in_pound');
            $sum232 = $result->where('grade', '23.2')->sum('total_in_pound');
            $sum233 = $result->where('grade', '23.3')->sum('total_in_pound');
            $sum234 = $result->where('grade', '23.4')->sum('total_in_pound');
            $sum235 = $result->where('grade', '23.5')->sum('total_in_pound');

            $totalPound = $sum210 + $sum211 + $sum212 + $sum213 + $sum214 + $sum215 + $sum216 + $sum217 + $sum218 + $sum219 + $sum220 + $sum221 + $sum222 + $sum223 + $sum224 + $sum225 + $sum226 + $sum227 + $sum228 + $sum229 + $sum230 + $sum231 + $sum232 + $sum233 + $sum234 + $sum235;

            $b185 = $sum * 18.5; $b186 = $sum186 * 18.6; $b187 = $sum187 * 18.7; $b188 = $sum188 * 18.8; $b189 = $sum189 * 18.9; $b190 = $sum190 * 19.0; $b191 = $sum191 * 19.1; $b192 = $sum192 * 19.2; $b193 = $sum193 * 19.3; $b194 = $sum194 * 19.4; $b195 = $sum195 * 19.5;  
            $b196 = $sum196 * 19.6; $b197 = $sum197 * 19.7;  $b198 = $sum198 * 19.8; $b199 = $sum199 * 19.9; $b200 = $sum200 * 20.0; $b201 = $sum201 * 20.1; $b202 = $sum202 * 20.2; $b203 = $sum203 * 20.3; 
            $b204 = $sum204 * 20.4; $b205 = $sum205 * 20.5; $b206 = $sum206 * 20.6; $b207 = $sum207 * 20.7; $b208 = $sum208 * 20.8; $b209 = $sum209 * 20.9;

            $totalBerating = $b185 + $b186 + $b187 + $b188 + $b189 + $b190 + $b191 + $b192 + $b193 + $b194 + $b195 + $b196 + $b197 + $b198 + $b199 + $b200 + $b201 +  $b202 + $b203 + $b204 + $b205 + $b206 + $b207 + $b208 + $b209;

            $beratingAverage = $totalBerating / $totalPound; 

            $totalBeratingAverage = number_format((float)$beratingAverage, 2, '.', '');

            // Percentage Average
            $percentage185 = $result->where('grade', '18.5');
            if($percentage185->isEmpty())
            {
                $sumPercentage185[] = 0;
            } else {
                foreach($percentage185 as $per185)
                {
                    $sumPercentage185[] = $per185->percentage_analysis * $per185->total_in_pound;
                }
            }
            $percentage186 = $result->where('grade', '18.6');
            if($percentage186->isEmpty())
            {
                $sumPercentage186[] = 0;
            } else {
                foreach($percentage186 as $per186)
                {
                    $sumPercentage186[] = $per186->percentage_analysis * $per186->total_in_pound;
                }
            }
            $percentage187 = $result->where('grade', '18.7');
            if($percentage187->isEmpty())
            {
                $sumPercentage187[] = 0;
            } else {
                foreach($percentage187 as $per187)
                {
                    $sumPercentage187[] = $per187->percentage_analysis * $per187->total_in_pound;
                }
            }
            $percentage188 = $result->where('grade', '18.8');
            if($percentage188->isEmpty())
            {
                $sumPercentage188[] = 0;
            } else {
                foreach($percentage188 as $per188)
                {
                    $sumPercentage188[] = $per188->percentage_analysis * $per188->total_in_pound;
                }
            }
            $percentage189 = $result->where('grade', '18.9');
            if($percentage189->isEmpty())
            {
                $sumPercentage189[] = 0;
            } else {
                foreach($percentage189 as $per189)
                {
                    $sumPercentage189[] = $per189->percentage_analysis * $per189->total_in_pound;
                }
            }
            $percentage190 = $result->where('grade', '19.0');
            if($percentage190->isEmpty())
            {
                $sumPercentage190[] = 0;
            } else {
                foreach($percentage190 as $per190)
                {
                    $sumPercentage190[] = $per190->percentage_analysis * $per190->total_in_pound;
                }
            }
            $percentage191 = $result->where('grade', '19.1');
            if($percentage191->isEmpty())
            {
                $sumPercentage191[] = 0;
            } else {
                foreach($percentage191 as $per191)
                {
                    $sumPercentage191[] = $per191->percentage_analysis * $per191->total_in_pound;
                }
            }
            $percentage192 = $result->where('grade', '19.2');
            if($percentage192->isEmpty())
            {
                $sumPercentage192[] = 0;
            } else {
                foreach($percentage192 as $per192)
                {
                    $sumPercentage192[] = $per192->percentage_analysis * $per192->total_in_pound;
                }
            }
            $percentage193 = $result->where('grade', '19.3');
            if($percentage193->isEmpty())
            {
                $sumPercentage193[] = 0;
            } else {
                foreach($percentage193 as $per193)
                {
                    $sumPercentage193[] = $per193->percentage_analysis * $per193->total_in_pound;
                }
            }
            $percentage194 = $result->where('grade', '19.4');
            if($percentage194->isEmpty())
            {
                $sumPercentage194[] = 0;
            } else {
                foreach($percentage194 as $per194)
                {
                    $sumPercentage194[] = $per194->percentage_analysis * $per194->total_in_pound;
                }
            }
            $percentage195 = $result->where('grade', '19.5');
            if($percentage195->isEmpty())
            {
                $sumPercentage195[] = 0;
            } else {
                foreach($percentage195 as $per195)
                {
                    $sumPercentage195[] = $per195->percentage_analysis * $per195->total_in_pound;
                }
            }
            $percentage196 = $result->where('grade', '19.6');
            if($percentage196->isEmpty())
            {
                $sumPercentage196[] = 0;
            } else {
                foreach($percentage196 as $per196)
                {
                    $sumPercentage196[] = $per196->percentage_analysis * $per196->total_in_pound;
                }
            }
            $percentage197 = $result->where('grade', '19.7');
            if($percentage197->isEmpty())
            {
                $sumPercentage197[] = 0;
            } else {
                foreach($percentage197 as $per197)
                {
                    $sumPercentage197[] = $per197->percentage_analysis * $per197->total_in_pound;
                }
            }
            $percentage198 = $result->where('grade', '19.8');
            if($percentage198->isEmpty())
            {
                $sumPercentage198[] = 0;
            } else {
                foreach($percentage198 as $per198)
                {
                    $sumPercentage198[] = $per198->percentage_analysis * $per198->total_in_pound;
                }
            }
            $percentage199 = $result->where('grade', '19.9');
            if($percentage199->isEmpty())
            {
                $sumPercentage199[] = 0;
            } else {
                foreach($percentage199 as $per199)
                {
                    $sumPercentage199[] = $per199->percentage_analysis * $per199->total_in_pound;
                }
            }
            $percentage200 = $result->where('grade', '20.0');
            if($percentage200->isEmpty())
            {
                $sumPercentage200[] = 0;
            } else {
                foreach($percentage200 as $per200)
                {
                    $sumPercentage200[] = $per200->percentage_analysis * $per200->total_in_pound;
                }
            }
            $percentage201 = $result->where('grade', '20.1');
            if($percentage201->isEmpty())
            {
                $sumPercentage201[] = 0;
            } else {
                foreach($percentage201 as $per201)
                {
                    $sumPercentage201[] = $per201->percentage_analysis * $per201->total_in_pound;
                }
            }
            $percentage202 = $result->where('grade', '20.2');
            if($percentage202->isEmpty())
            {
                $sumPercentage202[] = 0;
            } else {
                foreach($percentage202 as $per202)
                {
                    $sumPercentage202[] = $per202->percentage_analysis * $per202->total_in_pound;
                }
            }
            $percentage203 = $result->where('grade', '20.3');
            if($percentage203->isEmpty())
            {
                $sumPercentage203[] = 0;
            } else {
                foreach($percentage203 as $per203)
                {
                    $sumPercentage203[] = $per203->percentage_analysis * $per203->total_in_pound;
                }
            }
            $percentage204 = $result->where('grade', '20.4');
            if($percentage204->isEmpty())
            {
                $sumPercentage204[] = 0;
            } else {
                foreach($percentage204 as $per204)
                {
                    $sumPercentage204[] = $per204->percentage_analysis * $per204->total_in_pound;
                }
            }
            $percentage205 = $result->where('grade', '20.5');
            if($percentage205->isEmpty())
            {
                $sumPercentage205[] = 0;
            } else {
                foreach($percentage205 as $per205)
                {
                    $sumPercentage205[] = $per205->percentage_analysis * $per205->total_in_pound;
                }
            }
            $percentage206 = $result->where('grade', '20.6');
            if($percentage206->isEmpty())
            {
                $sumPercentage206[] = 0;
            } else {
                foreach($percentage206 as $per206)
                {
                    $sumPercentage206[] = $per206->percentage_analysis * $per206->total_in_pound;
                }
            }
            $percentage207 = $result->where('grade', '20.7');
            if($percentage207->isEmpty())
            {
                $sumPercentage207[] = 0;
            } else {
                foreach($percentage207 as $per207)
                {
                    $sumPercentage207[] = $per207->percentage_analysis * $per207->total_in_pound;
                }
            }
            $percentage208 = $result->where('grade', '20.8');
            if($percentage208->isEmpty())
            {
                $sumPercentage208[] = 0;
            } else {
                foreach($percentage208 as $per208)
                {
                    $sumPercentage208[] = $per208->percentage_analysis * $per208->total_in_pound;
                }
            }
            $percentage209 = $result->where('grade', '20.9');
            if($percentage209->isEmpty())
            {
                $sumPercentage209[] = 0;
            } else {
                foreach($percentage209 as $per209)
                {
                    $sumPercentage209[] = $per209->percentage_analysis * $per209->total_in_pound;
                }
            }

            $totalPercentage185 = array_sum($sumPercentage185); 
            $totalPercentage186 = array_sum($sumPercentage186); 
            $totalPercentage187 = array_sum($sumPercentage187); 
            $totalPercentage188 = array_sum($sumPercentage188); 
            $totalPercentage189 = array_sum($sumPercentage189); 
            $totalPercentage190 = array_sum($sumPercentage190);
            $totalPercentage191 = array_sum($sumPercentage191);
            $totalPercentage192 = array_sum($sumPercentage192);
            $totalPercentage193 = array_sum($sumPercentage193);
            $totalPercentage194 = array_sum($sumPercentage194); 
            $totalPercentage195 = array_sum($sumPercentage195); 
            $totalPercentage196 = array_sum($sumPercentage196);
            $totalPercentage197 = array_sum($sumPercentage197);
            $totalPercentage198 = array_sum($sumPercentage198); 
            $totalPercentage199 = array_sum($sumPercentage199);
            $totalPercentage200 = array_sum($sumPercentage200); 
            $totalPercentage201 = array_sum($sumPercentage201);
            $totalPercentage202 = array_sum($sumPercentage202);
            $totalPercentage203 = array_sum($sumPercentage203);
            $totalPercentage204 = array_sum($sumPercentage204); 
            $totalPercentage205 = array_sum($sumPercentage205);
            $totalPercentage206 = array_sum($sumPercentage206);
            $totalPercentage207 = array_sum($sumPercentage207);
            $totalPercentage208 = array_sum($sumPercentage208); 
            $totalPercentage209 = array_sum($sumPercentage209);

            $totalPercentage = $totalPercentage185 + $totalPercentage186 + $totalPercentage187 + $totalPercentage188 + $totalPercentage189 + $totalPercentage190 + $totalPercentage191 + $totalPercentage192 + $totalPercentage193 + $totalPercentage194 + $totalPercentage195 + $totalPercentage196 + $totalPercentage197 + $totalPercentage198 + $totalPercentage199 + $totalPercentage200 + $totalPercentage201 + $totalPercentage202 + $totalPercentage203 + $totalPercentage204 + $totalPercentage205 + $totalPercentage206 + $totalPercentage207 + $totalPercentage208 + $totalPercentage209;

            // $p185 = $sum185 * $percentage185; $p186 = $sum186 * $percentage186; $p187 = $sum187 * $percentage187; $p188 = $sum188 * $percentage188; $p189 = $sum189 * $percentage189; $p190 = $sum190 * $percentage190; $p191 = $sum191 * $percentage191; $p192 = $sum192 * $percentage192; $p193 = $sum193 * $percentage193; $p194 = $sum194 * $percentage194; $p195 = $sum195 * $percentage195;  
            // $p196 = $sum196 * $percentage196; $p197 = $sum197 * $percentage197;  $p198 = $sum198 * $percentage198; $p199 = $sum199 * $percentage199; $p200 = $sum200 * $percentage200; $p201 = $sum201 * $percentage201; $p202 = $sum202 * $percentage202; $p203 = $sum203 * $percentage203; 
            // $p204 = $sum204 * $percentage204; $p205 = $sum205 * $percentage205; $p206 = $sum206 * $percentage206; $p207 = $sum207 * $percentage207; $p208 = $sum208 * $percentage208; $p209 = $sum209 * $percentage209;

            // $totalPercentage =  $p185 + $p186 + $p187 + $p188 + $p189 + $p190 + $p191 + $p192 + $p193 + $p194 + $p195 + $p196 + $p197 + $p198 + $p199 + $p200 + $p201 +  $p202 + $p203 + $p204 + $p205 + $p206 + $p207 + $p208 + $p209;
            
            $percentageAverage = $totalPercentage / $totalPound;

            $totalPercentageAverage = number_format((float)$percentageAverage, 2, '.', '');

            // 
            $bags = $totalPound / 80;
            $str_arr = explode('.',$bags);
            $str = str_replace($str_arr[0], '0.', $str_arr[0]);
            $strP = $str_arr[1] ?? 0;
            $substr = $str.''.$strP;
            $answer = $substr * 80;
            $totalBags = [
                'bags' => $str_arr[0],
                'pounds' => number_format((float)$answer, 0, '.', '')
            ];
  
            $bag18 = $sum185 + $sum186 + $sum187 + $sum188 + $sum189;
            $bag19 = $sum190 + $sum191 + $sum192 + $sum193 + $sum194 + $sum195 + $sum196 + $sum197 + $sum198 + $sum199;
            $bag20 = $sum200 + $sum201 + $sum202 + $sum203 + $sum204 + $sum205 + $sum206 + $sum207 + $sum208 + $sum209;

            if($bag18 > 0)
            {
                $bag18Bags = $bag18 / 80;
                $str_arr18 = explode('.',$bag18Bags);
                $str18 = str_replace($str_arr18[0], '0.', $str_arr18[0]);
                $strPound = $str_arr18[1] ?? 0;
                $substr18 = $str18.''.$strPound;
                $answer18 = $substr18 * 80;
                $totalBags18 = [
                    'bags' => $str_arr18[0],
                    'pounds' => number_format((float)$answer18, 0, '.', '')
                ];
            } else {
                $totalBags18 = [
                    'bags' => 0,
                    'pounds' => 0
                ];
            }

            if($bag19 > 0)
            {
                $bag19Bags = $bag19 / 80;
                $str_arr19 = explode('.',$bag19Bags);
                $str19 = str_replace($str_arr19[0], '0.', $str_arr19[0]);
                $strPound = $str_arr19[1] ?? 0;
                $substr19 = $str19.''.$strPound;
                $answer19 = $substr19 * 80;
                $totalBags19 = [
                    'bags' => $str_arr19[0],
                    'pounds' => number_format((float)$answer19, 0, '.', '')
                ];
            } else {
                $totalBags19 = [
                    'bags' => 0,
                    'pounds' => 0
                ];
            }

            if($bag20 > 0)
            {
                $bag20Bags = $bag20 / 80;
                $str_arr20 = explode('.',$bag20Bags);
                $str20 = str_replace($str_arr20[0], '0.', $str_arr20[0]);
                $strPound = $str_arr20[1] ?? 0;
                $substr20 = $str20.''.$strPound;
                $answer20 = $substr20 * 80;
                $totalBags20 = [
                    'bags' => $str_arr20[0],
                    'pounds' => number_format((float)$answer20, 0, '.', '')
                ];
            } else {
                $totalBags20 = [
                    'bags' => 0,
                    'pounds' => 0
                ];
            }
            
            $data = ['18M' => $totalBags18, '19M' => $totalBags19, '20M' => $totalBags20, 'TOTAL_BAGS' => $totalBags, 'AB' => $totalBeratingAverage, 'AP' => $totalPercentageAverage];
        }

        if (request()->ajax()) {
            return DataTables::of($analysis)->make(true);
        }

        return view('admin.weekly_analysis.columbite_pound', [
            'analysis' => $analysis,
            'data' => $data,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'manager' => $request->manager
        ]);
    }

    public function monthly_analysis_lower_grade_columbite_kg(Request $request)
    {
        if($request->start_date == null && $request->end_date == null && $request->manager == null)
        {
            $columbitePayment = PaymentReceiptColumbite::latest()->where('type', 'kg')->get();
        } elseif($request->start_date !== null && $request->end_date !== null && $request->manager == null)
        {
            $columbitePayment = PaymentReceiptColumbite::latest()->where('type', 'kg')->whereBetween('date_of_purchase', [$request->start_date, $request->end_date])->get();
        } elseif($request->start_date == null && $request->end_date == null && $request->manager !== null)
        {
            $columbitePayment = PaymentReceiptColumbite::latest()->where('type', 'kg')->where('staff', $request->manager)->get();
        } else {
            $columbitePayment = PaymentReceiptColumbite::latest()->where('type', 'kg')->where('staff', $request->manager)->whereBetween('date_of_purchase', [$request->start_date, $request->end_date])->get();
        }

        if($columbitePayment->isEmpty())
        {
            $analysis = [];

        } else {
            
            $beratingCalculation = BeratingCalculation::latest()->get();

            foreach($columbitePayment as $colpayment)
            {
                $beratingpayment = BeratingCalculation::find($colpayment->grade);

                foreach($beratingCalculation as $berating)
                {
                    if($berating->grade == $beratingpayment->grade)
                    {
                        $data[] = ['date' => $colpayment->date_of_purchase, 'berating' => $berating->grade, 'total' => $colpayment->total_in_kg];

                        $analysis = array_values(array_unique($data, 0));
                                    
                        rsort($analysis);
                    }
                }
            }
        }

        // Calculation Starts
        if($request->start_date == null && $request->end_date == null && $request->manager == null)
        {
            $result =  PaymentReceiptColumbite::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_columbites.grade')->latest()->where('payment_receipt_columbites.type', 'kg')  
                                ->get(['payment_receipt_columbites.date_of_purchase', 'payment_receipt_columbites.total_in_kg', 'payment_receipt_columbites.percentage_analysis', 'berating_calculations.grade', 'payment_receipt_columbites.created_at', 'payment_receipt_columbites.updated_at']);
        } elseif($request->start_date !== null && $request->end_date !== null && $request->manager == null)
        {
            $result =  PaymentReceiptColumbite::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_columbites.grade')->latest()->where('payment_receipt_columbites.type', 'kg')  
                                ->whereBetween('payment_receipt_columbites.date_of_purchase', [$request->start_date, $request->end_date])
                                ->get(['payment_receipt_columbites.date_of_purchase', 'payment_receipt_columbites.total_in_kg', 'payment_receipt_columbites.percentage_analysis', 'berating_calculations.grade', 'payment_receipt_columbites.created_at', 'payment_receipt_columbites.updated_at']);
        } elseif($request->start_date == null && $request->end_date == null && $request->manager !== null)
        { 
            $result =  PaymentReceiptColumbite::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_columbites.grade')->latest()->where('payment_receipt_columbites.type', 'kg')  
                                ->where('payment_receipt_columbites.staff', $request->manager)
                                ->get(['payment_receipt_columbites.date_of_purchase', 'payment_receipt_columbites.total_in_kg', 'payment_receipt_columbites.percentage_analysis', 'berating_calculations.grade', 'payment_receipt_columbites.created_at', 'payment_receipt_columbites.updated_at']);
        } else {
            $result =  PaymentReceiptColumbite::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_columbites.grade')->latest()->where('payment_receipt_columbites.type', 'kg')  
                                ->where('payment_receipt_columbites.staff', $request->manager)->whereBetween('payment_receipt_columbites.date_of_purchase', [$request->start_date, $request->end_date])
                                ->get(['payment_receipt_columbites.date_of_purchase', 'payment_receipt_columbites.total_in_kg', 'payment_receipt_columbites.percentage_analysis', 'berating_calculations.grade', 'payment_receipt_columbites.created_at', 'payment_receipt_columbites.updated_at']);
        }
        
        if($result->isEmpty())
        {
            $totalBags = [
                'bags' => 0,
                'pounds' => 0
            ];

            $totalBags18 = [
                'bags' => 0,
                'pounds' => 0
            ];

            $totalBags19 = [
                'bags' => 0,
                'pounds' => 0
            ];

            $totalBags20 = [
                'bags' => 0,
                'pounds' => 0
            ];

            $totalBeratingAverage = 0;

            $totalPercentageAverage = 0;

            $data = ['18M' => $totalBags18, '19M' => $totalBags19, '20M' => $totalBags20, 'TOTAL_BAGS' => $totalBags, 'AB' => $totalBeratingAverage, 'AP' => $totalPercentageAverage];

        } else {
            // Total Kg and Total Average Berating
            $sum185 = $result->where('grade', '18.5')->sum('total_in_kg');
            $sum186 = $result->where('grade', '18.6')->sum('total_in_kg');
            $sum187 = $result->where('grade', '18.7')->sum('total_in_kg');
            $sum188 = $result->where('grade', '18.8')->sum('total_in_kg');
            $sum189 = $result->where('grade', '18.9')->sum('total_in_kg');
            $sum190 = $result->where('grade', '19.0')->sum('total_in_kg');
            $sum191 = $result->where('grade', '19.1')->sum('total_in_kg');
            $sum192 = $result->where('grade', '19.2')->sum('total_in_kg');
            $sum193 = $result->where('grade', '19.3')->sum('total_in_kg');
            $sum194 = $result->where('grade', '19.4')->sum('total_in_kg');
            $sum195 = $result->where('grade', '19.5')->sum('total_in_kg');
            $sum196 = $result->where('grade', '19.6')->sum('total_in_kg');
            $sum197 = $result->where('grade', '19.7')->sum('total_in_kg');
            $sum198 = $result->where('grade', '19.8')->sum('total_in_kg');
            $sum199 = $result->where('grade', '19.9')->sum('total_in_kg');
            $sum200 = $result->where('grade', '20.0')->sum('total_in_kg');
            $sum201 = $result->where('grade', '20.1')->sum('total_in_kg');
            $sum202 = $result->where('grade', '20.2')->sum('total_in_kg');
            $sum203 = $result->where('grade', '20.3')->sum('total_in_kg');
            $sum204 = $result->where('grade', '20.4')->sum('total_in_kg');
            $sum205 = $result->where('grade', '20.5')->sum('total_in_kg');
            $sum206 = $result->where('grade', '20.6')->sum('total_in_kg');
            $sum207 = $result->where('grade', '20.7')->sum('total_in_kg');
            $sum208 = $result->where('grade', '20.8')->sum('total_in_kg');
            $sum209 = $result->where('grade', '20.9')->sum('total_in_kg');

            $totalKg = $sum185 + $sum186 + $sum187 + $sum188 + $sum189 + $sum190 + $sum191 + $sum192 + $sum193 + $sum194 + $sum195 + $sum196 + $sum197 + $sum198 + $sum199 + $sum200 + $sum201 + $sum202 + $sum203 + $sum204 + $sum205 + $sum206 + $sum207 + $sum208 + $sum209;

            $b185 = $sum185 * 18.5; $b186 = $sum186 * 18.6; $b187 = $sum187 * 18.7; $b188 = $sum188 * 18.8; $b189 = $sum189 * 18.9; $b190 = $sum190 * 19.0; $b191 = $sum191 * 19.1; $b192 = $sum192 * 19.2; $b193 = $sum193 * 19.3; $b194 = $sum194 * 19.4; $b195 = $sum195 * 19.5;  
            $b196 = $sum196 * 19.6; $b197 = $sum197 * 19.7;  $b198 = $sum198 * 19.8; $b199 = $sum199 * 19.9; $b200 = $sum200 * 20.0; $b201 = $sum201 * 20.1; $b202 = $sum202 * 20.2; $b203 = $sum203 * 20.3; 
            $b204 = $sum204 * 20.4; $b205 = $sum205 * 20.5; $b206 = $sum206 * 20.6; $b207 = $sum207 * 20.7; $b208 = $sum208 * 20.8; $b209 = $sum209 * 20.9;

            $totalBerating = $b185 + $b186 + $b187 + $b188 + $b189 + $b190 + $b191 + $b192 + $b193 + $b194 + $b195 + $b196 + $b197 + $b198 + $b199 + $b200 + $b201 +  $b202 + $b203 + $b204 + $b205 + $b206 + $b207 + $b208 + $b209;

            $beratingAverage = $totalBerating / $totalKg; 

            $totalBeratingAverage = number_format((float)$beratingAverage, 2, '.', '');

            // Percentage Average
            $percentage185 = $result->where('grade', '18.5');
            if($percentage185->isEmpty())
            {
                $sumPercentage185[] = 0;
            } else {
                foreach($percentage185 as $per185)
                {
                    $sumPercentage185[] = $per185->percentage_analysis * $per185->total_in_kg;
                }
            }
            $percentage186 = $result->where('grade', '18.6');
            if($percentage186->isEmpty())
            {
                $sumPercentage186[] = 0;
            } else {
                foreach($percentage186 as $per186)
                {
                    $sumPercentage186[] = $per186->percentage_analysis * $per186->total_in_kg;
                }
            }
            $percentage187 = $result->where('grade', '18.7');
            if($percentage187->isEmpty())
            {
                $sumPercentage187[] = 0;
            } else {
                foreach($percentage187 as $per187)
                {
                    $sumPercentage187[] = $per187->percentage_analysis * $per187->total_in_kg;
                }
            }
            $percentage188 = $result->where('grade', '18.8');
            if($percentage188->isEmpty())
            {
                $sumPercentage188[] = 0;
            } else {
                foreach($percentage188 as $per188)
                {
                    $sumPercentage188[] = $per188->percentage_analysis * $per188->total_in_kg;
                }
            }
            $percentage189 = $result->where('grade', '18.9');
            if($percentage189->isEmpty())
            {
                $sumPercentage189[] = 0;
            } else {
                foreach($percentage189 as $per189)
                {
                    $sumPercentage189[] = $per189->percentage_analysis * $per189->total_in_kg;
                }
            }
            $percentage190 = $result->where('grade', '19.0');
            if($percentage190->isEmpty())
            {
                $sumPercentage190[] = 0;
            } else {
                foreach($percentage190 as $per190)
                {
                    $sumPercentage190[] = $per190->percentage_analysis * $per190->total_in_kg;
                }
            }
            $percentage191 = $result->where('grade', '19.1');
            if($percentage191->isEmpty())
            {
                $sumPercentage191[] = 0;
            } else {
                foreach($percentage191 as $per191)
                {
                    $sumPercentage191[] = $per191->percentage_analysis * $per191->total_in_kg;
                }
            }
            $percentage192 = $result->where('grade', '19.2');
            if($percentage192->isEmpty())
            {
                $sumPercentage192[] = 0;
            } else {
                foreach($percentage192 as $per192)
                {
                    $sumPercentage192[] = $per192->percentage_analysis * $per192->total_in_kg;
                }
            }
            $percentage193 = $result->where('grade', '19.3');
            if($percentage193->isEmpty())
            {
                $sumPercentage193[] = 0;
            } else {
                foreach($percentage193 as $per193)
                {
                    $sumPercentage193[] = $per193->percentage_analysis * $per193->total_in_kg;
                }
            }
            $percentage194 = $result->where('grade', '19.4');
            if($percentage194->isEmpty())
            {
                $sumPercentage194[] = 0;
            } else {
                foreach($percentage194 as $per194)
                {
                    $sumPercentage194[] = $per194->percentage_analysis * $per194->total_in_kg;
                }
            }
            $percentage195 = $result->where('grade', '19.5');
            if($percentage195->isEmpty())
            {
                $sumPercentage195[] = 0;
            } else {
                foreach($percentage195 as $per195)
                {
                    $sumPercentage195[] = $per195->percentage_analysis * $per195->total_in_kg;
                }
            }
            $percentage196 = $result->where('grade', '19.6');
            if($percentage196->isEmpty())
            {
                $sumPercentage196[] = 0;
            } else {
                foreach($percentage196 as $per196)
                {
                    $sumPercentage196[] = $per196->percentage_analysis * $per196->total_in_kg;
                }
            }
            $percentage197 = $result->where('grade', '19.7');
            if($percentage197->isEmpty())
            {
                $sumPercentage197[] = 0;
            } else {
                foreach($percentage197 as $per197)
                {
                    $sumPercentage197[] = $per197->percentage_analysis * $per197->total_in_kg;
                }
            }
            $percentage198 = $result->where('grade', '19.8');
            if($percentage198->isEmpty())
            {
                $sumPercentage198[] = 0;
            } else {
                foreach($percentage198 as $per198)
                {
                    $sumPercentage198[] = $per198->percentage_analysis * $per198->total_in_kg;
                }
            }
            $percentage199 = $result->where('grade', '19.9');
            if($percentage199->isEmpty())
            {
                $sumPercentage199[] = 0;
            } else {
                foreach($percentage199 as $per199)
                {
                    $sumPercentage199[] = $per199->percentage_analysis * $per199->total_in_kg;
                }
            }
            $percentage200 = $result->where('grade', '20.0');
            if($percentage200->isEmpty())
            {
                $sumPercentage200[] = 0;
            } else {
                foreach($percentage200 as $per200)
                {
                    $sumPercentage200[] = $per200->percentage_analysis * $per200->total_in_kg;
                }
            }
            $percentage201 = $result->where('grade', '20.1');
            if($percentage201->isEmpty())
            {
                $sumPercentage201[] = 0;
            } else {
                foreach($percentage201 as $per201)
                {
                    $sumPercentage201[] = $per201->percentage_analysis * $per201->total_in_kg;
                }
            }
            $percentage202 = $result->where('grade', '20.2');
            if($percentage202->isEmpty())
            {
                $sumPercentage202[] = 0;
            } else {
                foreach($percentage202 as $per202)
                {
                    $sumPercentage202[] = $per202->percentage_analysis * $per202->total_in_kg;
                }
            }
            $percentage203 = $result->where('grade', '20.3');
            if($percentage203->isEmpty())
            {
                $sumPercentage203[] = 0;
            } else {
                foreach($percentage203 as $per203)
                {
                    $sumPercentage203[] = $per203->percentage_analysis * $per203->total_in_kg;
                }
            }
            $percentage204 = $result->where('grade', '20.4');
            if($percentage204->isEmpty())
            {
                $sumPercentage204[] = 0;
            } else {
                foreach($percentage204 as $per204)
                {
                    $sumPercentage204[] = $per204->percentage_analysis * $per204->total_in_kg;
                }
            }
            $percentage205 = $result->where('grade', '20.5');
            if($percentage205->isEmpty())
            {
                $sumPercentage205[] = 0;
            } else {
                foreach($percentage205 as $per205)
                {
                    $sumPercentage205[] = $per205->percentage_analysis * $per205->total_in_kg;
                }
            }
            $percentage206 = $result->where('grade', '20.6');
            if($percentage206->isEmpty())
            {
                $sumPercentage206[] = 0;
            } else {
                foreach($percentage206 as $per206)
                {
                    $sumPercentage206[] = $per206->percentage_analysis * $per206->total_in_kg;
                }
            }
            $percentage207 = $result->where('grade', '20.7');
            if($percentage207->isEmpty())
            {
                $sumPercentage207[] = 0;
            } else {
                foreach($percentage207 as $per207)
                {
                    $sumPercentage207[] = $per207->percentage_analysis * $per207->total_in_kg;
                }
            }
            $percentage208 = $result->where('grade', '20.8');
            if($percentage208->isEmpty())
            {
                $sumPercentage208[] = 0;
            } else {
                foreach($percentage208 as $per208)
                {
                    $sumPercentage208[] = $per208->percentage_analysis * $per208->total_in_kg;
                }
            }
            $percentage209 = $result->where('grade', '20.9');
            if($percentage209->isEmpty())
            {
                $sumPercentage209[] = 0;
            } else {
                foreach($percentage209 as $per209)
                {
                    $sumPercentage209[] = $per209->percentage_analysis * $per209->total_in_kg;
                }
            }

            $totalPercentage185 = array_sum($sumPercentage185); 
            $totalPercentage186 = array_sum($sumPercentage186); 
            $totalPercentage187 = array_sum($sumPercentage187); 
            $totalPercentage188 = array_sum($sumPercentage188); 
            $totalPercentage189 = array_sum($sumPercentage189); 
            $totalPercentage190 = array_sum($sumPercentage190);
            $totalPercentage191 = array_sum($sumPercentage191);
            $totalPercentage192 = array_sum($sumPercentage192);
            $totalPercentage193 = array_sum($sumPercentage193);
            $totalPercentage194 = array_sum($sumPercentage194); 
            $totalPercentage195 = array_sum($sumPercentage195); 
            $totalPercentage196 = array_sum($sumPercentage196);
            $totalPercentage197 = array_sum($sumPercentage197);
            $totalPercentage198 = array_sum($sumPercentage198); 
            $totalPercentage199 = array_sum($sumPercentage199);
            $totalPercentage200 = array_sum($sumPercentage200); 
            $totalPercentage201 = array_sum($sumPercentage201);
            $totalPercentage202 = array_sum($sumPercentage202);
            $totalPercentage203 = array_sum($sumPercentage203);
            $totalPercentage204 = array_sum($sumPercentage204); 
            $totalPercentage205 = array_sum($sumPercentage205);
            $totalPercentage206 = array_sum($sumPercentage206);
            $totalPercentage207 = array_sum($sumPercentage207);
            $totalPercentage208 = array_sum($sumPercentage208); 
            $totalPercentage209 = array_sum($sumPercentage209);

            $totalPercentage = $totalPercentage185 + $totalPercentage186 + $totalPercentage187 + $totalPercentage188 + $totalPercentage189 + $totalPercentage190 + $totalPercentage191 + $totalPercentage192 + $totalPercentage193 + $totalPercentage194 + $totalPercentage195 + $totalPercentage196 + $totalPercentage197 + $totalPercentage198 + $totalPercentage199 + $totalPercentage200 + $totalPercentage201 + $totalPercentage202 + $totalPercentage203 + $totalPercentage204 + $totalPercentage205 + $totalPercentage206 + $totalPercentage207 + $totalPercentage208 + $totalPercentage209;

            // $p185 = $sum185 * $percentage185; $p186 = $sum186 * $percentage186; $p187 = $sum187 * $percentage187; $p188 = $sum188 * $percentage188; $p189 = $sum189 * $percentage189; $p190 = $sum190 * $percentage190; $p191 = $sum191 * $percentage191; $p192 = $sum192 * $percentage192; $p193 = $sum193 * $percentage193; $p194 = $sum194 * $percentage194; $p195 = $sum195 * $percentage195;  
            // $p196 = $sum196 * $percentage196; $p197 = $sum197 * $percentage197;  $p198 = $sum198 * $percentage198; $p199 = $sum199 * $percentage199; $p200 = $sum200 * $percentage200; $p201 = $sum201 * $percentage201; $p202 = $sum202 * $percentage202; $p203 = $sum203 * $percentage203; 
            // $p204 = $sum204 * $percentage204; $p205 = $sum205 * $percentage205; $p206 = $sum206 * $percentage206; $p207 = $sum207 * $percentage207; $p208 = $sum208 * $percentage208; $p209 = $sum209 * $percentage209;

            // $totalPercentage =  $p185 + $p186 + $p187 + $p188 + $p189 + $p190 + $p191 + $p192 + $p193 + $p194 + $p195 + $p196 + $p197 + $p198 + $p199 + $p200 + $p201 +  $p202 + $p203 + $p204 + $p205 + $p206 + $p207 + $p208 + $p209;
            
            $percentageAverage = $totalPercentage / $totalKg;

            $totalPercentageAverage = number_format((float)$percentageAverage, 2, '.', '');

            // 
            $bags = $totalKg / 80;
            $str_arr = explode('.',$bags);
            $str = str_replace($str_arr[0], '0.', $str_arr[0]);
            $strP = $str_arr[1] ?? 0;
            $substr = $str.''.$strP;
            $answer = $substr * 80;
            $totalBags = [
                'bags' => $str_arr[0],
                'pounds' => number_format((float)$answer, 0, '.', '')
            ];
  
            $bag18 = $sum185 + $sum186 + $sum187 + $sum188 + $sum189;
            $bag19 = $sum190 + $sum191 + $sum192 + $sum193 + $sum194 + $sum195 + $sum196 + $sum197 + $sum198 + $sum199;
            $bag20 = $sum200 + $sum201 + $sum202 + $sum203 + $sum204 + $sum205 + $sum206 + $sum207 + $sum208 + $sum209;

            if($bag18 > 0)
            {
                $bag18Bags = $bag18 / 80;
                $str_arr18 = explode('.',$bag18Bags);
                $str18 = str_replace($str_arr18[0], '0.', $str_arr18[0]);
                $strPound = $str_arr18[1] ?? 0;
                $substr18 = $str18.''.$strPound;
                $answer18 = $substr18 * 80;
                $totalBags18 = [
                    'bags' => $str_arr18[0],
                    'pounds' => number_format((float)$answer18, 0, '.', '')
                ];
            } else {
                $totalBags18 = [
                    'bags' => 0,
                    'pounds' => 0
                ];
            }

            if($bag19 > 0)
            {
                $bag19Bags = $bag19 / 80;
                $str_arr19 = explode('.',$bag19Bags);
                $str19 = str_replace($str_arr19[0], '0.', $str_arr19[0]);
                $strPound = $str_arr19[1] ?? 0;
                $substr19 = $str19.''.$strPound;
                $answer19 = $substr19 * 80;
                $totalBags19 = [
                    'bags' => $str_arr19[0],
                    'pounds' => number_format((float)$answer19, 0, '.', '')
                ];
            } else {
                $totalBags19 = [
                    'bags' => 0,
                    'pounds' => 0
                ];
            }

            if($bag20 > 0)
            {
                $bag20Bags = $bag20 / 80;
                $str_arr20 = explode('.',$bag20Bags);
                $str20 = str_replace($str_arr20[0], '0.', $str_arr20[0]);
                $strPound = $str_arr20[1] ?? 0;
                $substr20 = $str20.''.$strPound;
                $answer20 = $substr20 * 80;
                $totalBags20 = [
                    'bags' => $str_arr20[0],
                    'pounds' => number_format((float)$answer20, 0, '.', '')
                ];
            } else {
                $totalBags20 = [
                    'bags' => 0,
                    'pounds' => 0
                ];
            }
            
            $data = ['18M' => $totalBags18, '19M' => $totalBags19, '20M' => $totalBags20, 'TOTAL_BAGS' => $totalBags, 'AB' => $totalBeratingAverage, 'AP' => $totalPercentageAverage];
        }

        if (request()->ajax()) {
            return DataTables::of($analysis)->make(true);
        }

        return view('admin.weekly_analysis.columbite_kg', [
            'analysis' => $analysis,
            'data' => $data,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'manager' => $request->manager
        ]);
    }  

    public function daily_balance(Request $request)
    {
        if($request->start_date == null && $request->end_date == null)
        {
            $balances = Balance::latest()->get();
        } else {
            $balances = Balance::latest()->whereBetween('date', [$request->start_date, $request->end_date])->get();
        }

        $today = Carbon::now()->format('Y-m-d');

        $totalBalance = Balance::whereDate('date', $today)->first()->starting_balance ?? 0;
        $totalAdditionalIncome = Balance::whereDate('date', $today)->first()->additional_income ?? 0;
        $totalRemainingBalance = Balance::whereDate('date', '!=', $today)->sum('remaining_balance') ?? 0;

        $totalStartingBalance = $totalBalance + $totalAdditionalIncome + $totalRemainingBalance;

        return view('admin.balance')->with([
            'balances' => $balances,
            'totalStartingBalance' => $totalStartingBalance,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date
        ]);
    }

    public function update_daily_balance($id, Request $request)
    {
        $this->validate($request, [
            'starting_balance' => ['required', 'numeric']
        ]);

        if ($request->additional_income !== null) {
            $this->validate($request, [
                'additional_income' => ['numeric']
            ]); 
        }   

        if ($request->amount_used !== null) {
            $this->validate($request, [
                'amount_used' => ['numeric'],
            ]); 
        }

        $response = $request->starting_balance + $request->additional_income;

        if($request->amount_used > $response) 
        {
            return back()->with([
                'type' => 'danger',
                'message' => "Amount used can't be greater than the total of starting balance and additional income."
            ]);
        }
        
        $finder = Crypt::decrypt($id);

        $startBalance = Balance::find($finder);

        if($request->starting_balance == $startBalance->starting_balance)
        {
            if($request->additional_income == null && $request->amount_used !== null)
            {
                $startBalance->update([
                    'amount_used' => $request->amount_used,
                    'remaining_balance' => $startBalance->starting_balance + $startBalance->additional_income - $request->amount_used
                ]);
            } elseif($request->additional_income == !null && $request->amount_used == null)
            {
                $startBalance->update([
                    'additional_income' => $request->additional_income,
                    'remaining_balance' => $startBalance->starting_balance + $request->additional_income - $startBalance->amount_used
                ]);
            } else {
                $startBalance->update([
                    'additional_income' => $request->additional_income,
                    'amount_used' => $request->amount_used,
                    'remaining_balance' => $startBalance->starting_balance + $request->additional_income - $request->amount_used
                ]);
            }

            return back()->with([
                'alertType' => 'success',
                'message' => 'Daily starting balance updated successfully.'
            ]);
        } else {
            if($request->additional_income == null && $request->amount_used !== null)
            {
                $startBalance->update([
                    'starting_balance' => $request->starting_balance,
                    'amount_used' => $request->amount_used,
                    'remaining_balance' => $request->starting_balance + $startBalance->additional_income - $request->amount_used
                ]);
            } elseif($request->additional_income == !null && $request->amount_used == null)
            {
                $startBalance->update([
                    'starting_balance' => $request->starting_balance,
                    'additional_income' => $request->additional_income,
                    'remaining_balance' => $request->starting_balance + $request->additional_income - $startBalance->amount_used
                ]);
            } else {
                $startBalance->update([
                    'starting_balance' => $request->starting_balance,
                    'additional_income' => $request->additional_income,
                    'amount_used' => $request->amount_used,
                    'remaining_balance' => $request->starting_balance + $request->additional_income - $request->amount_used
                ]);
            }

            return back()->with([
                'alertType' => 'success',
                'message' => 'Daily starting balance updated successfully.'
            ]);
        }
    }

    public function delete_daily_balance($id)
    {
        $finder = Crypt::decrypt($id);

        Balance::find($finder)->delete();

        return back()->with([
            'alertType' => 'success',
            'message' => 'Balance deleted successfully!'
        ]);
    }
}