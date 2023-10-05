<?php

namespace twid\logger;

use Illuminate\Support\ServiceProvider;

/**
 * TwidLoggerServiceProvider
 *
 * The service provider for the Twid Logger package.
 *
 * @package twid\logger
 */
class TwidLoggerServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * This method is called when the service provider is registered within the application.
     *
     * @return void
     */
    public function register()
    {
        /**
         * Bind 'twid.logger' to the application container.
         *
         * This binding allows resolving 'twid.logger' to an instance of the Logger class.
         *
         * @param \Illuminate\Contracts\Foundation\Application $app The application instance.
         * @return \twid\logger\Logger An instance of the Logger class.
         */
        $this->app->bind('twid.logger', function ($app) {
            return new Logger();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * This method is called after all other service providers have been registered.
     *
     * @return void
     */ 
    public function boot()
    {
        /**
         * Publish configuration file.
         *
         * This method publishes the 'publish.php' configuration file to the 'config' directory.
         * The published file is named 'logging.php'.
         *
         * Usage: php artisan vendor:publish --tag=config
         *
         * @param array $paths The paths to publish.
         * @param string $group The publish group.
         * @return void
         */
        $this->publishes([__DIR__ . '/config/publish.php' => dirname(__DIR__, 4) . '/config/logging.php'], 'config');
    }
}
