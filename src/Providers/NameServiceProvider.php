<?php

declare(strict_types=1);

namespace Arendach\VodafoneName\Providers;

use Arendach\VodafoneName\Services\CurlRequest;
use Arendach\VodafoneName\Services\GetName;
use Arendach\VodafoneName\Services\Logger;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Arendach\VodafoneName\Name;

class NameServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/vodafone-name.php' => config_path('vodafone-name.php'),
        ], 'vodafone-name');
    }

    public function register(): void
    {
        Config::set('logging.channels.name', [
            'driver' => 'daily',
            'path'   => storage_path('logs/name.log'),
            'level'  => 'debug',
        ]);

        $this->app->singleton(Name::class, Name::class);
        $this->app->singleton(Logger::class, Logger::class);
        $this->app->singleton(CurlRequest::class, CurlRequest::class);
        $this->app->singleton(GetName::class, GetName::class);
    }
}
