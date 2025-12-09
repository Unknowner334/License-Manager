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
        foreach ($request->validated() as $key => $value) {
            setSetting($key, $value);
        }

        return response()->json([
            'status' => 0,
            'message' => '<b>WebUI</b> Settings <b>Successfully</b> Updated.'
        ]);
    }
}
