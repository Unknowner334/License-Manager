<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Key;

class App extends Model
{
    protected $table = 'apps';

    protected $fillable = [
        'app_id',
        'name',
        'ppd_basic',
        'ppd_premium',
        'status',
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
        static::creating(function ($app) {
            if (empty($app->app_id)) {
                $app->app_id = (string) \Illuminate\Support\Str::uuid();
            }

            if (empty($app->edit_id)) {
                $app->edit_id = (string) \Illuminate\Support\Str::uuid();
            }
        });

        static::deleting(function ($app) {
            $app->keys()->delete();
        });
    }

    public function keys()
    {
        return $this->hasMany(Key::class, 'app_id', 'app_id');
    }
}
