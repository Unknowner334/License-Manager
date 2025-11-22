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

            $parts = [];

            if ($diff->y > 0) {
                $parts[] = $diff->y . ' year' . ($diff->y > 1 ? 's' : '');
            }

            if ($diff->m > 0) {
                $parts[] = $diff->m . ' month' . ($diff->m > 1 ? 's' : '');
            }

            if ($diff->d > 0) {
                $parts[] = $diff->d . ' day' . ($diff->d > 1 ? 's' : '');
            }

            if ($diff->h > 0) {
                $parts[] = $diff->h . ' hour' . ($diff->h > 1 ? 's' : '');
            }

            if ($diff->i > 0) {
                $parts[] = $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
            }

            if ($diff->s > 0) {
                $parts[] = $diff->s . ' second' . ($diff->s > 1 ? 's' : '');
            }

            if (empty($parts)) {
                return 'N/A';
            }

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
}
