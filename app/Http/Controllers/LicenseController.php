<?php

namespace App\Http\Controllers;

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

class LicenseController extends Controller
{
    public function licenseregistrations() {
        if (require_ownership(1, 0, 1)) {
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
                'user_key'  => "<span class='text-$licenseStatus blur Blur copy-license' data-copy='$license->license'>$license->license</span>",
                'devices'   => "<span class='text-white bg-dark'>$devices</span>",
                'duration'  => "<span class='text-$durationC'>$duration</span>",
                'registrar' => userUsername($license->registrar),
                'created'   => "<i class='text-gray-400'>$created</i>",
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
        $request->validated();

        return LicenseHelper::licenseEdit($request);
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