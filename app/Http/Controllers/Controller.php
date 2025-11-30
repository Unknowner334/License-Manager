<?php

namespace App\Http\Controllers;

use DateTime;
use app\Models\User;

abstract class Controller
{
    static function statusColor($status) {
        if ($status == 'Active') {
            return 'success';
        } elseif ($status == 'Inactive') {
            return 'danger';
        } else {
            return 'warning';
        }
    }

    static function permissionColor($perm) {
        if ($perm == 'Owner') {
            return 'danger';
        } elseif ($perm == 'Admin') {
            return 'warning';
        } else {
            return 'dark';
        }
    }

    static function timeElapsed($dateString) {
        if (empty($dateString)) {
            return 'N/A';
        }

        try {
            $date = new DateTime($dateString);
            $now = new DateTime();
            $diff = $now->diff($date);

            $units = [
                'y' => 'year',
                'm' => 'month',
                'd' => 'day',
                'h' => 'hour',
                'i' => 'minute',
                's' => 'second'
            ];

            $parts = [];
            foreach ($units as $key => $label) {
                $value = $diff->$key;
                if ($value > 0) {
                    $parts[] = $value . ' ' . $label . ($value > 1 ? 's' : '');
                }
            }

            if (empty($parts)) {
                return 'N/A';
            }

            $parts = array_slice($parts, 0, 1);

            return implode(', ', $parts) . ' ago';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }


    static function censorText($text, $visibleChars = 6, $asterisks = 2) {
        $visible = substr($text, 0, $visibleChars);
        $hidden = str_repeat('*', $asterisks);
        return $visible . $hidden;
    }

    static function randomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }
        return $randomString;
    }

    static function userUsername($user_id) {
        $user = User::where('user_id', $user_id)->first();
        return $user?->username ?? 'N/A';
    }
}
