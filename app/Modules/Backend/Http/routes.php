<?php

Route::group( [ 'prefix' => 'opera', 'middleware' => 'authBackend' ], function () {
    Route::get( '/', function () {
        return redirect( 'opera/home' );
    } );

    Route::auth();

    Route::get( 'home', 'AppController@home' );
    Route::post( 'home', 'AppController@updateHome' );

    Route::get( 'sender', 'AppController@sender' );
    Route::post( 'load-csv', 'AppController@loadCsv' );
    Route::post( 'send-emails', 'AppController@sendEmails' );

    Route::get( 'trainings', 'AppController@training' );
    Route::get( 'training/{id?}', 'AppController@editTraining' );
    Route::get( 'training/remove/{id?}', 'AppController@removeTraining' );
    Route::post( 'training/{id?}', 'AppController@updateTraining' );



    Route::get( 'subscriptions', 'AppController@subscription' );
    Route::get( 'subscription/{id?}', 'AppController@editSubscription' );
    Route::post( 'subscription/{id?}', 'AppController@updateSubscription' );
    Route::get( 'subscription/remove/{id?}', 'AppController@removeSubscription' );

    Route::get( 'users', 'AppController@users' );
    Route::post( 'users', 'AppController@usersAction' );

    Route::get( 'finance', 'AppController@finance' );
    Route::get( 'finance/paid/{id?}', 'AppController@financePaid' );
    Route::get( 'finance/remove', 'AppController@financePaidRemove' );

    Route::get( 'affiliate-program', 'AppController@affiliateProgram' );

    Route::get( 'settings', 'AppController@settings' );
    Route::post( 'settings', 'AppController@setSettings' );
    // Password reset link request routes...
    Route::get('password/email', 'Auth\PasswordController@getEmail')->name('password.email');;
    Route::post('password/email', 'Auth\PasswordController@postEmail');

// Password reset routes...

    Route::post('password/reset', 'Auth\PasswordController@postReset')->name('password.reset');


    Route::get( 'main', 'MainController@home' );
    Route::post( 'main', 'MainController@update' );
    Route::get( 'seo', 'SeoController@home' );
    Route::post( 'seo', 'SeoController@update' );
    Route::get( 'banners', 'BannerController@home' );
    Route::post( 'banners', 'BannerController@add' );
    Route::get( 'banners/remove/{id}', 'BannerController@remove' );
} );