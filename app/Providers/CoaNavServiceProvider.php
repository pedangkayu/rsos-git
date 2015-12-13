<?php

namespace App\Providers;

use App\Classes\TreeCoa\CoaNav;

use Illuminate\Support\ServiceProvider;

class CoaNavServiceProvider extends ServiceProvider
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
         $this->app->bind('Coa', function(){
            return new CoaNav;
        });
    }
}
