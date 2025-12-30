<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Config;
use App\Helpers\LicenseInfo;
use App\Helpers\LicenseHelper;
use App\Http\Requests\LicenseGenerateRequest;
use App\Http\Requests\LicenseUpdateRequest;
use App\Http\Requests\LicenseDataRequest;
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
                'user_key'  => "<span class='text-[18px] text-$licenseStatus blur hover:blur-none Blur copy-license p-3 rounded transition-all duration-200' data-copy='$license->license'>$license->license</span>",
                'devices'   => "<span class='text-[16px] font-normal text-white bg-dark px-3 py-1 rounded'>$devices</span>",
                'duration'  => "<span class='text-[16px] text-$durationC'>$duration</span>",
                'registrar' => userUsername($license->registrar),
                'created'   => "<i class='text-md text-gray-500'>$created</i>",
                'price'     => "<span class='text-md'>$price</span>",
            ];
        });

        return response()->json([
            'status' => 0,
            'data'   => $data
        ]);
    }

    public function licensedata(LicenseDataRequest $request) {
        $errorMessage = Config::get('messages.error.validation');
        $request->validated();

        $license = License::where('edit_id', $request->input('id'))->first();

        try {
            if (!$license) {
                return response()->json([
                    'status' => 1,
                    'message' => str_replace(':info', 'Error Code 202', $errorMessage),
                ]);
            }

            return response()->json([
                'status' => 0,
                'app' => $license->app->app_id,
                'user_license' => $license->license,
                'license_status' => $license->status,
                'owner' => $license->owner,
                'duration' => $license->duration,
                'max_devices' => $license->max_devices,
                'devices' => $license->devices,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 1,
                'message' => str_replace(':info', 'Error Code 201', $errorMessage),
            ]);
        }
        
    }

    public function licenseregister(LicenseGenerateRequest $request) {
        $request->validated();

        return LicenseHelper::licenseGenerate($request);
    }

    public function licenseupdate(LicenseUpdateRequest $request) {
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