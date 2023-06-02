<?php

namespace App\Http\Controllers;

use App\Models\AnalysisCalculation;
use App\Models\BeratingCalculation;
use App\Models\ColumbiteMaterial;
use App\Models\ColumbitePaymentAnalysis;
use App\Models\Expenses;
use App\Models\Manager;
use App\Models\Notification;
use App\Models\TinPaymentAnalysis;
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

        $user = User::where('account_type', '!=', 'Administrator')->get()->count();
        $manager = Manager::get()->count();
        $staffs = $user + $manager;

        return view('admin.dashboard', [
            'moment' => $moment,
            'staffs' => $staffs
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

    public function managers()
    {
        return view('admin.managers.view');
    }

    public function manager_add()
    {
        return view('admin.managers.index');
    }

    public function manager_post(Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['string', 'email', 'max:255', 'unique:managers'],
        ]);
        
        if($request->status !== null && $request->status == 'false')
        {
            $manager =  Manager::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'gender' => $request->gender,
                'status' => false
            ]);
        } else {
            $manager = Manager::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'gender' => $request->gender,
                'status' => true
            ]);
        }

        return back()->with([
            'alertType' => 'success',
            'back' => route('admin.managers'),
            'message' => $manager->name. ' added successfully!'
        ]);
    }

    public function manager_edit($id)
    {
        $finder = Crypt::decrypt($id);

        $manager = Manager::find($finder);

        return view ('admin.managers.edit', [
            'manager' => $manager
        ]);
    }

    public function manager_update(Request $request, $id)
    {
        $finder = Crypt::decrypt($id);

        $manager = Manager::find($finder);

        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
        ]);
        
        if($request->status !== null && $request->status == '0')
        {
            if($manager->email == $request->email)
            {
                $manager->update([
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'gender' => $request->gender,
                    'status' => false
                ]);
            } else {
                //Validate Request
                $this->validate($request, [
                    'email' => ['string', 'email', 'max:255', 'unique:managers'],
                ]);

                $manager->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'gender' => $request->gender,
                    'status' => false
                ]);
            }
            
        } else {
            if($manager->email == $request->email)
            {
                $manager->update([
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'gender' => $request->gender,
                    'status' => true
                ]);
            } else {
                //Validate Request
                $this->validate($request, [
                    'email' => ['string', 'email', 'max:255', 'unique:managers'],
                ]);

                $manager->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'gender' => $request->gender,
                    'status' => true
                ]);
            }
        }

        return back()->with([
            'alertType' => 'success',
            'message' => $manager->name. ' updated successfully!'
        ]);
    }

    public function manager_activate(Request $request, $id)
    {
        $finder = Crypt::decrypt($id);

        $manager = Manager::find($finder);

        $manager->update([
            'status' => true
        ]);

        return back()->with([
            'alertType' => 'success',
            'message' => $manager->name. ' activated successfully!'
        ]);
    }

    public function manager_deactivate(Request $request, $id)
    {
        $finder = Crypt::decrypt($id);

        $manager = Manager::find($finder);

        $manager->update([
            'status' => false
        ]);

        return back()->with([
            'alertType' => 'success',
            'message' => $manager->name. ' deactivated successfully!'
        ]);
    }

    public function manager_delete($id)
    {
        $finder = Crypt::decrypt($id);

        Manager::find($finder)->delete();

        return back()->with([
            'alertType' => 'success',
            'message' => 'Manager deleted successfully!'
        ]);
    }

    public function accountants()
    {
        return view('admin.accountants.view');
    }

    public function accountant_add()
    {
        return view('admin.accountants.index');
    }

    public function accountant_post(Request $request)
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
                    'account_type' => 'Accountant',
                    'name' => $request->name,
                    'email' => $request->email,
                    'email_verified_at' => now(),
                    'password' => Hash::make($request->password),
                    'phone' => $request->phone,
                    'gender' => $request->gender,
                    'status' => false
                ]);
            } else {
                $user = User::create([
                    'account_type' => 'Accountant',
                    'name' => $request->name,
                    'email' => $request->email,
                    'email_verified_at' => now(),
                    'password' => Hash::make($request->password),
                    'phone' => $request->phone,
                    'gender' => $request->gender,
                    'status' => true
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
                'back' => route('admin.accountants'),
                'message' => $user->name. ' account created successfully!'
            ]);
        }
        
        if($request->status !== null && $request->status == 'false')
        {
            $user =  User::create([
                'account_type' => 'Accountant',
                'name' => $request->name,
                'email' => $request->email,
                'email_verified_at' => now(),
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'gender' => $request->gender,
                'status' => false
            ]);
        } else {
            $user = User::create([
                'account_type' => 'Accountant',
                'name' => $request->name,
                'email' => $request->email,
                'email_verified_at' => now(),
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'gender' => $request->gender,
                'status' => true
            ]);
        }

        return back()->with([
            'alertType' => 'success',
            'back' => route('admin.accountants'),
            'message' => $user->name. ' account created successfully!'
        ]);
    }

    public function accountant_edit($id)
    {
        $finder = Crypt::decrypt($id);

        $user = User::find($finder);

        return view ('admin.accountants.edit', [
            'user' => $user
        ]);
    }
    
    public function manager_assistances()
    {
        return view('admin.assistant-manager.view');
    }

    public function manager_assistance_add()
    {
        return view('admin.assistant-manager.index');
    }

    public function manager_assistance_post(Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed']
        ]);

        // dd($request->all());

        if($request->notify == 'on')
        {
            if($request->status !== null && $request->status == 'false')
            {
                $user = User::create([
                    'account_type' => 'Assistant Manager',
                    'name' => $request->name,
                    'email' => $request->email,
                    'email_verified_at' => now(),
                    'password' => Hash::make($request->password),
                    'phone' => $request->phone,
                    'gender' => $request->gender,
                    'status' => false
                ]);
            } else {
                $user = User::create([
                    'account_type' => 'Assistant Manager',
                    'name' => $request->name,
                    'email' => $request->email,
                    'email_verified_at' => now(),
                    'password' => Hash::make($request->password),
                    'phone' => $request->phone,
                    'gender' => $request->gender,
                    'status' => true
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
                'back' => route('admin.manager.assistances'),
                'message' => $user->name. ' account created successfully!'
            ]);
        }
        
        if($request->status !== null && $request->status == 'false')
        {
            $user =  User::create([
                'account_type' => 'Assistant Manager',
                'name' => $request->name,
                'email' => $request->email,
                'email_verified_at' => now(),
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'gender' => $request->gender,
                'status' => false
            ]);
        } else {
            $user = User::create([
                'account_type' => 'Assistant Manager',
                'name' => $request->name,
                'email' => $request->email,
                'email_verified_at' => now(),
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'gender' => $request->gender,
                'status' => true
            ]);
        }

        return back()->with([
            'alertType' => 'success',
            'back' => route('admin.manager.assistances'),
            'message' => $user->name. ' account created successfully!'
        ]);
    }

    public function assistance_manager_edit($id)
    {
        $finder = Crypt::decrypt($id);

        $user = User::find($finder);

        return view ('admin.assistant-manager.edit', [
            'user' => $user
        ]);
    }


    // General Settings for Account Type [Accountant and Assistant Manager]
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

        User::find($finder)->delete();

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

    // Rates
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
        $this->validate($request, [
            'grade' => ['required', 'numeric'],
            'price' => ['required', 'numeric'],
            'unit_price' => ['required', 'numeric'],
        ]);
        
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
            'grade' => ['required', 'numeric'],
            'price' => ['required', 'numeric'],
            'unit_price' => ['required', 'numeric'],
        ]);

        $finder = Crypt::decrypt($id);

        $beratingcalculation = BeratingCalculation::find($finder);
        
        $beratingcalculation->update([
            'grade' => $request->grade,
            'price' => $request->price,
            'unit_price' => $request->unit_price
        ]);

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

        BeratingCalculation::find($finder)->delete();

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
            'percentage_min' => ['required', 'string', 'max:255'],
            'percentage_max' => ['required', 'string', 'max:255'],
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
            'percentage_min' => ['required', 'string', 'max:255'],
            'percentage_max' => ['required', 'string', 'max:255'],
            'dollar' => ['required', 'numeric'],
            'exchange' => ['required', 'numeric'],
        ]);

        $finder = Crypt::decrypt($id);

        $analysiscalculation = AnalysisCalculation::find($finder);
        
        $analysiscalculation->update([
            'percentage_min' => $request->percentage_min,
            'percentage_max' => $request->percentage_max,
            'dollar_rate' => $request->dollar,
            'exchange_rate' => $request->exchange
        ]);

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

    // Payment Voucher
    public function payment_voucher_tin_view()
    {
        $tinPaymentVoucher = TinPaymentAnalysis::latest()->get();

        return view('admin.payment-voucher.view_tin', [
            'tinPaymentVoucher' => $tinPaymentVoucher
        ]);
    }

    public function payment_voucher_tin_edit($id)
    {
        $finder = Crypt::decrypt($id);

        $tinPayment = TinPaymentAnalysis::find($finder);

        return view('admin.payment-voucher.edit_tin', [
            'tinPayment' => $tinPayment
        ]);

    }

    public function payment_voucher_columbite_view()
    {
        return view('admin.payment-voucher.view_columbite');
    }

    // Expenses
    public function expenses()
    {
        $expenses = Expenses::latest()->get();

        return view('admin.expenses', [
            'expenses' => $expenses
        ]);
    }

    public function update_expense($id, Request $request)
    {
        $this->validate($request, [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric'],
            'date' => ['required', 'date'],
        ]);

        $finder = Crypt::decrypt($id);

        $expense = Expenses::find($finder);

        $transaction = Transaction::where('accountant_process_id', $expense->id)->first();

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
                    'title' => $request->title,
                    'description' => $request->description,
                    'amount' => $request->amount,
                    'date' => $request->date,
                    'receipt' => '/storage/expenses_receipts/'.$filename
                ]);
            } else {
                $expense->update([
                    'title' => $request->title,
                    'description' => $request->description,
                    'amount' => $request->amount,
                    'date' => $request->date
                ]);
            }

            Notification::create([
                'to' => $expense->user_id,
                'admin_id' => Auth::user()->id,
                'title' => config('app.name'),
                'body' => 'Admin has update an expense added by you with title - '.$expense->title
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
                    'title' => $request->title,
                    'description' => $request->description,
                    'amount' => $request->amount,
                    'date' => $request->date,
                    'receipt' => '/storage/expenses_receipts/'.$filename
                ]);
            } else {
                $expense->update([
                    'title' => $request->title,
                    'description' => $request->description,
                    'amount' => $request->amount,
                    'date' => $request->date
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
                'title' => $request->title,
                'description' => $request->description,
                'amount' => $request->amount,
                'date' => $request->date,
                'receipt' => '/storage/expenses_receipts/'.$filename
            ]);
        } else {
            $expense->update([
                'title' => $request->title,
                'description' => $request->description,
                'amount' => $request->amount,
                'date' => $request->date,
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

    public function post_material_columbite(Request $request)
    {
        $this->validate($request, [
            'grade' => ['required', 'numeric'],
            'material' => ['required', 'numeric'],
        ]);
        
       ColumbiteMaterial::create([
            'grade' => $request->grade,
            'material' => $request->material,
        ]);

        return back()->with([
            'alertType' => 'success',
            'back' => route('admin.material.columbite'),
            'message' => 'Added successfully!'
        ]);
    }

    public function material_columbite_update($id, Request $request)
    {
        $this->validate($request, [
            'grade' => ['required', 'numeric'],
            'material' => ['required', 'numeric'],
        ]);

        $finder = Crypt::decrypt($id);

        $columbiteMaterial = ColumbiteMaterial::find($finder);
        
        $columbiteMaterial->update([
            'grade' => $request->grade,
            'material' => $request->material,
        ]);

        return back()->with([
            'alertType' => 'success',
            'message' => 'Updated successfully!'
        ]);
    }

    public function material_columbite_delete($id)
    {
        $finder = Crypt::decrypt($id);

        ColumbiteMaterial::find($finder)->delete();

        return back()->with([
            'alertType' => 'success',
            'message' => 'Deleted successfully!'
        ]);
    }

    // Weekly Analysis Tin (Pounds)
    public function weekly_analysis_tin_pound(Request $request)
    {
        if($request->start_date == null && $request->end_date == null)
        {
            $tinPayment = TinPaymentAnalysis::latest()->where('type', 'pound')->get();
        } else {
            $tinPayment = TinPaymentAnalysis::latest()->where('type', 'pound')->whereBetween('date', [$request->start_date, $request->end_date])->get();
        }

        if($tinPayment->isEmpty())
        {
            $analysis = [];

        } else {
            
            $beratingCalculation = BeratingCalculation::latest()->get();

            foreach($tinPayment as $tinpound)
            {
                $beratingpayment = BeratingCalculation::find($tinpound->berating);

                foreach($beratingCalculation as $berating)
                {
                    if($berating->grade == $beratingpayment->grade)
                    {
                        $data[] = ['date' => $tinpound->date, 'berating' => $berating->grade, 'total' => $tinpound->total_in_pounds];

                        $analysis = array_values(array_unique($data, 0));
                                    
                        rsort($analysis);
                    }
                }
            }
        }

        // Calculation Starts
        if($request->start_date == null && $request->end_date == null)
        {
            $result =  TinPaymentAnalysis::join('berating_calculations', 'berating_calculations.id', '=', 'tin_payment_analyses.berating')->latest()->where('tin_payment_analyses.type', 'pound')  
                                ->get(['tin_payment_analyses.date', 'tin_payment_analyses.total_in_pounds', 'berating_calculations.grade', 'tin_payment_analyses.created_at', 'tin_payment_analyses.updated_at']);
        } else {
            $result =  TinPaymentAnalysis::join('berating_calculations', 'berating_calculations.id', '=', 'tin_payment_analyses.berating')->latest()->where('tin_payment_analyses.type', 'pound')  
                                ->whereBetween('tin_payment_analyses.date', [$request->start_date, $request->end_date])
                                ->get(['tin_payment_analyses.date', 'tin_payment_analyses.total_in_pounds', 'berating_calculations.grade', 'tin_payment_analyses.created_at', 'tin_payment_analyses.updated_at']);
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
            $sum188 = $result->where('grade', '18.8')->sum('total_in_pounds');
            $sum189 = $result->where('grade', '18.9')->sum('total_in_pounds');
            $sum190 = $result->where('grade', '19.0')->sum('total_in_pounds');
            $sum191 = $result->where('grade', '19.1')->sum('total_in_pounds');
            $sum192 = $result->where('grade', '19.2')->sum('total_in_pounds');
            $sum193 = $result->where('grade', '19.3')->sum('total_in_pounds');
            $sum194 = $result->where('grade', '19.4')->sum('total_in_pounds');
            $sum195 = $result->where('grade', '19.5')->sum('total_in_pounds');
            $sum196 = $result->where('grade', '19.6')->sum('total_in_pounds');
            $sum197 = $result->where('grade', '19.7')->sum('total_in_pounds');
            $sum198 = $result->where('grade', '19.8')->sum('total_in_pounds');
            $sum199 = $result->where('grade', '19.9')->sum('total_in_pounds');
            $sum200 = $result->where('grade', '20.0')->sum('total_in_pounds');
            $sum201 = $result->where('grade', '20.1')->sum('total_in_pounds');
            $sum202 = $result->where('grade', '20.2')->sum('total_in_pounds');
            $sum203 = $result->where('grade', '20.3')->sum('total_in_pounds');
            $sum204 = $result->where('grade', '20.4')->sum('total_in_pounds');
            $sum205 = $result->where('grade', '20.5')->sum('total_in_pounds');

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
            'data' => $data
        ]);
    }

    public function weekly_analysis_tin_kg(Request $request)
    {
        if($request->start_date == null && $request->end_date == null)
        {
            $tinPayment = TinPaymentAnalysis::latest()->where('type', 'kg')->get();
        } else {
            $tinPayment = TinPaymentAnalysis::latest()->where('type', 'kg')->whereBetween('date', [$request->start_date, $request->end_date])->get();
        }

        if($tinPayment->isEmpty())
        {
            $analysis = [];

        } else {
            
            $beratingCalculation = BeratingCalculation::latest()->get();

            foreach($tinPayment as $tinpound)
            {
                $beratingpayment = BeratingCalculation::find($tinpound->berating);

                foreach($beratingCalculation as $berating)
                {
                    if($berating->grade == $beratingpayment->grade)
                    {
                        $data[] = ['date' => $tinpound->date, 'berating' => $berating->grade, 'total' => $tinpound->total_in_kg];

                        $analysis = array_values(array_unique($data, 0));
                                    
                        rsort($analysis);
                    }
                }
            }
        }

        // Calculation Starts
        if($request->start_date == null && $request->end_date == null)
        {
            $result =  TinPaymentAnalysis::join('berating_calculations', 'berating_calculations.id', '=', 'tin_payment_analyses.berating')->latest()->where('tin_payment_analyses.type', 'kg')  
                                ->get(['tin_payment_analyses.date', 'tin_payment_analyses.total_in_kg', 'tin_payment_analyses.percentage_analysis', 'berating_calculations.grade', 'tin_payment_analyses.created_at', 'tin_payment_analyses.updated_at']);
        } else {
            $result =  TinPaymentAnalysis::join('berating_calculations', 'berating_calculations.id', '=', 'tin_payment_analyses.berating')->latest()->where('tin_payment_analyses.type', 'kg')  
                                ->whereBetween('tin_payment_analyses.date', [$request->start_date, $request->end_date])
                                ->get(['tin_payment_analyses.date', 'tin_payment_analyses.total_in_kg', 'tin_payment_analyses.percentage_analysis', 'berating_calculations.grade', 'tin_payment_analyses.created_at', 'tin_payment_analyses.updated_at']);
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
            $percentage188 = $result->where('grade', '18.8')->sum('percentage_analysis');
            $percentage189 = $result->where('grade', '18.9')->sum('percentage_analysis');
            $percentage190 = $result->where('grade', '19.0')->sum('percentage_analysis');
            $percentage191 = $result->where('grade', '19.1')->sum('percentage_analysis');
            $percentage192 = $result->where('grade', '19.2')->sum('percentage_analysis');
            $percentage193 = $result->where('grade', '19.3')->sum('percentage_analysis');
            $percentage194 = $result->where('grade', '19.4')->sum('percentage_analysis');
            $percentage195 = $result->where('grade', '19.5')->sum('percentage_analysis');
            $percentage196 = $result->where('grade', '19.6')->sum('percentage_analysis');
            $percentage197 = $result->where('grade', '19.7')->sum('percentage_analysis');
            $percentage198 = $result->where('grade', '19.8')->sum('percentage_analysis');
            $percentage199 = $result->where('grade', '19.9')->sum('percentage_analysis');
            $percentage200 = $result->where('grade', '20.0')->sum('percentage_analysis');
            $percentage201 = $result->where('grade', '20.1')->sum('percentage_analysis');
            $percentage202 = $result->where('grade', '20.2')->sum('percentage_analysis');
            $percentage203 = $result->where('grade', '20.3')->sum('percentage_analysis');
            $percentage204 = $result->where('grade', '20.4')->sum('percentage_analysis');
            $percentage205 = $result->where('grade', '20.5')->sum('percentage_analysis');

            $p188 = $sum188 * $percentage188; $p189 = $sum189 * $percentage189; $p190 = $sum190 * $percentage190; $p191 = $sum191 * $percentage191; $p192 = $sum192 * $percentage192; $p193 = $sum193 * $percentage193; $p194 = $sum194 * $percentage194; $p195 = $sum195 * $percentage195;  
            $p196 = $sum196 * $percentage196; $p197 = $sum197 * $percentage197;  $p198 = $sum198 * $percentage198; $p199 = $sum199 * $percentage199; $p200 = $sum200 * $percentage200; $p201 = $sum201 * $percentage201; $p202 = $sum202 * $percentage202; $p203 = $sum203 * $percentage203; 
            $p204 = $sum204 * $percentage204; $p205 = $sum205 * $percentage205;

            $totalPercentage =  $p188 + $p189 + $p190 + $p191 + $p192 + $p193 + $p194 + $p195 + $p196 + $p197 + $p198 + $p199 + $p200 + $p201 +  $p202 + $p203 + $p204 + $p205;

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
            'data' => $data
        ]);
    }

    public function weekly_analysis_columbite_pound(Request $request)
    {
        if($request->start_date == null && $request->end_date == null)
        {
            $tinPayment = ColumbitePaymentAnalysis::latest()->where('type', 'pound')->get();
        } else {
            $tinPayment = ColumbitePaymentAnalysis::latest()->where('type', 'pound')->whereBetween('date', [$request->start_date, $request->end_date])->get();
        }

        if($tinPayment->isEmpty())
        {
            $analysis = [];

        } else {
            
            $beratingCalculation = BeratingCalculation::latest()->get();

            foreach($tinPayment as $tinpound)
            {
                $beratingpayment = BeratingCalculation::find($tinpound->berating);

                foreach($beratingCalculation as $berating)
                {
                    if($berating->grade == $beratingpayment->grade)
                    {
                        $data[] = ['date' => $tinpound->date, 'berating' => $berating->grade, 'total' => $tinpound->total_in_pounds];

                        $analysis = array_values(array_unique($data, 0));
                                    
                        rsort($analysis);
                    }
                }
            }
        }

        // Calculation Starts
        if($request->start_date == null && $request->end_date == null)
        {
            $result =  ColumbitePaymentAnalysis::join('berating_calculations', 'berating_calculations.id', '=', 'columbite_payment_analyses.berating')->latest()->where('columbite_payment_analyses.type', 'pound')  
                                ->get(['columbite_payment_analyses.date', 'columbite_payment_analyses.total_in_pounds', 'columbite_payment_analyses.percentage_analysis', 'berating_calculations.grade', 'columbite_payment_analyses.created_at', 'columbite_payment_analyses.updated_at']);
        } else {
            $result =  ColumbitePaymentAnalysis::join('berating_calculations', 'berating_calculations.id', '=', 'columbite_payment_analyses.berating')->latest()->where('columbite_payment_analyses.type', 'pound')  
                                ->whereBetween('columbite_payment_analyses.date', [$request->start_date, $request->end_date])
                                ->get(['columbite_payment_analyses.date', 'columbite_payment_analyses.total_in_pounds', 'columbite_payment_analyses.percentage_analysis', 'berating_calculations.grade', 'columbite_payment_analyses.created_at', 'columbite_payment_analyses.updated_at']);
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
            $sum185 = $result->where('grade', '18.5')->sum('total_in_pounds');
            $sum186 = $result->where('grade', '18.6')->sum('total_in_pounds');
            $sum187 = $result->where('grade', '18.7')->sum('total_in_pounds');
            $sum188 = $result->where('grade', '18.8')->sum('total_in_pounds');
            $sum189 = $result->where('grade', '18.9')->sum('total_in_pounds');
            $sum190 = $result->where('grade', '19.0')->sum('total_in_pounds');
            $sum191 = $result->where('grade', '19.1')->sum('total_in_pounds');
            $sum192 = $result->where('grade', '19.2')->sum('total_in_pounds');
            $sum193 = $result->where('grade', '19.3')->sum('total_in_pounds');
            $sum194 = $result->where('grade', '19.4')->sum('total_in_pounds');
            $sum195 = $result->where('grade', '19.5')->sum('total_in_pounds');
            $sum196 = $result->where('grade', '19.6')->sum('total_in_pounds');
            $sum197 = $result->where('grade', '19.7')->sum('total_in_pounds');
            $sum198 = $result->where('grade', '19.8')->sum('total_in_pounds');
            $sum199 = $result->where('grade', '19.9')->sum('total_in_pounds');
            $sum200 = $result->where('grade', '20.0')->sum('total_in_pounds');
            $sum201 = $result->where('grade', '20.1')->sum('total_in_pounds');
            $sum202 = $result->where('grade', '20.2')->sum('total_in_pounds');
            $sum203 = $result->where('grade', '20.3')->sum('total_in_pounds');
            $sum204 = $result->where('grade', '20.4')->sum('total_in_pounds');
            $sum205 = $result->where('grade', '20.5')->sum('total_in_pounds');
            $sum206 = $result->where('grade', '20.6')->sum('total_in_pounds');
            $sum207 = $result->where('grade', '20.7')->sum('total_in_pounds');
            $sum208 = $result->where('grade', '20.8')->sum('total_in_pounds');
            $sum209 = $result->where('grade', '20.9')->sum('total_in_pounds');

            $totalPound = $sum185 + $sum186 + $sum187 + $sum188 + $sum189 + $sum190 + $sum191 + $sum192 + $sum193 + $sum194 + $sum195 + $sum196 + $sum197 + $sum198 + $sum199 + $sum200 + $sum201 + $sum202 + $sum203 + $sum204 + $sum205 + $sum206 + $sum207 + $sum208 + $sum209;

            $b185 = $sum185 * 18.5; $b186 = $sum186 * 18.6; $b187 = $sum187 * 18.7; $b188 = $sum188 * 18.8; $b189 = $sum189 * 18.9; $b190 = $sum190 * 19.0; $b191 = $sum191 * 19.1; $b192 = $sum192 * 19.2; $b193 = $sum193 * 19.3; $b194 = $sum194 * 19.4; $b195 = $sum195 * 19.5;  
            $b196 = $sum196 * 19.6; $b197 = $sum197 * 19.7;  $b198 = $sum198 * 19.8; $b199 = $sum199 * 19.9; $b200 = $sum200 * 20.0; $b201 = $sum201 * 20.1; $b202 = $sum202 * 20.2; $b203 = $sum203 * 20.3; 
            $b204 = $sum204 * 20.4; $b205 = $sum205 * 20.5; $b206 = $sum206 * 20.6; $b207 = $sum207 * 20.7; $b208 = $sum208 * 20.8; $b209 = $sum209 * 20.9;

            $totalBerating = $b185 + $b186 + $b187 + $b188 + $b189 + $b190 + $b191 + $b192 + $b193 + $b194 + $b195 + $b196 + $b197 + $b198 + $b199 + $b200 + $b201 +  $b202 + $b203 + $b204 + $b205 + $b206 + $b207 + $b208 + $b209;

            $beratingAverage = $totalBerating / $totalPound; 

            $totalBeratingAverage = number_format((float)$beratingAverage, 2, '.', '');

            // Percentage Average
            $percentage185 = $result->where('grade', '18.5')->sum('percentage_analysis');
            $percentage186 = $result->where('grade', '18.6')->sum('percentage_analysis');
            $percentage187 = $result->where('grade', '18.7')->sum('percentage_analysis');
            $percentage188 = $result->where('grade', '18.8')->sum('percentage_analysis');
            $percentage189 = $result->where('grade', '18.9')->sum('percentage_analysis');
            $percentage190 = $result->where('grade', '19.0')->sum('percentage_analysis');
            $percentage191 = $result->where('grade', '19.1')->sum('percentage_analysis');
            $percentage192 = $result->where('grade', '19.2')->sum('percentage_analysis');
            $percentage193 = $result->where('grade', '19.3')->sum('percentage_analysis');
            $percentage194 = $result->where('grade', '19.4')->sum('percentage_analysis');
            $percentage195 = $result->where('grade', '19.5')->sum('percentage_analysis');
            $percentage196 = $result->where('grade', '19.6')->sum('percentage_analysis');
            $percentage197 = $result->where('grade', '19.7')->sum('percentage_analysis');
            $percentage198 = $result->where('grade', '19.8')->sum('percentage_analysis');
            $percentage199 = $result->where('grade', '19.9')->sum('percentage_analysis');
            $percentage200 = $result->where('grade', '20.0')->sum('percentage_analysis');
            $percentage201 = $result->where('grade', '20.1')->sum('percentage_analysis');
            $percentage202 = $result->where('grade', '20.2')->sum('percentage_analysis');
            $percentage203 = $result->where('grade', '20.3')->sum('percentage_analysis');
            $percentage204 = $result->where('grade', '20.4')->sum('percentage_analysis');
            $percentage205 = $result->where('grade', '20.5')->sum('percentage_analysis');
            $percentage206 = $result->where('grade', '20.6')->sum('percentage_analysis');
            $percentage207 = $result->where('grade', '20.7')->sum('percentage_analysis');
            $percentage208 = $result->where('grade', '20.8')->sum('percentage_analysis');
            $percentage209 = $result->where('grade', '20.9')->sum('percentage_analysis');

            $p185 = $sum185 * $percentage185; $p186 = $sum186 * $percentage186; $p187 = $sum187 * $percentage187; $p188 = $sum188 * $percentage188; $p189 = $sum189 * $percentage189; $p190 = $sum190 * $percentage190; $p191 = $sum191 * $percentage191; $p192 = $sum192 * $percentage192; $p193 = $sum193 * $percentage193; $p194 = $sum194 * $percentage194; $p195 = $sum195 * $percentage195;  
            $p196 = $sum196 * $percentage196; $p197 = $sum197 * $percentage197;  $p198 = $sum198 * $percentage198; $p199 = $sum199 * $percentage199; $p200 = $sum200 * $percentage200; $p201 = $sum201 * $percentage201; $p202 = $sum202 * $percentage202; $p203 = $sum203 * $percentage203; 
            $p204 = $sum204 * $percentage204; $p205 = $sum205 * $percentage205; $p206 = $sum206 * $percentage206; $p207 = $sum207 * $percentage207; $p208 = $sum208 * $percentage208; $p209 = $sum209 * $percentage209;

            $totalPercentage =  $p185 + $p186 + $p187 + $p188 + $p189 + $p190 + $p191 + $p192 + $p193 + $p194 + $p195 + $p196 + $p197 + $p198 + $p199 + $p200 + $p201 +  $p202 + $p203 + $p204 + $p205 + $p206 + $p207 + $p208 + $p209;

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
            'data' => $data
        ]);
    }
}
