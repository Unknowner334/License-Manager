<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\App;
use App\Models\KeyHistory;

class Key extends Model
{
    protected $table = 'key_codes';

    protected $fillable = [
        'app_id',
        'owner',
        'key',
        'status',
        'max_devices',
        'duration',
        'expire_date',
        'rank',
        'created_by',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [
        'edit_id',
        'id',
    ];

    protected static function booted()
    {
        static::creating(function ($key) {
            if (empty($key->edit_id)) {
                $key->edit_id = (string) Str::uuid();
            }
        });

        static::deleting(function ($key) {
            $key->histories()->delete();
        });
    }
    
    public function app()
    {
        return $this->belongsTo(App::class, 'app_id', 'app_id');
    }

    public function histories()
    {
        return $this->hasMany(KeyHistory::class, 'key_id', 'edit_id');
    }
}
