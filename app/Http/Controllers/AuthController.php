<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function LoginView() {
        return view('Auth.login');
    }
    
    public function LoginPost(Request $request) {
        $successMessage = Config::get('messages.success.logged_in');
        $errorMessage = Config::get('messages.error.wrong_creds');

        $request->validate([
            'username' => 'required|string|max:50',
            'password' => 'required|string|max:50|min:6',
            'stay_log' => 'in:1,0',
        ]);

        $credentials = $request->only('username', 'password');

        $remember = $request->has('stay_log');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            Session::put('login_time', now());
            return redirect()->intended('dashboard')->with('msgSuccess', $successMessage);
        }

        return back()->withErrors(['username' => $errorMessage,])->onlyInput('username');
    }
}
