<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Models\License;
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

    public function licensedata_10() {
        $licenses = License::orderBy('created_at', 'desc')->limit(10)->get();

        $data = $licenses->map(function ($license) {
            $currency = Config::get('messages.settings.currency');
            $created = timeElapsed($license->created_at);
            $registrar = userUsername($license->registrar);
            $licenseName = censorText($license->license);

            return [
                'id'        => "<span class='align-middle badge text-dark'>#$license->id</span>",
                'user_key'  => "<span class='align-middle badge text-dark'>$licenseName</span>",
                'duration'  => "<span class='align-middle badge text-dark'>$license->duration Days</span>",
                'devices'   => "<span class='align-middle badge text-primary'>$license->max_devices Devices</span>",
                'registrar' => "<span class='align-middle badge text-primary'>$registrar</span>",
                'created'   => "<i class='align-middle badge fw-normal text-muted'>$created</span>",
            ];
        });

        return response()->json([
            'status' => 0,
            'data'   => $data
        ]);
    }

    public function dashboard() {
        $loginTime = session('login_time');
        $sessionLifetime = session('session_lifetime');
        $expiryTime = $loginTime ? $loginTime->copy()->addMinutes($sessionLifetime) : null;

        return view('Home.dashboard', compact('expiryTime', 'loginTime', 'sessionLifetime'));
    }

    public function managereferrable() {
        require_ownership();
        
        return view('Home.manage_reff');
    }

    public function managereferrabledata() {
        require_ownership();
        
        $reffs = Reff::get();

        $data = $reffs->map(function ($reff) {
            $created = timeElapsed($reff->created_at);
            $reffStatus = statusColor($reff->status);
            $users = 0;

            foreach($reff->users as $user) {
                $users += 1;
            }

            return [
                'id'        => $reff->id,
                'edit_id'   => $reff->edit_id,
                'code'      => "<span class='align-middle badge fw-normal text-$reffStatus fs-6 blur Blur px-3 copy-trigger' data-copy='$reff->code'>$reff->code</span>",
                'status'    => "<span class='align-middle badge fw-normal text-$reffStatus fs-6'>$reff->status</span>",
                'registrar' => userUsername($reff->registrar),
                'created'   => "<i class='align-middle badge fw-normal text-dark fs-6'>$created</i>",
                'users'     => $users . " Users",
            ];
        });

        return response()->json([
            'status' => 0,
            'data'   => $data
        ]);
    }

    public function managereferrablegenerate() {
        $errorMessage = Config::get('messages.error.validation');

        require_ownership();

        return view('Home.generate_reff');
    }

    public function managereferrablegenerate_action(Request $request) {
        $successMessage = Config::get('messages.success.created');
        $errorMessage = Config::get('messages.error.validation');

        require_ownership(0, 1, 1);

        $request->validate([
            'status'   => 'required|in:Active,Inactive',
        ]);

        if ($request->input('code') == '') {
            do {
                $code = randomString();
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
                'registrar'   => auth()->user()->user_id,
            ]);

            return response()->json([
                'status' => 0,
                'message' => str_replace(':flag', "<b>Reff</b> " . $code, $successMessage),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 1,
                'message' => str_replace(':info', 'Error Code 201', $errorMessage),
            ]);
        }
    }

    public function managereferrableedit($id) {
        $errorMessage = Config::get('messages.error.validation');
        $reff = Reff::where('edit_id', $id)->first();

        require_ownership();

        if (empty($reff)) {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 202', $errorMessage),])->onlyInput('name');
        }

        return view('Home.edit_reff', compact('reff'));
    }

    public function managereferrableedit_action(Request $request) {
        $successMessage = Config::get('messages.success.updated');
        $errorMessage = Config::get('messages.error.validation');

        require_ownership(0, 1, 1);

        $request->validate([
            'edit_id'  => 'required|string|min:4|max:36|exists:referrable_codes,edit_id',
            'status'   => 'required|in:Active,Inactive',
        ]);

        $reff = Reff::where('edit_id', $request->input('edit_id'))->first();

        if (empty($reff)) {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 202', $errorMessage),])->onlyInput('name');
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

            return response()->json([
                'status' => 0,
                'message' => str_replace(':flag', "<b>Reff</b> " . $code, $successMessage),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 1,
                'message' => str_replace(':info', 'Error Code 201', $errorMessage),
            ]);
        }
    }

    public function managereferrabledelete(Request $request) {
        $successMessage = Config::get('messages.success.deleted');
        $errorMessage = Config::get('messages.error.validation');

        require_ownership(0, 1, 1);

        $request->validate([
            'edit_id'  => 'required|string|min:4|max:36|exists:referrable_codes,edit_id',
        ]);

        $reff = Reff::where('edit_id', $request->input('edit_id'))->first();

        if (empty($reff)) {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 202', $errorMessage),])->onlyInput('name');
        }

        $code = $reff->code;

        try {
            $reff->delete();

            return response()->json([
                'status' => 0,
                'message' => str_replace(':flag', "<b>Reff</b> " . $code, $successMessage),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 1,
                'message' => str_replace(':info', 'Error Code 201', $errorMessage),
            ]);
        }
    }
}
