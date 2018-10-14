<?php

namespace JoshThackeray\Roles;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
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

        //Adding an if directive to blade to check for whether a user has been assigned a role
        Blade::if('assigned', function ($role, $guard = null) {

            $user = null;
            if(is_null($guard)) {
                //Check if the default guard has an active session.
                if(!Auth::check())
                    return false;

                //Getting the user from this default guard.
                $user = Auth::user();
            } else {
                //Check if the specified guard has an active session.
                if(!Auth::guard($guard)->check())
                    return false;

                //Getting the user from the specified guard.
                $user = Auth::guard($guard)->user();
            }

            //Ensure the user model used extends the Assignable trait
            if(method_exists($user, 'roles'))
                //Then check if the user has this role
                return $user->hasRole($role);

            //Else return false;
            return false;
        });

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
