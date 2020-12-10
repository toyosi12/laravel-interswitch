<?php
Route::group(['namespace' => 'Toyosi\Interswitch\Http\Controllers'], function(){
    Route::post('interswitch-pay', 'InterswitchController@pay');

    /**
     * The redirect url after transaction attempt.
     * Do not change this
     */
    Route::post('interswitch-redirect', 'InterswitchController@redirect');

    /**
     * Log of all transactions. Implement route guards as necessary
     */
    Route::get('interswitch-logs', 'InterswitchController@logs');


    /**
     * Requery incomplete transactions
     */
    Route::post('interswitch-requery', 'InterswitchController@requeryTransaction');

    




});