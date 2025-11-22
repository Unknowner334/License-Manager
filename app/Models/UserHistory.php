<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class UserHistory extends Model
{
    protected $table = 'users_history';

    protected $fillable = [
        'user_id',
        'username',
        'status',
        'type',
        'ip_address',
        'user_agent',
        'payload',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [
        'id',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
