<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebuiController extends Controller
{
    public function webui_settings() {
        return view('Settings.webui_settings');
    }
}
