<?php

use App\Models\Product;
use Illuminate\Http\Request;


Route::group(['middleware' => ['web']], function () {
    Route::get('/{affiliateId?}', 'AppController@home');

    Route::post('payment', array(
        'as' => 'payment',
        'uses' => 'PaymentController@stripe',
    ));

});