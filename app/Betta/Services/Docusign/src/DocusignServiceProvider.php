<?php

namespace Betta\Docusign;

use Illuminate\Support\ServiceProvider;

class DocusignServiceProvider extends ServiceProvider
{
    /**
     * Only load it when requested
     *
     * @var boolean
     */
    protected $defer = true;


    /**
     * Boot the service Provider
     *
     * @return Void
     */
    public function boot()
    {
        # We need to auto-publish this config file, but we are not there yet
        $config = [ __DIR__.'/config/config.php' => app_path('config/docusign.php') ];
    }


    /**
     * Register Service Provider
     *
     * @return Docusign
     */
    public function register()
    {
        # We will share the Docusign accross the app,
        # without creating new instances each time we need it
        $this->app->singleton('Betta\Docusign\DocusignClient', function ( $app, $arguments ){
            # Get Config
            $arguments = $app['config']['docusign'];
            # resolve
            return new DocusignClient( $arguments );
        });
    }


    /**
     * WHat are the services provided?
     *
     * @return Array
     */
    public function provides()
    {
        return [DocusignClient::class];
    }
}
