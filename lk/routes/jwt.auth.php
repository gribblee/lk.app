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
], function() {
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
    Route::post('{id}/rate_fix_regions', 'BidController@rateFixRegions');
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
