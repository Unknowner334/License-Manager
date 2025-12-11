<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Helpers\LicenseInfo;
use App\Helpers\LicenseHelper;
use App\Http\Requests\LicenseGenerateRequest;
use App\Http\Requests\LicenseUpdateRequest;
use App\Http\Requests\LicenseDeleteRequest;
use App\Models\License;
use App\Models\LicenseHistory;
use App\Models\App;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class LicenseController extends Controller
{
    public function licenselist() {
        return view('License.list');
    }

    public function licensedata() {
        if (require_ownership(1, 0)) {
            $licenses = License::get();
        } else {
            $licenses = License::where('registrar', auth()->user()->user_id)->get();
        }

        $data = $licenses->map(function ($license) {
            if ($license->owner == "") $owner = "N/A"; else $owner = $license->owner;

            $currency = Config::get('messages.settings.currency');
            $cplace = Config::get('messages.settings.currency_place');
            $devices = LicenseInfo::DevicesHooked($license->devices) . '/' . $license->max_devices;
            $durationC = LicenseInfo::RemainingDaysColor(LicenseInfo::RemainingDays($license->expire_date));
            $duration = LicenseInfo::RemainingDays($license->expire_date) . '/' . $license->duration . " Days";
            $created = timeElapsed($license->created_at);
            $licenseStatus = statusColor($license->status);

            $price = number_format(LicenseInfo::licensePriceCalculator($license->app->price, $license->max_devices, $license->duration));
            if ($cplace == 0) {
                $price = $price . $currency;
            } else if ($cplace == 1) {
                $price = $currency . $price;
            } else {
                $price = $price . ' ' . $currency;
            }

            return [
                'id'        => $license->id,
                'edit_id'   => $license->edit_id,
                'owner'     => $owner,
                'app'       => $license->app->name,
                'user_key'  => "<span class='align-middle badge fw-normal text-$licenseStatus fs-6 blur Blur px-3 copy-trigger' data-copy='$license->license'>$license->license</span>",
                'devices'   => "<span class='align-middle badge fw-normal text-white bg-dark fs-6'>$devices</span>",
                'duration'  => "<span class='align-middle badge fw-normal text-$durationC fs-6'>$duration</span>",
                'registrar' => userUsername($license->registrar),
                'created'   => "<i class='align-middle badge fw-normal text-dark fs-6'>$created</i>",
                'price'     => "$price",
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
        $currencyPlace = Config::get('messages.settings.currency_place');

        return view('License.generate', compact('apps', 'currency', 'currencyPlace'));
    }

    public function licensegenerate_action(LicenseGenerateRequest $request) {
        $request->validated();

        return LicenseHelper::licenseGenerate($request);
    }

    public function licenseedit($id) {
        $errorMessage = Config::get('messages.error.validation');

        if (require_ownership(1, 0)) {
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
        $currencyPlace = Config::get('messages.settings.currency_place');

        return view('License.edit', compact('license', 'apps', 'currency', 'currencyPlace'));
    }

    public function licenseedit_action(LicenseUpdateRequest $request) {
        $successMessage = Config::get('messages.success.updated');
        $errorMessage = Config::get('messages.error.validation');

        $request->validated();

        $id = $request->input('edit_id');

        if (require_ownership(1, 0)) {
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
                $licenseName = randomString();
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

    public function licensedelete(LicenseDeleteRequest $request) {
        $successMessage = Config::get('messages.success.deleted');
        $errorMessage = Config::get('messages.error.validation');

        $request->validated();

        try {
            if (require_ownership(1, 0)) {
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

        try {
            if (require_ownership(1, 0)) {
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

            $license->update([
                'devices' => NULL,
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