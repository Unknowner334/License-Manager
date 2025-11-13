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

    public function KeyListView() {
        if (auth()->user()->permissions == "Owner") {
            $keys = Key::orderBy('created_at', 'desc')->paginate(10);
        } else {
            $keys = Key::where('created_by', auth()->user()->username)->orderBy('created_at', 'desc')->paginate(10);
        }
        $fullKeys = Key::orderBy('created_at', 'desc')->get();
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
                'created_by'  => auth()->user()->username,
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
            $key = Key::where('created_by', auth()->user()->username)->where('edit_id', $id)->first();

            if (empty($key)) {
                return back()->withErrors(['name' => str_replace(':info', 'Error Code 202, Access Forbidden', $errorMessage),])->onlyInput('name');
            }
        }

        $apps = App::orderBy('created_at', 'desc')->get();
        $currency = Config::get('messages.settings.currency');

        return view('Key.edit', compact('key', 'apps', 'currency'));
    }
}