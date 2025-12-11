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
        require_ownership(1);

        return view('Home.manage_users');
    }

    public function manageusersdata() {
        require_ownership(1);
        
        $users = User::get();

        $data = $users->map(function ($user) {
            $created = timeElapsed($user->created_at);
            $userStatus = statusColor($user->status);
            $saldo = saldoData($user->saldo, $user->role);
            $saldoS = $saldo[0];
            $saldoC = $saldo[1];
            $roleC = permissionColor($user->role);

            if ($user->referrable != NULL) {
                $reff_status = statusColor($user->referrable->status);
                $reff_code = censorText($user->referrable->code);
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
                'registrar' => userUsername($user->registrar),
                'reff'      => "<span class='align-middle badge fw-normal text-$reff_status fs-6'>$reff_code</span>",
            ];
        });

        return response()->json([
            'status' => 0,
            'data'   => $data
        ]);
    }

    public function manageusersgenerate() {
        require_ownership(1);

        return view('Home.generate_user');
    }

    public function manageusersgenerate_action(Request $request) {
        $successMessage = Config::get('messages.success.created');
        $errorMessage = Config::get('messages.error.validation');

        require_ownership(1, 1, 1);

        $request->validate([
            'name'     => 'required|string|min:4|max:100',
            'username' => 'required|string|min:4|max:50|unique:users,username',
            'password' => 'required|string|confirmed|min:8|max:50',
            'status'   => 'required|in:Active,Inactive',
            'role'     => 'required|in:Owner,Manager,Reseller',
        ]);

        manager_limit($request->input('role'));

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

            $msg = str_replace(':flag', "<strong>User</strong> $username", $successMessage);
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

        require_ownership(1);

        manager_limit($user->role);
        psueAction($user);

        return view('Home.edit_user', compact('user'));
    }

    public function manageusersedit_action(Request $request) {
        $successMessage = Config::get('messages.success.updated');
        $errorMessage = Config::get('messages.error.validation');

        require_ownership(1, 1, 1);

        $request->validate([
            'user_id'  => 'required|string|min:4|max:100|exists:users,user_id',
            'name'     => 'required|string|min:4|max:100',
            'status'   => 'required|in:Active,Inactive',
            'role'     => 'required|in:Owner,Manager,Reseller',
        ]);

        $username = $request->input('username');
        $user = User::where('user_id', $request->input('user_id'))->first();

        $request->validate([
            'username' => 'required|string|min:4|max:50',Rule::unique('users', 'username')->ignore($user->user_id, 'user_id'),
        ]);

        if (empty($user)) {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 203', $errorMessage),])->onlyInput('name');
        }

        manager_limit($user->role);
        psueAction($user);

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

            $msg = str_replace(':flag', "<strong>User</strong> $username", $successMessage);
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

        require_ownership();

        return view('Home.wallet_user', compact('user'));
    }

    public function manageuserssaldoedit_action(Request $request) {
        $successMessage = Config::get('messages.success.updated');
        $errorMessage = Config::get('messages.error.validation');

        require_ownership(0, 1, 1);

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
        $username = $user->username;

        try {
            $user->update([
                'saldo' => $request->input('saldo'),
            ]);

            $msg = str_replace(':flag', "<strong>User</strong> $username", $successMessage);
            $msg = "
                $msg <br>
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

        require_ownership(1, 1, 1);

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

            $msg = str_replace(':flag', "<strong>User</strong> $username", $successMessage);
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

    public function manageusershistoryuser() {
        return view('Home.history_user');
    }

    public function manageusershistorydata($id) {
        require_ownership(1);
        
        $histories = UserHistory::where('user_id', $id)->get();

        $data = $histories->map(function ($h) {
            $created = Controller::timeElapsed($h->created_at);

            if ($h->user_id == NULL) {
                $user_id = "N/A";
            } else {
                $user_id = Controller::censorText($h->user_id, 3);
            }

            $agent = Controller::censorText($h->user_agent, 10);

            return [
                'id'        => $h->id,
                'user_id'   => $user_id,
                'username'  => "<span class='align-middle badge fw-normal text-dark fs-6 blur Blur px-3'>$h->username</span>",
                'created'   => "<i class='align-middle badge fw-normal text-dark fs-6'>$created</i>",
                'status'    => $h->status,
                'type'      => $h->type,
                'ip'        => $h->ip_address,
                'agent'     => "<span class='align-middle badge fw-normal text-dark fs-6 copy-trigger' data-copy='$h->user_agent'>$agent</span>",
            ];
        });

        return response()->json([
            'status' => 0,
            'data'   => $data
        ]);
    }
}
