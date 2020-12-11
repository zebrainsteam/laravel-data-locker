<?php

declare(strict_types=1);

namespace Zebrains\LaravelDataLocker;

use Illuminate\Support\ServiceProvider;
use Prozorov\DataVerification\Configuration;
use Prozorov\DataVerification\Locker;

class OtpServiceProvider extends ServiceProvider
{
    protected $commands = [
        // 'Zebrains\LaravelDataLocker\RemoveExpiredCodesCommand',
    ];

    /**
     * Bootstrap the application services...
     *
     * @return void
     */
    public function boot()
    {
        $configPath = $this->configPath();

        $this->publishes([
            "${configPath}/otp.php" => $this->publishPath('otp.php'),
        ], 'config');

        $this->loadMigrationsFrom(__DIR__ . '/../migrations');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);

        $this->app->singleton('otp', static function ($app) {
            return new Locker(new Configuration($app, config('otp')));
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['otp'];
    }

    protected function configPath()
    {
        return __DIR__ . '/../config';
    }
    
    protected function publishPath($configFile)
    {
        if (function_exists('config_path')) {
            return config_path($configFile);
        } else {
            return base_path('config/' . $configFile);
        }
    }
}
