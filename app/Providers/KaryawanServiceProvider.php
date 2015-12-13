<?php

namespace App\Providers;

use App\Classes\Karyawan\Karyawan;

use Illuminate\Support\ServiceProvider;

class KaryawanServiceProvider extends ServiceProvider
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
    public function register(){
        $this->app->bind('Me', function(){
            return new Karyawan;
        });
    }
}
