<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\WebuiUpdateRequest;

class WebuiController extends Controller
{
    public function webui_settings() {
        return view('Settings.webui_settings');
    }

    public function webui_action(WebuiUpdateRequest $request) {
        $data = $request->validated();

        setSetting('app_name', $data['app_name']);
        setSetting('app_timezone', $data['app_timezone']);
        setSetting('currency', $data['currency']);
        setSetting('currency_place', $data['currency_place']);

        return response()->json([
            'status' => 0,
            'message' => 'WebUI settings updated successfully.'
        ]);
    }
}
