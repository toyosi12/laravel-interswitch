<?php
Route::group(['namespace' => 'Toyosi\Interswitch\Http\Controllers'], function(){
    Route::post('interswitch-pay', 'InterswitchController@pay');

    /**
     * The redirect url after transaction attempt.
     * Do not change this
     */
    Route::post('interswitch-redirect', 'InterswitchController@redirect');
    
    Route::get('interswitch-sample-form', function(){
        return view('interswitch::sample-form');
    });
});