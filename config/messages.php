<?php

return [
    'success' => [
        // ? General
        'logged_in'  => '<b>Logged In</b> Successfully.',
        'logged_out' => '<b>Logged Out</b> Successfully.',
        'register_success' => '<b>Registered Successfully</b>, You can now login.',
        // ? Setting
        'created' => ':flag <b>Successfully Created</b>.',
        'updated' => ':flag <b>Successfully Updated</b>.',
        'deleted' => ':flag <b>Successfully Deleted</b>.',
        'reseted' => ':flag <b>Successfully Reseted</b>.',
    ],

    'error' => [
        // ? General
        'wrong_creds' => '<b>Invalid</b> Credentials.',
        'register_fail' => '<b>Register Failed</b>, Please Try Again Later.',
        'validation' => '<b>Something Went Wrong</b>, :info.',
    ],

    'settings' => [
        'currency' => env('MESSAGES_CURRENCY', '$'),
        'currency_place' => env('MESSAGES_CURRENCY_PLACE', '0'),
        'source_link' => env('SOURCE_LINK', '#'),
        'source' => env('SOURCE', 'DISCORD'),
    ],
];