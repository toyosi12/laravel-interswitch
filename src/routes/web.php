<?php
Route::group([
    'namespace' => 'Toyosi\Interswitch\Http\Controllers', 
    
    /**
     * Allow package have access to session
     */
    'middleware' => '\Illuminate\Session\Middleware\StartSession::class'], 
    function(){
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
    
    Route::get('interswitch-sample-form', function(){
        return view('interswitch::sample-form');
    });

});