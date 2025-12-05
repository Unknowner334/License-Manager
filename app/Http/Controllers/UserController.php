<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Models\User;
use App\Models\UserHistory;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function manageusers(Request $request) {
        parent::require_ownership(1);

        return view('Home.manage_users');
    }

    public function manageusersdata() {
        parent::require_ownership(1);
        
        $users = User::get();

        $data = $users->map(function ($user) {
            $created = Controller::timeElapsed($user->created_at);
            $userStatus = Controller::statusColor($user->status);
            $saldo = Controller::saldoData($user->saldo, $user->role);
            $saldoS = $saldo[0];
            $saldoC = $saldo[1];
            $roleC = Controller::permissionColor($user->role);

            if ($user->referrable != NULL) {
                $reff_status = Controller::statusColor($user->referrable->status);
                $reff_code = Controller::censorText($user->referrable->code);
            } else {
                $reff_status = 'dark';
                $reff_code = "N/A";
            }

            return [
                'id'        => $user->id,
                'user_id'   => $user->user_id,
                'name'      => $user->name,
                'username'  => "<span class='align-middle badge fw-normal text-$userStatus fs-6 blur Blur px-3 copy-trigger' data-copy='$user->username'>$user->username</span>",
                'created'   => "<i class='align-middle badge fw-normal text-dark fs-6'>$created</i>",
                'saldo'     => "<span class='align-middle badge fw-normal text-$saldoC fs-6'>$saldoS</span>",
                'role'      => "<span class='align-middle badge fw-normal text-$roleC fs-6'>$user->role</span>",
                'registrar' => Controller::userUsername($user->registrar),
                'reff'      => "<span class='align-middle badge fw-normal text-$reff_status fs-6'>$reff_code</span>",
            ];
        });

        return response()->json([
            'status' => 0,
            'data'   => $data
        ]);
    }

    public function manageusersgenerate() {
        parent::require_ownership(1);

        return view('Home.generate_user');
    }

    public function manageusersgenerate_action(Request $request) {
        $successMessage = Config::get('messages.success.created');
        $errorMessage = Config::get('messages.error.validation');

        parent::require_ownership(1);

        $request->validate([
            'name'     => 'required|string|min:4|max:100',
            'username' => 'required|string|min:4|max:50|unique:users,username',
            'password' => 'required|string|confirmed|min:8|max:50',
            'status'   => 'required|in:Active,Inactive',
            'role'     => 'required|in:Owner,Manager,Reseller',
        ]);

        parent::manager_limit($request->input('role'));

        $username = $request->input('username');
        $name = $request->input('name');
        $role = $request->input('role');

        try {
            User::create([
                'name'        => $request->input('name'),
                'username'    => $request->input('username'),
                'password'    => $request->input('password'),
                'status'      => $request->input('status'),
                'role'        => $request->input('role'),
                'registrar'   => auth()->user()->user_id,
            ]);

            $msg = str_replace(':flag', "<strong>User</strong>", $successMessage);
            $msg = "
                $msg <br>
                <b>Name: $name</b> <br>
                <b>Username: $username</b> <br>
                <b>Role: $role</b>
                ";
            return response()->json([
                'status' => 0,
                'message' => $msg,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 1,
                'message' => str_replace(':info', 'Error Code 202', $errorMessage),
            ]);
        }
    }

    public function manageusersedit($id) {
        $user = User::where('user_id', $id)->first();

        if (empty($user)) {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 202', $errorMessage),])->onlyInput('name');
        }

        parent::require_ownership(1);

        parent::manager_limit($user->role);
        parent::psueAction($user);

        return view('Home.edit_user', compact('user'));
    }

    public function manageusersedit_action(Request $request) {
        $successMessage = Config::get('messages.success.updated');
        $errorMessage = Config::get('messages.error.validation');

        parent::require_ownership(1);

        $request->validate([
            'user_id'  => 'required|string|min:4|max:100|exists:users,user_id',
            'name'     => 'required|string|min:4|max:100',
            'status'   => 'required|in:Active,Inactive',
            'role'     => 'required|in:Owner,Manager,Reseller',
        ]);

        $username = $request->input('username');
        $name = $request->input('name');
        $role = $request->input('role');
        $status = $request->input('status');
        $user = User::where('user_id', $request->input('user_id'))->first();

        $request->validate([
            'username' => 'required|string|min:4|max:50',Rule::unique('users', 'username')->ignore($user->user_id, 'user_id'),
        ]);

        if (empty($user)) {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 203', $errorMessage),])->onlyInput('name');
        }

        parent::manager_limit($user->role);
        parent::psueAction($user);

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
                    'permissions' => $request->input('role'),
                ]);
            } else {
                $user->update([
                    'name'        => $request->input('name'),
                    'username'    => $request->input('username'),
                    'status'      => $request->input('status'),
                    'permissions' => $request->input('role'),
                ]);
            }

            $msg = str_replace(':flag', "<strong>User</strong>", $successMessage);
            $msg = "
                $msg <br>
                <b>Name: $name</b> <br>
                <b>Username: $username</b> <br>
                <b>Role: $role</b> <br>
                <b>Status: $status</b>
                ";
            return response()->json([
                'status' => 0,
                'message' => $msg,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 1,
                'message' => str_replace(':info', 'Error Code 202', $errorMessage),
            ]);
        }
    }

    public function manageuserssaldoedit($id) {
        $user = User::where('user_id', $id)->first();

        if (empty($user)) {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 202', $errorMessage),])->onlyInput('name');
        }

        parent::require_ownership();

        return view('Home.wallet_user', compact('user'));
    }

    public function manageuserssaldoedit_action(Request $request) {
        $successMessage = Config::get('messages.success.updated');
        $errorMessage = Config::get('messages.error.validation');

        parent::require_ownership();

        $request->validate([
            'user_id'  => 'required|string|min:4|max:100|exists:users,user_id',
            'saldo'    => 'required|integer|min:1|max:2000000000',
        ]);

        $new_saldo = $request->input('saldo');
        $user = User::where('user_id', $request->input('user_id'))->first();

        if (empty($user)) {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 203', $errorMessage),])->onlyInput('name');
        }

        $old_saldo = $user->saldo;
        $name = $user->name;
        $username = $user->username;

        try {
            $user->update([
                'saldo' => $request->input('saldo'),
            ]);

            $msg = str_replace(':flag', "<strong>User</strong>", $successMessage);
            $msg = "
                $msg <br>
                <b>Name: $name</b> <br>
                <b>Username: $username</b> <br>
                <b>Old Saldo: $old_saldo</b> <br>
                <b>New Saldo: $new_saldo</b> <br>
                ";
            return response()->json([
                'status' => 0,
                'message' => $msg,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 1,
                'message' => str_replace(':info', 'Error Code 202', $errorMessage),
            ]);
        }
    }

    public function manageusersdelete(Request $request) {
        $successMessage = Config::get('messages.success.deleted');
        $errorMessage = Config::get('messages.error.validation');

        parent::require_ownership(1);

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

            $msg = str_replace(':flag', "<strong>User</strong>", $successMessage);
            $msg = "
                $msg <br>
                <b>User Deleted: $username</b> <br>
                ";
            return response()->json([
                'status' => 0,
                'message' => $msg,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 1,
                'message' => str_replace(':info', 'Error Code 202', $errorMessage),
            ]);
        }
    }

    public function manageusershistoryuser($id) {
        $errorMessage = Config::get('messages.error.validation');
        $histories = UserHistory::where('user_id', $id)->get();

        parent::require_ownership(1);

        if (empty($histories)) {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 202', $errorMessage),])->onlyInput('name');
        }

        return view('Home.history_user', compact('histories'));
    }
}
