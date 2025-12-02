<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Models\Key;
use App\Models\App;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class KeyController extends Controller
{
    static function keyPriceCalculator($price, $devices, $duration) {
        $price = (int) $price;
        $devices = (int) $devices;
        $duration = (int) $duration;

        $duration = $duration / 30;
        $total = $price * $duration * $devices;

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

    public function keylist(Request $request) {
        if (parent::require_ownership(1, 0)) {
            $keys = Key::get();
        } else {
            $keys = Key::where('registrar', auth()->user()->user_id)->get();
        }
        $currency = Config::get('messages.settings.currency');

        return view('Key.list', compact('keys', 'currency'));
    }

    public function keygenerate() {
        $apps = App::where('status', 'Active')->orderBy('created_at', 'desc')->get();
        $currency = Config::get('messages.settings.currency');

        return view('Key.generate', compact('apps', 'currency'));
    }

    public function keygenerate_action(Request $request) {
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
            $key = parent::randomString(16);
            $keyExists = Key::where('key', $key)->exists();
        } while ($keyExists);

        $now = Carbon::now();
        $expire_date = $now->addDays((int) $request->input('duration'));
        $saldo_price = 10;
        $currency = Config::get('messages.settings.currency');
        $owner = $request->input('owner') ?? "";
        $duration = $request->input('duration');
        $status = $request->input('status');
        $devices = $request->input('devices');
        $appName = App::where('app_id', $request->input('app'))->first()->name;
        $saldo = parent::saldoData(auth()->user()->saldo, auth()->user()->role, 1);
        auth()->user()->deductSaldo($saldo_price);

        if (is_int($saldo[0])) {
            $saldo_ext = (int) $saldo[0] - $saldo_price . $currency;
        } else {
            $saldo_ext = $saldo[0];
        }

        try {
            Key::create([
                'app_id'      => $request->input('app'),
                'owner'       => $owner,
                'duration'    => $duration,
                'expire_date' => $expire_date,
                'key'         => $key,
                'status'      => $status,
                'max_devices' => $devices,
                'registrar'   => auth()->user()->user_id,
            ]);

            $msg = str_replace(':flag', "<b>Key</b>", $successMessage);
            return redirect()->route('keys.generate')->with('msgSuccess',
                "
                $msg <br>
                <i class='bi bi-terminal'></i> <b>App: $appName</b> <br>
                <i class='bi bi-key'></i> <b>Key: $key</b> <br>
                <i class='bi bi-award'></i> <b>Owner: $owner</b> <br>
                <i class='bi bi-clock'></i> <b>Duration: $duration Days</b> <br>
                <i class='bi bi-clipboard-check'></i> <b>Status: $status</b> <br>
                <i class='bi bi-phone'></i> <b>Max Devices: $devices</b> <br>
                <i class='bi bi-wallet'></i> <b>Saldo: $saldo_ext</b>
                "
            );
        } catch (\Exception $e) {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 201', $errorMessage),])->onlyInput('name');
        }
    }

    public function keyedit($id) {
        $errorMessage = Config::get('messages.error.validation');

        if (parent::require_ownership(1, 0)) {
            $key = Key::where('edit_id', $id)->first();

            if (empty($key)) {
                return back()->withErrors(['name' => str_replace(':info', 'Error Code 201', $errorMessage),])->onlyInput('name');
            }
        } else {
            $key = Key::where('registrar', auth()->user()->user_id)->where('edit_id', $id)->first();

            if (empty($key)) {
                return back()->withErrors(['name' => str_replace(':info', 'Error Code 202, Access Forbidden', $errorMessage),])->onlyInput('name');
            }
        }

        $apps = App::orderBy('created_at', 'desc')->get();
        $currency = Config::get('messages.settings.currency');

        return view('Key.edit', compact('key', 'apps', 'currency'));
    }

    public function keyedit_action(Request $request) {
        $successMessage = Config::get('messages.success.updated');
        $errorMessage = Config::get('messages.error.validation');

        $request->validate([
            'edit_id'  => 'required|string|min:6|max:36',
            'key'      => 'max:50',
            'app'      => 'required|string|exists:apps,app_id|min:6|max:36',
            'owner'    => 'max:50',
            'duration' => 'required|integer',
            'status'   => 'required|in:Active,Inactive',
            'devices'  => 'required|integer|min:1|max:1000000',
        ]);

        if (parent::require_ownership(1, 0)) {
            $key = Key::where('edit_id', $request->input('edit_id'))->first();

            if (empty($key)) {
                return back()->withErrors(['name' => str_replace(':info', 'Error Code 201', $errorMessage),])->onlyInput('name');
            }
        } else {
            $key = Key::where('created_by', auth()->user()->user_id)->where('edit_id', $id)->first();

            if (empty($key)) {
                return back()->withErrors(['name' => str_replace(':info', 'Error Code 403, <b>Access Forbidden</b>', $errorMessage),])->onlyInput('name');
            }
        }

        if ($request->input('key') == '') {
            do {
                $keyName = parent::randomString(16);
                $keyExists = Key::where('key', $keyName)->exists();
            } while ($keyExists);
        } else {
            $keyName = $request->input('key');

            $request->validate([
                'key' => [
                    'required',
                    'string',
                    'min:6',
                    'max:50',
                    Rule::unique('key_codes', 'key')->ignore($key->edit_id, 'edit_id')
                ],
            ]);
        }

        $now = Carbon::now();
        $expire_date = $now->addDays((int) $request->input('duration'));

        try {
            if ($request->has('duration-update')) {
                $key->update([
                    'app_id'      => $request->input('app'),
                    'owner'       => $request->input('owner') ?? "",
                    'duration'    => $request->input('duration'),
                    'expire_date' => $expire_date,
                    'key'         => $keyName,
                    'status'      => $request->input('status'),
                    'max_devices' => $request->input('devices'),
                ]);
            } else {
                $key->update([
                    'app_id'      => $request->input('app'),
                    'owner'       => $request->input('owner') ?? "",
                    'key'         => $keyName,
                    'status'      => $request->input('status'),
                    'max_devices' => $request->input('devices'),
                ]);
            }

            return redirect()->route('keys.edit', $request->input('edit_id'))->with('msgSuccess', str_replace(':flag', "Key " . $keyName, $successMessage));
        } catch (\Exception $e) {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 203', $errorMessage),])->onlyInput('name');
        }
    }

    public function keydelete(Request $request) {
        $successMessage = Config::get('messages.success.deleted');
        $errorMessage = Config::get('messages.error.validation');

        $request->validate([
            'edit_id'  => 'required|string|min:6|max:36',
        ]);

        if (parent::require_ownership(1, 0)) {
            $key = Key::where('edit_id', $request->input('edit_id'))->first();

            if (empty($key)) {
                return back()->withErrors(['name' => str_replace(':info', 'Error Code 201', $errorMessage),])->onlyInput('name');
            }
        } else {
            $key = Key::where('registrar', auth()->user()->user_id)->where('edit_id', $request->input('edit_id'))->first();

            if (empty($key)) {
                return back()->withErrors(['name' => str_replace(':info', 'Error Code 403, <b>Access Forbidden</b>', $errorMessage),])->onlyInput('name');
            }
        }

        $keyName = $key->key;

        try {
            $key->delete();

            return redirect()->route('keys')->with('msgSuccess', str_replace(':flag', "<b>Key</b> " . $keyName, $successMessage));
        } catch (\Exception $e) {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 202', $errorMessage),])->onlyInput('name');
        }
    }
}