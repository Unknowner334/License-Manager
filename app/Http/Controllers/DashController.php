<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Http\Requests\ReffGenerateRequest;
use App\Http\Requests\ReffUpdateRequest;
use App\Http\Requests\ReffDeleteRequest;
use App\Helpers\ReffHelper;
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
                'id'        => "<span class='text-dark-text'>#$license->id</span>",
                'user_key'  => "<span class='text-[16px] text-dark-text'>$licenseName</span>",
                'duration'  => "<span class='text-[14px] text-dark-text'>$license->duration Days</span>",
                'devices'   => "<span class='text-primary'>$license->max_devices Devices</span>",
                'registrar' => "<span class='text-primary'>$registrar</span>",
                'created'   => "<i class='text-gray-500'>$created</span>",
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

    public function managereferrablegenerate_action(ReffGenerateRequest $request) {
        $request->validated();

        return ReffHelper::reffGenerate($request);
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

    public function managereferrableedit_action(ReffUpdateRequest $request) {
        $request->validated();

        return ReffHelper::reffEdit($request);
    }

    public function managereferrabledelete(ReffDeleteRequest $request) {
        $request->validated();

        return ReffHelper::reffDelete($request);
    }
}
