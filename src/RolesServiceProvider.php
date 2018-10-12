<?php

namespace JoshThackeray\Roles;

use JoshThackeray\Roles\Commands\SyncRoles;
use Illuminate\Support\ServiceProvider;

class RolesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //Loading the package migrations
        $this->loadMigrationsFrom(__DIR__.'/Migrations');

        //Publishing to packages config
        $this->publishes([
            __DIR__.'/Config/roles.php' => config_path('identify.php'),
        ]);
        $this->mergeConfigFrom(
            __DIR__.'/Config/roles.php', 'identify.roles'
        );

        //Assigning the commands to the Kernel
        if ($this->app->runningInConsole()) {
            $this->commands([
                SyncRoles::class,
            ]);
        }

    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
