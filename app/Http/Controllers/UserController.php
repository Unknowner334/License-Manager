<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Models\User;
use App\Models\UserHistory;
use App\Http\Requests\UserGenerateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Helpers\UserHelper;

class UserController extends Controller
{
    public function manageusers() {
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

    public function manageusersgenerate_action(UserGenerateRequest $request) {
        $request->validated();

        return UserHelper::userGenerate($request);
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

    public function manageusersedit_action(UserUpdateRequest $request) {
        $request->validated();

        return UserHelper::userEdit($request);
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
