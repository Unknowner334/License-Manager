<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeyHistory extends Model
{
    protected $table = 'app_history';

    protected $fillable = [
        'app_id',
        'type',
        'created_at',
        'updated_at',
    ];


    protected $hidden = [
        'id',
    ];
}
