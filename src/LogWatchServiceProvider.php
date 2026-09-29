<?php

namespace Nnaemekanweke\Logwatch;

use Illuminate\Support\ServiceProvider;

class LogWatchServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/logwatch.php', 'logwatch');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/logwatch.php' => config_path('logwatch.php'),
            ], 'logwatch-config');
        }
    }
}
