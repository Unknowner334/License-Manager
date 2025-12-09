<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Config;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $schedule = $this->app->make(Schedule::class);

            $schedule->command('licenses:deactivate-expired');
        }

        Config::set('app.name', getSetting('app_name', config('app.name')));
        Config::set('app.timezone', getSetting('app_timezone', config('app.timezone')));
        Config::set('messages.settings.currency', getSetting('currency', config('messages.settings.currency')));
        Config::set('messages.settings.currency_place', getSetting('currency_place', config('messages.settings.currency_place')));
        Config::set('messages.settings.source_link', getSetting('source_link', config('messages.settings.source_link')));
        Config::set('messages.settings.source', getSetting('source', config('messages.settings.source')));
    }
}
