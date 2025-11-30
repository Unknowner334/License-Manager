<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Models\Key;
use App\Models\App;
use App\Models\User;
use App\Models\UserHistory;
use App\Models\Reff;
use Illuminate\Validation\Rule;

class DashController extends Controller
{
    static function UsersCreated($edit_id) {
        $reff = Reff::where('edit_id', $edit_id)->first();
        if (!$reff) return "N/A";

        return $reff->users->count();
    }

    public function dashboard() {
        if (auth()->user()->permissions == "Owner") {
            $keys = Key::orderBy('created_at', 'desc')->paginate(5, ['*'], 'keys_page');
        } else {
            $keys = Key::where('created_by', auth()->user()->user_id)->orderBy('created_at', 'desc')->paginate(10, ['*'], 'keys_page');
        }

        $apps = App::orderBy('created_at', 'desc')->paginate(5, ['*'], 'apps_page');
        $currency = Config::get('messages.settings.currency');
        $loginTime = session('login_time');
        $sessionLifetime = session('session_lifetime');
        $expiryTime = $loginTime ? $loginTime->copy()->addMinutes($sessionLifetime) : null;

        return view('Home.dashboard', compact('keys', 'apps', 'currency', 'expiryTime', 'loginTime', 'sessionLifetime'));
    }

    public function manageusers(Request $request) {
        $errorMessage = Config::get('messages.error.validation');
        if ($request->get('search')) {
            $users = User::where('username', $request->get('search'))->orderBy('created_at', 'desc')->paginate(10);
        } else {
            $users = User::orderBy('created_at', 'desc')->paginate(10);
        }

        if (auth()->user()->permissions == "Owner") {
            return view('Home.manage_users', compact('users'));
        }

        return back()->withErrors(['name' => str_replace(':info', 'Error Code 201, Access Forbidden', $errorMessage),])->onlyInput('name');
    }

    public function manageusersgenerate() {
        $errorMessage = Config::get('messages.error.validation');

        if (auth()->user()->permissions == "Owner") {
            return view('Home.generate_user');
        }

        return back()->withErrors(['name' => str_replace(':info', 'Error Code 201, Access Forbidden', $errorMessage),])->onlyInput('name');
    }

    public function manageusersgenerate_action(Request $request) {
        $successMessage = Config::get('messages.success.created');
        $errorMessage = Config::get('messages.error.validation');

        if (!auth()->user()->permissions == "Owner") {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 201, Access Forbidden', $errorMessage),])->onlyInput('name');
        }

        $request->validate([
            'name'     => 'required|string|min:4|max:100',
            'username' => 'required|string|min:4|max:50|unique:users,username',
            'password' => 'required|string|confirmed|min:8|max:50',
            'status'   => 'required|in:Active,Inactive',
            'perm'     => 'required|in:Owner,Admin',
        ]);

        $username = $request->input('username');

        try {
            User::create([
                'name'        => $request->input('name'),
                'username'    => $request->input('username'),
                'password'    => $request->input('password'),
                'status'      => $request->input('status'),
                'permissions' => $request->input('perm'),
                'created_by'  => auth()->user()->user_id,
            ]);

            return redirect()->route('admin.users.generate')->with('msgSuccess', str_replace(':flag', "User " . $username, $successMessage));
        } catch (\Exception $e) {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 202', $errorMessage),])->onlyInput('name');
        }
    }

    public function manageusersedit($id) {
        $errorMessage = Config::get('messages.error.validation');
        $user = User::where('user_id', $id)->first();

        if (empty($user)) {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 202', $errorMessage),])->onlyInput('name');
        }

        if (auth()->user()->permissions == "Owner") {
            return view('Home.edit_user', compact('user'));
        }

        return back()->withErrors(['name' => str_replace(':info', 'Error Code 201, Access Forbidden', $errorMessage),])->onlyInput('name');
    }

    public function manageusersedit_action(Request $request) {
        $successMessage = Config::get('messages.success.updated');
        $errorMessage = Config::get('messages.error.validation');

        if (!auth()->user()->permissions == "Owner") {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 201, Access Forbidden', $errorMessage),])->onlyInput('name');
        }

        $request->validate([
            'user_id'  => 'required|string|min:4|max:100|exists:users,user_id',
            'name'     => 'required|string|min:4|max:100',
            'status'   => 'required|in:Active,Inactive',
            'perm'     => 'required|in:Owner,Admin',
        ]);

        $username = $request->input('username');
        $user = User::where('user_id', $request->input('user_id'))->first();

        $request->validate([
            'username' => 'required|string|min:4|max:50',Rule::unique('users', 'username')->ignore($user->user_id, 'user_id'),
        ]);

        if (empty($user)) {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 203', $errorMessage),])->onlyInput('name');
        }

        try {
            if ($request->has('new_password')) {
                $request->validate([
                    'password' => 'required|string|confirmed|min:8|max:50',
                ]);

                $user->update([
                    'name'        => $request->input('name'),
                    'username'    => $request->input('username'),
                    'password'    => $request->input('password'),
                    'status'      => $request->input('status'),
                    'permissions' => $request->input('perm'),
                ]);
            } else {
                $user->update([
                    'name'        => $request->input('name'),
                    'username'    => $request->input('username'),
                    'status'      => $request->input('status'),
                    'permissions' => $request->input('perm'),
                ]);
            }

            return redirect()->route('admin.users.edit', $request->input('user_id'))->with('msgSuccess', str_replace(':flag', "User " . $username, $successMessage));
        } catch (\Exception $e) {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 202', $errorMessage),])->onlyInput('name');
        }
    }

    public function manageusersdelete(Request $request) {
        $successMessage = Config::get('messages.success.deleted');
        $errorMessage = Config::get('messages.error.validation');

        if (!auth()->user()->permissions == "Owner") {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 201, Access Forbidden', $errorMessage),])->onlyInput('name');
        }

        $request->validate([
            'user_id'  => 'required|string|min:4|max:100|exists:users,user_id',
        ]);

        $user = User::where('user_id', $request->input('user_id'))->first();

        if (empty($user)) {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 203', $errorMessage),])->onlyInput('name');
        }

        $username = $user->username;

        try {
            $user->delete();

            return redirect()->route('admin.users')->with('msgSuccess', str_replace(':flag', "User " . $username, $successMessage));
        } catch (\Exception $e) {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 202', $errorMessage),])->onlyInput('name');
        }
    }
    
    public function manageusershistroy() {
        $errorMessage = Config::get('messages.error.validation');
        $histories = UserHistory::orderBy('created_at', 'desc')->paginate(10);

        if (!auth()->user()->permissions == "Owner") {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 201, Access Forbidden', $errorMessage),])->onlyInput('name');
        }

        if (empty($histories)) {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 202', $errorMessage),])->onlyInput('name');
        }

        return view('Home.history_user', compact('histories'));
    }

    public function manageusershistoryuser($id) {
        $errorMessage = Config::get('messages.error.validation');
        $histories = UserHistory::where('user_id', $id)->orderBy('created_at', 'desc')->paginate(10);

        if (!auth()->user()->permissions == "Owner") {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 201, Access Forbidden', $errorMessage),])->onlyInput('name');
        }

        if (empty($histories)) {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 202', $errorMessage),])->onlyInput('name');
        }

        return view('Home.history_user', compact('histories'));
    }

    public function managereferrable() {
        $errorMessage = Config::get('messages.error.validation');
        $reffs = Reff::orderBy('created_at', 'desc')->paginate(10);

        if (auth()->user()->permissions == "Owner") {
            return view('Home.manage_reff', compact('reffs'));
        }

        return back()->withErrors(['name' => str_replace(':info', 'Error Code 201, Access Forbidden', $errorMessage),])->onlyInput('name');
    }

    public function managereferrablegenerate() {
        $errorMessage = Config::get('messages.error.validation');

        if (auth()->user()->permissions == "Owner") {
            return view('Home.generate_reff');
        }

        return back()->withErrors(['name' => str_replace(':info', 'Error Code 201, Access Forbidden', $errorMessage),])->onlyInput('name');
    }

    public function managereferrablegenerate_action(Request $request) {
        $successMessage = Config::get('messages.success.created');
        $errorMessage = Config::get('messages.error.validation');

        if (!auth()->user()->permissions == "Owner") {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 201, Access Forbidden', $errorMessage),])->onlyInput('name');
        }

        $request->validate([
            'status'   => 'required|in:Active,Inactive',
        ]);

        if ($request->input('code') == '') {
            do {
                $code = parent::randomString(16);
                $codeExists = Reff::where('code', $code)->exists();
            } while ($codeExists);
        } else {
            $code = $request->input('code');

            $request->validate([
                'code' => [
                    'required',
                    'string',
                    'min:4',
                    'max:50',
                    Rule::unique('referrable_codes', 'code')
                ],
            ]);
        }

        try {
            Reff::create([
                'code'        => $code,
                'status'      => $request->input('status'),
                'created_by'  => auth()->user()->user_id,
            ]);

            return redirect()->route('admin.referrable.generate')->with('msgSuccess', str_replace(':flag', "Reff " . $code, $successMessage));
        } catch (\Exception $e) {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 202', $errorMessage),])->onlyInput('name');
        }
    }

    public function managereferrableedit($id) {
        $errorMessage = Config::get('messages.error.validation');
        $reff = Reff::where('edit_id', $id)->first();

        if (empty($reff)) {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 202', $errorMessage),])->onlyInput('name');
        }

        if (auth()->user()->permissions == "Owner") {
            return view('Home.edit_reff', compact('reff'));
        }

        return back()->withErrors(['name' => str_replace(':info', 'Error Code 201, Access Forbidden', $errorMessage),])->onlyInput('name');
    }

    public function managereferrableedit_action(Request $request) {
        $successMessage = Config::get('messages.success.updated');
        $errorMessage = Config::get('messages.error.validation');

        if (!auth()->user()->permissions == "Owner") {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 201, Access Forbidden', $errorMessage),])->onlyInput('name');
        }

        $request->validate([
            'edit_id'  => 'required|string|min:4|max:36|exists:referrable_codes,edit_id',
            'status'   => 'required|in:Active,Inactive',
        ]);

        $reff = Reff::where('edit_id', $request->input('edit_id'))->first();

        if (empty($reff)) {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 203', $errorMessage),])->onlyInput('name');
        }

        if ($request->input('code') == '') {
            do {
                $code = parent::randomString(16);
                $codeExists = Reff::where('code', $code)->exists();
            } while ($codeExists);
        } else {
            $code = $request->input('code');

            $request->validate([
                'code' => [
                    'required',
                    'string',
                    'min:4',
                    'max:50',
                    Rule::unique('referrable_codes', 'code')->ignore($reff->edit_id, 'edit_id')
                ],
            ]);
        }

        try {
            $reff->update([
                'code'   => $code,
                'status' => $request->input('status'),
            ]);

            return redirect()->route('admin.referrable.edit', $request->input('edit_id'))->with('msgSuccess', str_replace(':flag', "Reff " . $code, $successMessage));
        } catch (\Exception $e) {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 202', $errorMessage),])->onlyInput('name');
        }
    }

    public function managereferrabledelete(Request $request) {
        $successMessage = Config::get('messages.success.deleted');
        $errorMessage = Config::get('messages.error.validation');

        if (!auth()->user()->permissions == "Owner") {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 201, Access Forbidden', $errorMessage),])->onlyInput('name');
        }

        $request->validate([
            'edit_id'  => 'required|string|min:4|max:36|exists:referrable_codes,edit_id',
        ]);

        $reff = Reff::where('edit_id', $request->input('edit_id'))->first();

        if (empty($reff)) {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 203', $errorMessage),])->onlyInput('name');
        }

        $code = $reff->code;

        try {
            $reff->delete();

            return redirect()->route('admin.referrable')->with('msgSuccess', str_replace(':flag', "Reff " . $code, $successMessage));
        } catch (\Exception $e) {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 202', $errorMessage),])->onlyInput('name');
        }
    }
}
