<?php

namespace App\Http\Controllers;

use App\Models\ResetCodePassword;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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
        if (auth()->attempt($input, $request->get('remember'))) {
            if ($user->status == '0' || $user->access == '0') {

                Auth::logout();

                return back()->with([
                    'type' => 'danger',
                    'message' => 'Account disactivated, please contact administrator.'
                ]);
            }

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

    public function forgot()
    {
        return view('auth.forgot');
    }

    public function forget_password(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|exists:users',
        ]);

        $user = User::where('email', $request->email)->first();

        // Delete all old code that user send before.
        ResetCodePassword::where('email', $request->email)->delete();

        // Generate random code
        $code = mt_rand(100000, 999999);

        // Create a new code
        $codeData = ResetCodePassword::create([
            'email' => $request->email,
            'code' => $code
        ]);

         /** Store information to include in mail in $data as an array */
         $data = array(
            'name' => $user->name,
            'email' => $user->email,
            'code' => $codeData->code
        );

        /** Send message to the user */
        Mail::send('emails.resetPassword', $data, function ($m) use ($data) {
            $m->to($data['email'])->subject(config('app.name'));
        });

        return redirect()->route('reset.password', Crypt::encrypt($user->email))->with([
            'type' => 'success',
            'message' => 'We have emailed your password reset code!'
        ]);
    }

    public function password_reset_email($email)
    {
        $email = Crypt::decrypt($email);

        $user = User::firstWhere('email', $email);

        return view('auth.reset_password', [
            'user' => $user
        ]);
    }

    public function reset_password($id, Request $request)
    {
        $request->validate([
            'code' => 'required|string|exists:reset_code_passwords',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $finder = Crypt::decrypt($id);

        if (ResetCodePassword::where('code', '=', $request->code)->exists()) {
            // find the code
            $passwordReset = ResetCodePassword::firstWhere('code', $request->code);

            // check if it does not expired: the time is one hour
            if ($passwordReset->created_at > now()->addHour()) {
                $passwordReset->delete();

                return back()->with([
                    'type' => 'danger',
                    'message' => 'Password reset code expired'
                ]);
            }

            // find user's email
            $user = User::findorfail($finder);

            // update user password
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            // delete current code
            $passwordReset->delete();

            return redirect()->route('index')->with([
                'type' => 'success',
                'message' => 'Password has been successfully reset, Please login'
            ]);
        } else {
            return back()->with([
                'type' => 'danger',
                'message' => "Code doesn't exist in our database"
            ]);
        }
    }

    public function user_login(Request $request)
    {
        $this->validate($request, [
            'email' => ['required', 'string', 'max:255'],
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
                'message' => "Email/Username doesn't exist"
            ]);
        }

        // authentication attempt
        if (auth()->attempt($input, $request->get('remember'))) 
        {
            if ($user->status == '0' || $user->access == '0') {

                Auth::logout();

                return back()->with([
                    'type' => 'danger',
                    'message' => 'Account disactivated, please contact administrator.'
                ]);
            }

            if ($user->access == '0') {

                Auth::logout();

                return back()->with([
                    'type' => 'danger',
                    'message' => 'No access given to you to login.'
                ]);
            }

            if ($user->account_type == 'Accountant') {
                return redirect()->route('dashboard', $user->username);
            }

            if ($user->account_type == 'Assistant Manager' || $user->account_type == 'Store Personnel') {
                return redirect()->route('dashboard', $user->username);
            }

            Auth::logout();

            return back()->with([
                'type' => 'danger',
                'message' => 'You are not a User.'
            ]);
        } else {
            return back()->with([
                'type' => 'danger',
                'message' => 'User authentication failed.'
            ]);
        }
    }
}
