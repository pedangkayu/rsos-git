<?php

namespace App\Providers;

use App\Classes\LogUser\LogUser;

use Illuminate\Support\ServiceProvider;

class LogUserServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Loguser', function(){
            return new LogUser;
        });
    }
}
