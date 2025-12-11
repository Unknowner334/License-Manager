<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Config;
use App\Models\License;
use App\Models\LicenseHistory;
use App\Helpers\LicenseInfo;
use App\Helpers\LicenseHelper;
use Carbon\Carbon;

class LicenseHelper
{
    static function newUserLicense() {
        do {
            $license = randomString();
            $licenseExists = License::where('license', $license)->exists();
        } while ($licenseExists);

        return $license;
    }

    static function licenseGenerate($request) {
        $successMessage = Config::get('messages.success.created');
        $errorMessage = Config::get('messages.error.validation');

        try {
            $now = Carbon::now();
            $license = LicenseHelper::newUserLicense();
            $expire_date = $now->addDays((int) $request->input('duration'));
            $currency = Config::get('messages.settings.currency');
            $cplace = Config::get('messages.settings.currency_place');
            $saldo_price = LicenseInfo::saldoPriceCut($request->input('duration'), $request->input('devices'));
            $saldo = saldoData(auth()->user()->saldo, auth()->user()->role, 1);
            if ($saldo_price[0] > $saldo[0]) {
                return response()->json([
                    'status' => 1,
                    'message' => "You don't have <b>enough</b> Saldo to generate this license."
                ]);
            }
            auth()->user()->deductSaldo($saldo_price[0]);

            if (is_int($saldo[0])) {
                $saldo_ext = number_format($saldo[0] - $saldo_price[0]);
                if ($cplace == 0) {
                    $saldo_ext = $saldo_ext . $currency . " Left";
                } else if ($cplace == 1) {
                    $saldo_ext = $currency . $saldo_ext . " Left";
                } else {
                    $saldo_ext = $saldo_ext . ' ' . $currency . " Left";
                }
            } else {
                $saldo_ext = $saldo[0];
            }

            $saldo_cut = $saldo_price[1];

            if ($cplace == 0) {
                $saldo_cut = $saldo_cut . $currency;
            } else if ($cplace == 1) {
                $saldo_cut = $currency . $saldo_cut;
            } else {
                $saldo_cut = $saldo_cut . ' ' . $currency;
            }

            License::create([
                'app_id'      => $request->input('app'),
                'owner'       => $request->input('owner') ?? "",
                'license'     => $license,
                'status'      => $request->input('status'),
                'max_devices' => $request->input('devices'),
                'duration'    => $request->input('duration'),
                'expire_date' => $expire_date,
                'registrar'   => auth()->user()->user_id,
            ]);

            $licenses = License::where('license', $license)->where('duration', $request->input('duration'))->where('max_devices', $request->input('devices'))->first();

            LicenseHistory::create([
                'license_id' => $licenses->edit_id,
                'user'   => auth()->user()->user_id,
                'type'   => 'Create',
            ]);

            $msg = str_replace(':flag', "<b>License</b> " . $license[0], $successMessage);
            $msg = "
                $msg <br>
                <b>Saldo Cut: $saldo_cut</b> <br>
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
}