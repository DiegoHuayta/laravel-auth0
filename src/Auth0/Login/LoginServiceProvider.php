<?php namespace Auth0\Login;

use Illuminate\Support\ServiceProvider;

class LoginServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // $this->package('auth0/login','auth0');
        $this->publishes([
            __DIR__.'/../../config/config.php' => config_path('auth0.php'),
            __DIR__.'/../../config/api.php' => config_path('auth0_api.php'),
        ], 'config');
        \Auth::extend('auth0', function($app) {
            $provider =  new Auth0UserProvider();

            return new \Illuminate\Auth\Guard($provider, $app['session.store']);
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $configPath = __DIR__ . '/../../config/config.php';
        $configPathApi = __DIR__ . '/../../config/api.php';
        $this->mergeConfigFrom($configPath, 'auth0');
        $this->mergeConfigFrom($configPathApi, 'auth0');
        // Bind the auth0 name to a singleton instance of the Auth0 Service
        $this->app->singleton("auth0", function() {
            return new Auth0Service();
        });


        // When Laravel logs out, logout the auth0 SDK trough the service
        \Event::listen('auth.logout', function() {
            \App::make("auth0")->logout();
        });

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

}
