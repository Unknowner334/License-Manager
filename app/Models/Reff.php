<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\User;

class Reff extends Model
{
    protected $table = 'referrable_codes';

    protected $fillable = [
        'edit_id',
        'code',
        'status',
        'created_by',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
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
