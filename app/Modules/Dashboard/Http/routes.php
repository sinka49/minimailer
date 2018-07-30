<?php


Route::group( [ 'prefix' => 'dashboard', 'middleware' => 'authDashboard' ], function () {
    Route::get( '/', function () {
        return redirect( 'dashboard/home' );
    } );

    Route::auth();

    Route::get( 'home', 'AppController@home' );

    Route::get( 'sender', 'AppController@sender' );
    Route::get( 'smtp-accounts', 'AppController@smtpAccounts' );

    Route::post( 'smtp-account/load', 'AppController@loadXls' );

    Route::post( 'smtp-account/delete', 'AppController@deleteSmtpAccount' );
    Route::get( 'smtp-account/edit/{smtpAccount?}', 'AppController@editSmtpAccount' );
    Route::post( 'smtp-account/edit/{smtpAccount?}', 'AppController@updateSmtpAccount' );
    Route::get( 'smtp-account/add', 'AppController@newSmtpAccount' );
    Route::post( 'smtp-account/add', 'AppController@addSmtpAccount' );

    Route::get( 'mailing-history', 'AppController@mailingHistory' );
    Route::post( 'mailing-history/delete', 'AppController@deletemailingHistory' );

    Route::group( [ 'prefix' => 'api/v1' ], function () {
        Route::resource( 'smtp-account', 'SmtpAccountController' );
    });

    Route::post( 'load-csv', 'AppController@loadCsv' );
    Route::post( 'send-emails', 'AppController@sendEmails' );

    Route::get( 'trainings', 'AppController@training' );
    Route::get( 'training/{id?}', 'AppController@showTraining' );

    Route::get( 'subscription', 'AppController@subscription' );
    Route::post( 'subscription/cancel', 'PaymentController@subscriptionCancel' );

    Route::post( 'payment', 'PaymentController@stripe' );

    Route::get( 'users', 'AppController@users' );

    Route::get( 'finance', 'AppController@finance' );
    Route::get( 'finance/payout', 'PaymentController@payout' );
    Route::post( 'finance/sendPayout', 'PaymentController@sendPayout' );

    Route::get( 'terms', function(){
        return view("dashboard::app.terms");
    } );
    Route::get( 'policy', function(){
        return view("dashboard::app.policy");
    } );

    Route::get( 'profile', 'ProfileInfoController@index' );
    Route::post( 'profile/update/card', 'ProfileInfoController@setCardNumber');
    Route::post( 'profile/update/ppemail', 'ProfileInfoController@setPayPal');
    Route::post( 'profile/update/name', 'ProfileInfoController@setName');
    Route::post( 'profile/update/password', 'ProfileInfoController@setPassword');

    Route::get( 'support', 'SupportController@index' );

    Route::post( 'support', 'SupportController@getMessage' );

    Route::match( [ 'get', 'post' ], 'accept-affiliate-program', 'AppController@acceptAffiliateProgram' );
    Route::match( [ 'get', 'post' ], 'affiliate-program', 'AppController@affiliateProgram' );
    Route::post( '/affiliate-program/mail', 'AppController@sendEmails');

    // Password reset link request routes...
    Route::get('password/email', 'Auth\PasswordController@getEmail')->name('password.email');;
    Route::post('password/email', 'Auth\PasswordController@postEmail');

// Password reset routes...

    Route::post('password/reset', 'Auth\PasswordController@postReset')->name('password.reset');
    Route::get( 'banners', 'AppController@banners' );
} );