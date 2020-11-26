<?php
namespace Toyosi\Interswitch;

use Illuminate\Support\ServiceProvider;


class InterswitchServiceProvider extends ServiceProvider{
    public function boot(){
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/views', 'interswitch');
        $this->mergeConfigFrom(__DIR__ . '/config/interswitch.php', 'interswitch');
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }


    public function register(){
        $this->app->bind('laravel-interswitch', function(){
            return new Interswitch;
        });
    }

    public function provides(){
        return ['laravel-interswitch'];
    }
}