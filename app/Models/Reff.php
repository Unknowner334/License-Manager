<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\User;

class Reff extends Model
{
    protected $table = 'referrable_codes';

    protected $fillable = [
        'created_at',
        'updated_at',
        'code',
        'status',
        'created_by',
    ];

    protected $hidden = [
        'edit_id',
        'id',
    ];

    protected static function booted()
    {
        static::creating(function ($reff) {
            if (empty($reff->edit_id)) {
                $reff->edit_id = (string) Str::uuid();
            }
        });

        static::deleting(function ($reff) {
            $reff->users()->delete();
        });
    }

    public function users() {
        return $this->hasMany(User::class, 'reff', 'edit_id');
    }
}
