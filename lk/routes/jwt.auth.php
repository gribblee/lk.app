<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * Маршруты только для авторизованых пользователей
 */

/**
 * Здесь Post
 */
Route::post('/logout', 'AuthController@logout');

Route::group([
    'prefix' => 'demo'
], function () {
    Route::post('create', 'DemoController@create');
    Route::post('requisite', 'DemoController@getRequisite');
    Route::post('requisite/create', 'DemoController@requisiteCreate');
});

Route::group([
    'prefix' => 'user'
], function () {
    Route::get('/updated', 'UserController@updated');
    Route::get('/me', 'UserController@meUser');
});


/**
 * Здесь Get Запросы
 */


/**
 * Start Ver1.0
 */
Route::group([
    'prefix' => 'webmaster'
], function() {
    Route::group([
        'prefix' => 'token'
    ], function() {
        Route::get('load', "WebmasterController@tokenLoad");
        Route::post('create', "WebmasterController@tokenCreate");
        Route::post("delete", "WebmasterController@tokenDelete");
    });
});

Route::group([
    'prefix' => 'disput'
], function () {
    Route::post('', "DisputController@index");
    Route::post('info', "DisputController@info");
    Route::post('{id}/close', "DisputController@close");
});

Route::group([
    'prefix' => 'payment'
], function () {

    Route::group([
        'prefix' => 'card'
    ], function () {
        Route::post('create', "PaymentController@cardCreate");
    });

    Route::group([
        'prefix' => 'requisite'
    ], function () {
        Route::post('create', "PaymentController@requisiteCreate");
        Route::post('update', "PaymentController@requisiteUpdate");
    });

    Route::group([
        'prefix' => 'credit'
    ], function () {
        Route::post('create', "PaymentController@creditCreate");
    });
    /**
     * Start Ver 1.0
     */
    Route::get('history', "PaymentController@generalHistory");
    Route::group([
        'prefix' => 'requisites'
    ], function () {
        Route::post('', "PaymentController@bills");
        Route::post('update', "PaymentController@requisitePayment");
    });
    /**
     * End Ver 1.0
     */
});

Route::group(['prefix' => 'deals'], function () {
    Route::post('', "DealController@index");

    /**
     * Distributed
     */
    Route::get('/distributed', "DistributedController@index");
    Route::post('/distributed/{id}', "DistributedController@deal");
    Route::post('/distributed/{id}/status', "DistributedController@status");
    Route::post('/distributed/{id}/update', "DistributedController@update");
    Route::post('/distributed/{id}/delete', "DistributedController@delete");
});

Route::group([
    'prefix' => 'deal'
], function () {
    Route::post('{id}', "DealController@show");
});

Route::get('/requisites', "RequisiteController@index");
Route::get('/categories', "CategoryController@index");

Route::group([
    'prefix' => 'requisite'
], function () {
    Route::get('/create', "RequisiteController@create");
    Route::post('/{id}/update', "RequisiteController@update");
    Route::get('/{id}/delete', "RequisiteController@delete");
});
Route::group([
    'prefix' => 'user'
], function () {
    Route::post('update_type', 'UserController@updateCategory');
    Route::post('update', 'UserController@update');
    Route::post('balance/history', 'UserController@history');
});
Route::group([
    'prefix' => 'bids'
], function () {
    Route::post('', 'BidController@index');
});

Route::group([
    'prefix' => 'bid',
], function () {
    Route::post('create', 'BidController@create');
    Route::post('{id}', 'BidController@show');
    Route::post('{id}/update', 'BidController@update');
    Route::post('{id}/launch', 'BidController@launch');
    Route::post('{id}/buy_insurance', 'BidController@buyInsurance');
    Route::post('{id}/delete', 'BidController@delete');
    Route::post('{id}/update_region', 'BidController@updateRegion');
});

Route::group(['prefix' => 'directory'], function () {
    Route::get('/', "DirectoryController@index");
    // Route::post('/direction/save', "Directory\Controllers\DirectoryApi@direction_save");
    // Route::post('/regions/save', "Directory\Controllers\DirectoryApi@regions_save");
    // Route::post('/status/save', "Directory\Controllers\DirectoryApi@status_save");
    // Route::post('/options_save', "Directory\Controllers\DirectoryApi@options_save");
});

Route::group(['prefix' => 'insurance'], function () {
    Route::get('', "InsuranceController@index");
    Route::post('/create', "InsuranceController@create");
    Route::post('update', "InsuranceController@update");
    Route::post('{id}/delete', "InsuranceController@delete");
});

/**
 * End Ver1.0
 */
