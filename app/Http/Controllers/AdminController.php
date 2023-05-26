<?php

namespace App\Http\Controllers;

use App\Models\AnalysisCalculation;
use App\Models\BeratingCalculation;
use App\Models\Manager;
use App\Models\Notification;
use App\Models\TinPaymentAnalysis;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

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

    // Fund Account
    public function account_funding_confirm($response, $amount)
    {
        $wallet = Wallet::first();

        $wallet->update([
            'amount' => $wallet->amount + $amount
        ]);

        Transaction::create([
            'user_id' => Auth::user()->id,
            'amount' => $amount,
            'reference' => $response,
            'status' => 'Top Up'
        ]);

        Notification::create([
            'to' => Auth::user()->id,
            'title' => config('app.name'),
            'body' => 'You have funded ₦' . $amount . ' to '.config('app.name').' wallet.'
        ]);

        return back()->with([
            'alertType' => 'success',
            'message' => 'Wallet funded successfully.'
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

    public function payment_voucher_columbite_view()
    {
        return view('admin.payment-voucher.view_columbite');
    }


}
