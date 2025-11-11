<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\App;
use DateTime;

class AppController extends Controller
{
    static function timeElapsed($dateString) {
        if (empty($dateString)) {
            return 'N/A';
        }

        try {
            $date = new DateTime($dateString);
            $now = new DateTime();
            $diff = $now->diff($date);

            $years = $diff->y;
            $months = $diff->m;
            $days = $diff->days;

            if ($years >= 1) {
                return sprintf("%d year%s ago", $years, $years > 1 ? 's' : '');
            }

            if ($months >= 1) {
                return sprintf("%d month%s ago", $months, $months > 1 ? 's' : '');
            }

            return sprintf("%d day%s ago", $days, $days > 1 ? 's' : '');
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    public function AppListView() {
        $apps = App::paginate(10);

        return view('App.list', compact('apps'));
    }
}