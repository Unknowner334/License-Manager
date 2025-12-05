<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\App;
use App\Models\LicenseHistory;

class License extends Model
{
    protected $table = 'licenses';

    protected $fillable = [
        'edit_id',
        'app_id',
        'owner',
        'license',
        'status',
        'max_devices',
        'devices',
        'duration',
        'expire_date',
        'registrar',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [
        'id',
    ];

    protected static function booted()
    {
        static::creating(function ($license) {
            if (empty($license->edit_id)) {
                $license->edit_id = (string) Str::uuid();
            }
        });

        static::deleting(function ($license) {
            $license->histories()->delete();
        });
    }
    
    public function app()
    {
        return $this->belongsTo(App::class, 'app_id', 'app_id');
    }

    public function histories()
    {
        return $this->hasMany(LicenseHistory::class, 'license_id', 'edit_id');
    }
}
