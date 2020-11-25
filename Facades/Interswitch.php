<?php
namespace Toyosi\Interswitch\Facades;
use Illuminate\Support\Facades\Facade;

class Interswitch extends Facade{
    protected static function getFacadeAccessor(){
        return 'laravel-interswitch';
    }
}