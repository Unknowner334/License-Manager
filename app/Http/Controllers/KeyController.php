<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Models\Key;
use App\Models\KeyHistory;
use App\Models\App;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class KeyController extends Controller
{
    static function keyPriceCalculator($rank, $basic, $premium, $devices, $duration) {
        $rank = (string) $rank;
        $basic = (int) $basic;
        $premium = (int) $premium;
        $devices = (int) $devices;
        $duration = (int) $duration;

        if ($rank == 'Basic' || $rank == 'basic') {
            $price = $basic;
        } elseif ($rank == 'Premium' || $rank == 'premium') {
            $price = $premium;
        } else {
            return 'N/A';
        }

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

    static function DevicesHooked($key_id) {
        $key = Key::where('edit_id', $key_id)->first();
        if (!$key) return "N/A";

        return $key->histories->where('status', 'Success')->unique('serial_number')->count();
    }

    public function KeyListView(Request $request) {
        if ($request->get('search')) {
            if (auth()->user()->permissions == "Owner") {
                $keys = Key::where('key', $request->get('search'))->orderBy('created_at', 'desc')->paginate(10);
            } else {
                $keys = Key::where('created_by', auth()->user()->user_id)->where('key', $request->get('search'))->orderBy('created_at', 'desc')->paginate(10);
            }

            $fullKeys = Key::where('key', $request->get('search'))->orderBy('created_at', 'desc')->get();
        } else {
            if (auth()->user()->permissions == "Owner") {
                $keys = Key::orderBy('created_at', 'desc')->paginate(10);
            } else {
                $keys = Key::where('created_by', auth()->user()->user_id)->orderBy('created_at', 'desc')->paginate(10);
            }

            $fullKeys = Key::orderBy('created_at', 'desc')->get();
        }
        $currency = Config::get('messages.settings.currency');

        return view('Key.list', compact('keys', 'fullKeys', 'currency'));
    }

    public function KeyGenerateView() {
        $apps = App::orderBy('created_at', 'desc')->get();
        $currency = Config::get('messages.settings.currency');

        return view('Key.generate', compact('apps', 'currency'));
    }

    public function KeyGeneratePost(Request $request) {
        $successMessage = Config::get('messages.success.created');
        $errorMessage = Config::get('messages.error.validation');

        $request->validate([
            'app'      => 'required|string|exists:apps,app_id|min:6|max:36',
            'owner'    => 'max:50',
            'rank'     => 'required|in:Basic,Premium',
            'duration' => 'required|integer',
            'status'   => 'required|in:Active,Inactive',
            'devices'  => 'required|integer|min:1',
        ]);

        do {
            $key = parent::randomString(16);
            $keyExists = Key::where('key', $key)->exists();
        } while ($keyExists);

        $now = Carbon::now();
        $expire_date = $now->addDays((int) $request->input('duration'));

        try {
            Key::create([
                'app_id'      => $request->input('app'),
                'owner'       => $request->input('owner') ?? "",
                'rank'        => $request->input('rank'),
                'duration'    => $request->input('duration'),
                'expire_date' => $expire_date,
                'key'         => $key,
                'status'      => $request->input('status'),
                'max_devices' => $request->input('devices'),
                'created_by'  => auth()->user()->user_id,
            ]);

            return redirect()->route('keys.generate')->with('msgSuccess', str_replace(':flag', "Key " . $key, $successMessage));
        } catch (\Exception $e) {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 201', $errorMessage),])->onlyInput('name');
        }
    }

    public function KeyEditView($id) {
        $errorMessage = Config::get('messages.error.validation');

        if (auth()->user()->permissions == "Owner") {
            $key = Key::where('edit_id', $id)->first();

            if (empty($key)) {
                return back()->withErrors(['name' => str_replace(':info', 'Error Code 201', $errorMessage),])->onlyInput('name');
            }
        } else {
            $key = Key::where('created_by', auth()->user()->user_id)->where('edit_id', $id)->first();

            if (empty($key)) {
                return back()->withErrors(['name' => str_replace(':info', 'Error Code 202, Access Forbidden', $errorMessage),])->onlyInput('name');
            }
        }

        $apps = App::orderBy('created_at', 'desc')->get();
        $currency = Config::get('messages.settings.currency');

        return view('Key.edit', compact('key', 'apps', 'currency'));
    }

    public function KeyEditPost(Request $request) {
        $successMessage = Config::get('messages.success.updated');
        $errorMessage = Config::get('messages.error.validation');

        $request->validate([
            'edit_id'  => 'required|string|min:6|max:36',
            'key'      => 'max:50',
            'app'      => 'required|string|exists:apps,app_id|min:6|max:36',
            'owner'    => 'max:50',
            'rank'     => 'required|in:Basic,Premium',
            'duration' => 'required|integer',
            'status'   => 'required|in:Active,Inactive',
            'devices'  => 'required|integer|min:1',
        ]);

        if (auth()->user()->permissions == "Owner") {
            $key = Key::where('edit_id', $request->input('edit_id'))->first();

            if (empty($key)) {
                return back()->withErrors(['name' => str_replace(':info', 'Error Code 201', $errorMessage),])->onlyInput('name');
            }
        } else {
            $key = Key::where('created_by', auth()->user()->user_id)->where('edit_id', $id)->first();

            if (empty($key)) {
                return back()->withErrors(['name' => str_replace(':info', 'Error Code 202, Access Forbidden', $errorMessage),])->onlyInput('name');
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
                    'rank'        => $request->input('rank'),
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
                    'rank'        => $request->input('rank'),
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

    public function KeyHistoryView($id) {
        $errorMessage = Config::get('messages.error.validation');

        if (auth()->user()->permissions == "Owner") {
            $keyHistory = Key::where('edit_id', $id)->first()->histories()->paginate(10);

            if (empty($keyHistory)) {
                return back()->withErrors(['name' => str_replace(':info', 'Error Code 201', $errorMessage),])->onlyInput('name');
            }
        } else {
            $keyHistory = Key::where('created_by', auth()->user()->user_id)->where('edit_id', $id)->first()->histories()->paginate(10);

            if (empty($keyHistory)) {
                return back()->withErrors(['name' => str_replace(':info', 'Error Code 202, Access Forbidden', $errorMessage),])->onlyInput('name');
            }
        }

        return view('Key.history', compact('keyHistory', 'id'));
    }
    
    public function KeyHistoryDelete(Request $request) {
        $successMessage = Config::get('messages.success.deleted');
        $errorMessage = Config::get('messages.error.validation');

        $request->validate([
            'key_id'  => 'required|string|min:6|max:36',
            'id'  => 'required|integer|min:1',
        ]);

        if (auth()->user()->permissions == "Owner") {
            $key = Key::where('edit_id', $request->input('key_id'))->first();

            if (empty($key)) {
                return back()->withErrors(['name' => str_replace(':info', 'Error Code 201', $errorMessage),])->onlyInput('name');
            }
        } else {
            $key = Key::where('created_by', auth()->user()->user_id)->where('key_id', $id)->first();

            if (empty($key)) {
                return back()->withErrors(['name' => str_replace(':info', 'Error Code 202, Access Forbidden', $errorMessage),])->onlyInput('name');
            }
        }

        $keyName = $key->key;

        try {
            $key->histories()->where('id', $request->input('id'))->first()->delete();

            return redirect()->route('keys.history', ['id' => $request->input('key_id')])->with('msgSuccess', str_replace(':flag', "Key " . $keyName . " History", $successMessage));
        } catch (\Exception $e) {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 203', $errorMessage),])->onlyInput('name');
        }
    }

    public function KeyHistoryDeleteAll(Request $request) {
        $successMessage = Config::get('messages.success.deleted');
        $errorMessage = Config::get('messages.error.validation');

        $request->validate([
            'id'  => 'required|string|min:6|max:36',
        ]);

        if (auth()->user()->permissions == "Owner") {
            $key = Key::where('edit_id', $request->input('id'))->first();

            if (empty($key)) {
                return back()->withErrors(['name' => str_replace(':info', 'Error Code 201', $errorMessage),])->onlyInput('name');
            }
        } else {
            $key = Key::where('created_by', auth()->user()->user_id)->where('id', $id)->first();

            if (empty($key)) {
                return back()->withErrors(['name' => str_replace(':info', 'Error Code 202, Access Forbidden', $errorMessage),])->onlyInput('name');
            }
        }

        $keyName = $key->key;

        try {
            $key->histories()->delete();

            return redirect()->route('keys.history', ['id' => $request->input('id')])->with('msgSuccess', str_replace(':flag', "Key " . $keyName . " History", $successMessage));
        } catch (\Exception $e) {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 203', $errorMessage),])->onlyInput('name');
        }
    }

    public function KeyDelete(Request $request) {
        $successMessage = Config::get('messages.success.deleted');
        $errorMessage = Config::get('messages.error.validation');

        $request->validate([
            'edit_id'  => 'required|string|min:6|max:36',
        ]);

        if (auth()->user()->permissions == "Owner") {
            $key = Key::where('edit_id', $request->input('edit_id'))->first();

            if (empty($key)) {
                return back()->withErrors(['name' => str_replace(':info', 'Error Code 201', $errorMessage),])->onlyInput('name');
            }
        } else {
            $key = Key::where('created_by', auth()->user()->user_id)->where('edit_id', $id)->first();

            if (empty($key)) {
                return back()->withErrors(['name' => str_replace(':info', 'Error Code 202, Access Forbidden', $errorMessage),])->onlyInput('name');
            }
        }

        $keyName = $key->key;

        try {
            $key->delete();

            return redirect()->route('keys')->with('msgSuccess', str_replace(':flag', "Key " . $keyName, $successMessage));
        } catch (\Exception $e) {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 203', $errorMessage),])->onlyInput('name');
        }
    }
}