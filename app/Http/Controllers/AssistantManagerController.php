<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AssistantManagerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function payment_analysis_tin_view()
    {
        return view('assistant_managers.payment_analysis_tin_view');
    }

    public function payment_analysis_tin_add()
    {
        return view('assistant_managers.payment_analysis_tin_add');
    }

    public function payment_analysis_tin_post(Request $request)
    {
        $this->validate($request, [
            'customer' => ['required', 'string', 'max:255'],
            'berating' => ['string', 'numeric', 'max:10',],
            'manager' => ['required', 'numeric'],
            'date' => ['required', 'date'],
            'receipt' => 'required|mimes:jpeg,png,jpg'
        ]);

        if($request->weight == 'bag')
        {

        } 

        if($request->weight == 'pound')
        {

        } 

        if ($request->status !== null && $request->status == 'false') {
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

        return back()->with([
            'type' => 'danger',
            'message' => 'Please select weight type'
        ]);
    }

    public function payment_analysis_columbite_view()
    {
        return view('assistant_managers.payment_analysis_columbite_view');
    }

    public function payment_analysis_columbite_add()
    {
        return view('assistant_managers.payment_analysis_columbite_add');
    }
}
