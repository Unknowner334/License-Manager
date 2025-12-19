<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use App\Models\Reff;
use App\Models\UserHistory;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'user_id',
        'name',
        'username',
        'password',
        'role',
        'status',
        'saldo',
        'reff',
        'registrar',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [
        'id',
        'remember_token',
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

    public function deductSaldo(int $amount): bool {
        if ($this->role === 'Owner' || $this->saldo >= 2000000000) return true;

        $updated = $this->where('id', $this->id)->where('saldo', '>=', $amount)->decrement('saldo', $amount);

        return (bool)$updated;
    }
}
