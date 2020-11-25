<?php
Route::group(['namespace' => 'Toyosi\Interswitch\Http\Controllers'], function(){
    Route::post('interswitch-pay', 'InterswitchController@pay');

    
    Route::get('interswitch-sample-form', function(){
        return view('interswitch::sample-form');
    });
});