<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Models\Key;
use App\Models\App;
use App\Models\User;
use App\Models\UserHistory;
use Illuminate\Validation\Rule;

class DashController extends Controller
{
    public function Dashboard() {
        if (auth()->user()->permissions == "Owner") {
            $keys = Key::orderBy('created_at', 'desc')->paginate(10, ['*'], 'keys_page');
        } else {
            $keys = Key::where('created_by', auth()->user()->username)->orderBy('created_at', 'desc')->paginate(10, ['*'], 'keys_page');
        }
        $apps = App::orderBy('created_at', 'desc')->paginate(10, ['*'], 'apps_page');
        $currency = Config::get('messages.settings.currency');

        return view('Home.dashboard', compact('keys', 'apps', 'currency'));
    }

    public function ManageUsers() {
        $errorMessage = Config::get('messages.error.validation');
        $users = User::orderBy('created_at', 'desc')->paginate(10);

        if (auth()->user()->permissions == "Owner") {
            return view('Home.manage_users', compact('users'));
        }

        return back()->withErrors(['name' => str_replace(':info', 'Error Code 201, Access Forbidden', $errorMessage),])->onlyInput('name');
    }

    public function ManageUsersGenerateView() {
        $errorMessage = Config::get('messages.error.validation');

        if (auth()->user()->permissions == "Owner") {
            return view('Home.generate_user');
        }

        return back()->withErrors(['name' => str_replace(':info', 'Error Code 201, Access Forbidden', $errorMessage),])->onlyInput('name');
    }

    public function ManageUsersGeneratePost(Request $request) {
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
                'created_by'  => auth()->user()->username,
            ]);

            return redirect()->route('admin.users.generate')->with('msgSuccess', str_replace(':flag', "User " . $username, $successMessage));
        } catch (\Exception $e) {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 202', $errorMessage),])->onlyInput('name');
        }
    }

    public function ManageUsersEditView($id) {
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

    public function ManageUsersEditPost(Request $request) {
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

    public function ManageUsersDeletePost(Request $request) {
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
            $user->histories()->delete();
            $user->delete();

            return redirect()->route('admin.users')->with('msgSuccess', str_replace(':flag', "User " . $username, $successMessage));
        } catch (\Exception $e) {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 202', $errorMessage),])->onlyInput('name');
        }
    }
}
