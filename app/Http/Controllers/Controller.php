<?php

namespace App\Http\Controllers;

use DateTime;

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

            $years = $diff->y;
            $months = $diff->m;
            $days = $diff->days;

            if ($years >= 1) {
                return sprintf("%d year%s ago", $years, $years > 1 ? 's' : '');
            }

            if ($months >= 1) {
                return sprintf("%d month%s ago", $months, $months > 1 ? 's' : '');
            }

            if ($days == 1) {
                return sprintf('Yesterday');
            }

            if ($days == 0) {
                return sprintf('Today');
            }

            return sprintf("%d day%s ago", $days, $days > 1 ? 's' : '');
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
}
