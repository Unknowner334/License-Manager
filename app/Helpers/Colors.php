<?php

function statusColor($status) {
    if ($status == 'Active') {
        return 'green-600';
    } elseif ($status == 'Inactive') {
        return 'red-600';
    } else {
        return 'yellow-300';
    }
}

function permissionColor($perm) {
    if ($perm == 'Owner') {
        return 'red-600';
    } elseif ($perm == 'Manager') {
        return 'yellow-300';
    } elseif ($perm == 'Reseller') {
        return 'primary';
    } else {
        return 'dark-text';
    }
}