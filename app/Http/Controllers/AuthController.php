<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function index(Request $request)
    {
        return view('auth.index');
    }

    public function admin_login(Request $request)
    {
        return view('auth.admin_login');
    }

    public function post_admin_login(Request $request)
    {
        $this->validate($request, [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $input = $request->only(['email', 'password']);

        $user = User::query()->where('email', $request->email)->first();

        if ($user && !Hash::check($request->password, $user->password)) {
            return back()->with([
                'type' => 'danger',
                'message' => 'Incorrect Password!'
            ]);
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->with([
                'type' => 'danger',
                'message' => "Email doesn't exist"
            ]);
        }

        // authentication attempt
        if (auth()->attempt($input)) {
            if ($user->account_type == 'Administrator') {
                return redirect()->route('admin.dashboard');
            }
            return back()->with([
                'type' => 'danger',
                'message' => "You are not an Administrator!"
            ]);
        } else {
            return back()->with([
                'type' => 'danger',
                'message' => "User authentication failed."
            ]);
        }
    }

    public function logout()
    {
        $account_type = Auth::user()->account_type;
        
        Session::flush();

        Auth::logout();

        if($account_type == 'Administrator')
        {
            return redirect('/admin/login');
        }
        
        return redirect('/');
    }
}
