<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class SettingController extends Controller
{
    public function settings() {
        return view('Home.settings');
    }

    public function settingsusername(Request $request) {
        $successMessage = str_replace(':flag', 'Username', Config::get('messages.success.updated'));
        $errorMessage = Config::get('messages.error.validation');

        $user = Auth::user();

        $request->validate([
            'username' => 'required|string|max:50|unique:users,username,' . $user->id,
            'password' => 'required|string|min:8|max:50',
        ]);

        if (!password_verify($request->password, $user->password)) {
            return back()->withErrors([
                'password' => str_replace(':info', 'Invalid Credentials', $errorMessage)
            ]);
        }

        try {
            $user->update(['username' => $request->username]);

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('msgSuccess', $successMessage);
        } catch (Exception $e) {
            return back()->withErrors([
                'username' => str_replace(':info', 'Error Code 201', $errorMessage)
            ]);
        }
    }

    public function settingsname(Request $request) {
        $successMessage = str_replace(':flag', 'Name', Config::get('messages.success.updated'));
        $errorMessage = Config::get('messages.error.validation');

        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:100',
            'password' => 'required|string|min:8|max:50',
        ]);

        if (!password_verify($request->password, $user->password)) {
            return back()->withErrors([
                'password' => str_replace(':info', 'Invalid Credentials', $errorMessage)
            ]);
        }

        try {
            $user->update(['name' => $request->name]);

            return redirect()->route('settings')->with('msgSuccess', $successMessage);
        } catch (Exception $e) {
            return back()->withErrors([
                'name' => str_replace(':info', 'Error Code 201', $errorMessage)
            ]);
        }
    }

    public function settingspassword(Request $request){
        $successMessage = str_replace(':flag', 'Password', Config::get('messages.success.updated'));
        $errorMessage = Config::get('messages.error.validation');

        $user = Auth::user();

        $request->validate([
            'currentpassword' => 'required|string|min:8|max:50',
            'password' => 'required|string|min:8|max:50|confirmed',
        ]);

        if (!password_verify($request->currentpassword, $user->password)) {
            return back()->withErrors([
                'currentpassword' => str_replace(':info', 'Invalid current password', $errorMessage)
            ]);
        }

        try {
            $user->update(['password' => Hash::make($request->password)]);

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('msgSuccess', $successMessage);
        } catch (Exception $e) {
            return back()->withErrors([
                'password' => str_replace(':info', 'Error Code 201', $errorMessage)
            ]);
        }
    }
}
