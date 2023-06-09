<?php

namespace App\Http\Controllers;

use App\Models\Balance;
use App\Models\BeratingCalculation;
use App\Models\Expenses;
use App\Models\Notification;
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

        $monthly_expenses = Expenses::whereDate('date', $today)->sum('amount');
        $monthly_expenses_count = Expenses::whereDate('date', $today)->get()->count();

        $notifications = Notification::latest()->where('to', Auth::user()->id)->get()->take(5);

        $totalBalance = Balance::whereDate('date', $today)->first()->starting_balance ?? 0;
        $totalAdditionalIncome = Balance::whereDate('date', $today)->first()->additional_income ?? 0;
        $totalRemainingBalance = Balance::whereDate('date', '!=', $today)->sum('remaining_balance') ?? 0;

        $totalStartingBalance = $totalBalance + $totalAdditionalIncome + $totalRemainingBalance;

        return view('dashboard.dashboard', [
            'moment' => $moment,
            'notifications' => $notifications,
            'monthly_expenses' => $monthly_expenses,
            'monthly_expenses_count' => $monthly_expenses_count,
            'totalStartingBalance' => $totalStartingBalance
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
}
