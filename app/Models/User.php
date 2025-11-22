<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use App\Models\Reff;
use App\Models\UserHistory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'user_id',
        'name',
        'username',
        'password',
        'permissions',
        'status',
        'reff',
        'created_by',
    ];

    protected $hidden = [
        'id',
        'password',
        'remember_token',
        'created_at',
        'updated_at',
    ];

    protected static function booted()
    {
        static::creating(function ($user) {
            if (empty($user->user_id)) {
                $user->user_id = (string) Str::uuid();
            }
        });

        static::deleting(function ($user) {
            $user->histories()->delete();
        });
    }

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function histories() {
        return $this->hasMany(UserHistory::class, 'user_id', 'user_id');
    }

    public function referrable() {
        return $this->belongsTo(Reff::class, 'reff', 'edit_id');
    }
}
