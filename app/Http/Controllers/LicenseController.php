<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Models\License;
use App\Models\LicenseHistory;
use App\Models\App;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class LicenseController extends Controller
{
    static function licensePriceCalculator($price, $devices, $duration) {
        $price = (int) $price;
        $devices = (int) $devices;
        $duration = (int) $duration;

        $duration = $duration / 30;
        $total = $price * $duration * $devices;

        return $total;
    }

    static function saldoPriceCut($devices, $duration) {
        $basePrice = 10;
        $devices = (int) $devices;
        $duration = (int) $duration;

        $duration = $duration / 30;
        $total = $basePrice * $duration * $devices;
        $total = [$total, number_format($total)];

        return $total;
    }

    static function RemainingDays($expire_date) {
        if (empty($expire_date)) {
            return 'N/A';
        }

        try {
            $expire = Carbon::parse($expire_date);
        } catch (\Exception $e) {
            return 'N/A';
        }

        $remainingDays = now()->diffInDays($expire, false) + 1;
        return max(0, (int) $remainingDays);
    }

    static function RemainingDaysColor($remainingDays) {
        if ($remainingDays == "N/A") {
            return "danger";
        } elseif ($remainingDays <= 10) {
            return 'danger';
        } elseif ($remainingDays <= 20) {
            return 'warning';
        } elseif ($remainingDays <= 30) {
            return 'success';
        } else {
            return 'success';
        }
    }

    static function RankColor($rank) {
        if ($rank == "Basic" || $rank == "basic") {
            return "success";
        } elseif ($rank == "Premium" || $rank == "premium") {
            return "warning";
        } else {
            return "danger";
        }
    }

    static function DevicesHooked($serials) {
        $items = preg_split('/[\s,]+/', trim($serials), -1, PREG_SPLIT_NO_EMPTY);
        $count = count($items);
        return $count;
    }

    public function licenselist(Request $request) {
        return view('License.list');
    }

    public function licensedata() {
        if (parent::require_ownership(1, 0)) {
            $licenses = License::get();
        } else {
            $licenses = License::where('registrar', auth()->user()->user_id)->get();
        }

        $data = $licenses->map(function ($license) {
            if ($license->owner == "") $owner = "N/A"; else $owner = $license->owner;

            $currency = Config::get('messages.settings.currency');
            $devices = $this->DevicesHooked($license->devices) . '/' . $license->max_devices;
            $durationC = $this->RemainingDaysColor($this->RemainingDays($license->expire_date));
            $duration = $this->RemainingDays($license->expire_date) . '/' . $license->duration . " Days";
            $created = Controller::timeElapsed($license->created_at);
            $licenseStatus = Controller::statusColor($license->status);

            $price = number_format(LicenseController::licensePriceCalculator($license->app->price, $license->max_devices, $license->duration));

            return [
                'id'        => $license->id,
                'edit_id'   => $license->edit_id,
                'owner'     => $owner,
                'app'       => $license->app->name,
                'user_key'  => "<span class='align-middle badge fw-normal text-$licenseStatus fs-6 blur Blur px-3 copy-trigger' data-copy='$license->license'>$license->license</span>",
                'devices'   => "<span class='align-middle badge fw-normal text-white bg-dark fs-6'>$devices</span>",
                'duration'  => "<span class='align-middle badge fw-normal text-$durationC fs-6'>$duration</span>",
                'registrar' => Controller::userUsername($license->registrar),
                'created'   => "<i class='align-middle badge fw-normal text-dark fs-6'>$created</i>",
                'price'     => "$price$currency",
            ];
        });

        return response()->json([
            'status' => 0,
            'data'   => $data
        ]);
    }

    public function licensegenerate() {
        $apps = App::where('status', 'Active')->orderBy('created_at', 'desc')->get();
        $currency = Config::get('messages.settings.currency');

        return view('License.generate', compact('apps', 'currency'));
    }

    public function licensegenerate_action(Request $request) {
        $successMessage = Config::get('messages.success.created');
        $errorMessage = Config::get('messages.error.validation');

        $request->validate([
            'app'      => 'required|string|exists:apps,app_id|min:6|max:36',
            'owner'    => 'max:50',
            'duration' => 'required|integer',
            'status'   => 'required|in:Active,Inactive',
            'devices'  => 'required|integer|min:1|max:1000000',
        ]);

        do {
            $license = parent::randomString();
            $licenseExists = License::where('license', $license)->exists();
        } while ($licenseExists);

        $now = Carbon::now();
        $expire_date = $now->addDays((int) $request->input('duration'));
        $currency = Config::get('messages.settings.currency');
        $owner = $request->input('owner') ?? "";
        $duration = $request->input('duration');
        $status = $request->input('status');
        $devices = $request->input('devices');
        $appName = App::where('app_id', $request->input('app'))->first()->name;
        $saldo_price = $this->saldoPriceCut($duration, $devices);
        $saldo = parent::saldoData(auth()->user()->saldo, auth()->user()->role, 1);
        if ($saldo_price[0] > $saldo[0]) {
            return response()->json([
                'status' => 1,
                'message' => "You don't have <b>enough</b> Saldo to generate this license.",
            ]);
        }
        auth()->user()->deductSaldo($saldo_price[0]);

        if (is_int($saldo[0])) {
            $saldo_ext = number_format($saldo[0] - $saldo_price[0]);
            $saldo_ext = $saldo_ext . $currency . " Left";
        } else {
            $saldo_ext = $saldo[0];
        }

        try {
            License::create([
                'app_id'      => $request->input('app'),
                'owner'       => $owner,
                'duration'    => $duration,
                'expire_date' => $expire_date,
                'license'     => $license,
                'status'      => $status,
                'max_devices' => $devices,
                'registrar'   => auth()->user()->user_id,
            ]);

            $licenses = License::where('license', $license)->where('duration', $duration)->where('max_devices', $devices)->first();

            LicenseHistory::create([
                'license_id' => $licenses->edit_id,
                'user'   => auth()->user()->user_id,
                'type'   => 'Create',
            ]);

            $msg = str_replace(':flag', "<b>License</b> " . $license, $successMessage);
            $saldo_cut = $saldo_price[1];
            $msg = "
                $msg <br>
                <b>Saldo Cut: $saldo_cut$currency</b> <br>
                <b>Saldo: $saldo_ext</b>
            ";
            return response()->json([
                'status' => 0,
                'message' => $msg,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 1,
                'message' => str_replace(':info', 'Error Code 201', $errorMessage),
            ]);
        }
    }

    public function licenseedit($id) {
        $errorMessage = Config::get('messages.error.validation');

        if (parent::require_ownership(1, 0)) {
            $license = License::where('edit_id', $id)->first();

            if (empty($license)) {
                return back()->withErrors(['name' => str_replace(':info', 'Error Code 201', $errorMessage),])->onlyInput('name');
            }
        } else {
            $license = License::where('registrar', auth()->user()->user_id)->where('edit_id', $id)->first();

            if (empty($license)) {
                return back()->withErrors(['name' => str_replace(':info', 'Error Code 202, Access Forbidden', $errorMessage),])->onlyInput('name');
            }
        }

        $apps = App::orderBy('created_at', 'desc')->get();
        $currency = Config::get('messages.settings.currency');

        return view('License.edit', compact('license', 'apps', 'currency'));
    }

    public function licenseedit_action(Request $request) {
        $successMessage = Config::get('messages.success.updated');
        $errorMessage = Config::get('messages.error.validation');

        $request->validate([
            'edit_id'  => 'required|string|min:6|max:36',
            'license'  => 'max:50',
            'app'      => 'required|string|exists:apps,app_id|min:6|max:36',
            'owner'    => 'max:50',
            'duration' => 'required|integer',
            'status'   => 'required|in:Active,Inactive',
            'devices'  => 'required|integer|min:1|max:1000000',
        ]);

        $id = $request->input('edit_id');

        if (parent::require_ownership(1, 0)) {
            $license = License::where('edit_id', $request->input('edit_id'))->first();

            if (empty($license)) {
                return back()->withErrors(['name' => str_replace(':info', 'Error Code 201', $errorMessage),])->onlyInput('name');
            }
        } else {
            $license = License::where('registrar', auth()->user()->user_id)->where('edit_id', $id)->first();

            if (empty($license)) {
                return back()->withErrors(['name' => str_replace(':info', 'Error Code 403, <b>Access Forbidden</b>', $errorMessage),])->onlyInput('name');
            }
        }

        if ($request->input('license') == '') {
            do {
                $licenseName = parent::randomString();
                $licenseExists = License::where('license', $keyName)->exists();
            } while ($licenseExists);
        } else {
            $licenseName = $request->input('license');

            $request->validate([
                'license' => [
                    'required',
                    'string',
                    'min:6',
                    'max:50',
                    Rule::unique('licenses', 'license')->ignore($license->edit_id, 'edit_id')
                ],
            ]);
        }

        $now = Carbon::now();
        $expire_date = $now->addDays((int) $request->input('duration'));
        $currency = Config::get('messages.settings.currency');
        if ($request->has('duration-update')) {
            $saldo_price = $this->saldoPriceCut($request->input('duration'), $request->input('devices'));
        } else {
            $saldo_price = $this->saldoPriceCut($license->duration, $request->input('devices'));
        }
        $saldo = parent::saldoData(auth()->user()->saldo, auth()->user()->role, 1);
        if (is_int($saldo[0]) && $saldo_price[0] > $saldo[0]) {
            return response()->json([
                'status' => 1,
                'message' => "You don't have <b>enough</b> Saldo to edit this license.",
            ]);
        }
        auth()->user()->deductSaldo($saldo_price[0]);

        if (is_int($saldo[0])) {
            $saldo_ext = number_format($saldo[0] - $saldo_price[0]);
            $saldo_ext = $saldo_ext . $currency . " Left";
        } else {
            $saldo_ext = $saldo[0];
        }

        try {
            if ($request->has('duration-update')) {
                $license->update([
                    'app_id'      => $request->input('app'),
                    'owner'       => $request->input('owner') ?? "",
                    'duration'    => $request->input('duration'),
                    'expire_date' => $expire_date,
                    'license'     => $licenseName,
                    'status'      => $request->input('status'),
                    'max_devices' => $request->input('devices'),
                ]);
            } else {
                $license->update([
                    'app_id'      => $request->input('app'),
                    'owner'       => $request->input('owner') ?? "",
                    'license'     => $licenseName,
                    'status'      => $request->input('status'),
                    'max_devices' => $request->input('devices'),
                ]);
            }

            LicenseHistory::create([
                'license_id' => $license->edit_id,
                'user'       => auth()->user()->user_id,
                'type'       => 'Update',
            ]);

            $msg = str_replace(':flag', "<b>License</b> " . $licenseName, $successMessage);
            $saldo_cut = $saldo_price[1];
            $msg = "
                $msg <br>
                <b>Saldo Cut: $saldo_cut$currency</b> <br>
                <b>Saldo: $saldo_ext</b>
            ";
            return response()->json([
                'status' => 0,
                'message' => $msg,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 1,
                'message' => str_replace(':info', 'Error Code 203', $errorMessage),
            ]);
        }
    }

    public function licensedelete(Request $request) {
        $successMessage = Config::get('messages.success.deleted');
        $errorMessage = Config::get('messages.error.validation');

        $request->validate([
            'edit_id'  => 'required|string|min:6|max:36',
        ]);

        if (parent::require_ownership(1, 0)) {
            $license = License::where('edit_id', $request->input('edit_id'))->first();

            if (empty($license)) {
                return back()->withErrors(['name' => str_replace(':info', 'Error Code 201', $errorMessage),])->onlyInput('name');
            }
        } else {
            $license = License::where('registrar', auth()->user()->user_id)->where('edit_id', $request->input('edit_id'))->first();

            if (empty($license)) {
                return back()->withErrors(['name' => str_replace(':info', 'Error Code 403, <b>Access Forbidden</b>', $errorMessage),])->onlyInput('name');
            }
        }

        $licenseName = $license->license;

        try {
            $license->delete();

            return response()->json([
                'status' => 0,
                'message' => str_replace(':flag', "<b>License</b> " . $licenseName, $successMessage),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 1,
                'message' => str_replace(':info', 'Error Code 202', $errorMessage),
            ]);
        }
    }

    public function licenseresetapi($id = "") {
        $successMessage = Config::get('messages.success.reseted');
        $errorMessage = Config::get('messages.error.validation');

        if (parent::require_ownership(1, 0)) {
            $license = License::where('edit_id', $id)->first();

            if (empty($license)) {
                return back()->withErrors(['name' => str_replace(':info', 'Error Code 201', $errorMessage),])->onlyInput('name');
            }
        } else {
            $license = License::where('registrar', auth()->user()->user_id)->where('edit_id', $id)->first();

            if (empty($license)) {
                return back()->withErrors(['name' => str_replace(':info', 'Error Code 403, <b>Access Forbidden</b>', $errorMessage),])->onlyInput('name');
            }
        }

        $licenseName = $license->license;

        try {
            $license->update([
                'devices' => "",
            ]);

            return response()->json([
                'status' => 0,
                'message' => str_replace(':flag', '<b>License</b> ' . $licenseName, $successMessage),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 1,
                'message' => str_replace(':info', 'Error Code 202', $errorMessage),
            ]);
        }
    }
}