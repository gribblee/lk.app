<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * Маршруты только для авторизованых пользователей
 */
/**
 * Здесь Post
 */
Route::get('/logout', 'AuthController@logout');

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

    Route::post('show', "AdminController@showUser");
    Route::post('add', "AdminController@addUser");
    Route::post('updates', "AdminController@updateUser");
    Route::post('delete', "AdminController@deleteUser");
    Route::post('active', "AdminController@deleteActive");
});

Route::group(['prefix' => 'category'], function () {
    Route::post('create', "CategoryController@create");
    Route::post('update', "CategoryController@update");
    Route::post('delete', "CategoryController@delete");
});

Route::group(['prefix' => 'disput_status'], function () {
    Route::get('', "DisputTypeController@index");
    Route::post('create', "DisputTypeController@create");
    Route::post('update', "DisputTypeController@update");
    Route::post('delete', "DisputTypeController@delete");
});

Route::group([
    'prefix' => 'company'
], function () {
    Route::put('/create', 'Company\CompanyController@create');
    Route::post('{id}', 'Company\CompanyController@update');
    Route::delete('{id}', 'Company\CompanyController@delete');
    Route::post('{id}/upload', 'Company\CompanyController@upload');
    Route::group([
        'prefix' => '{companyId}/issues'
    ], function() {
        Route::put('create', 'Company\IssuesController@create');
        Route::post('{id}', 'Company\IssuesController@update');
        Route::delete('{id}', 'Company\IssuesController@delete');
    });
});
/**
 * Здесь Get Запросы
 */
Route::get('me/companies', "Company\CompanyController@meCompanies");
Route::get('company/{id}', 'Company\CompanyController@show');
Route::get('companies', "Company\CompanyController@index");
Route::get('companies/{regionId}', "Company\CompanyController@companiesInRegion");

/**
 * Start Ver1.0
 */

Route::get('/managers', "ManagerController@index");

Route::group([
    'prefix' => 'webmaster'
], function () {
    Route::group([
        'prefix' => 'token'
    ], function () {
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
    Route::post('{id}/upload', "DealController@uploadAudio");
    Route::post('{id}/storage/{storage_id}/delete', "DealController@storageDelete");
    Route::post('{id}/status_update', "DealController@statusUpdate");
    //Исправить
    Route::post('{id}/disput/create', 'DisputController@create');
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
    Route::post('pay_bonus', "UserController@payBonus");

    Route::post('update/region', 'UserController@updateRegion');
});
Route::group([
    'prefix' => 'manager'
], function () {
    Route::post('users', "ManagerController@getUsers");
});
Route::group([
    'prefix' => 'bids'
], function () {
    Route::post('', 'BidController@index');
    Route::post('action_update', "BidController@actionUpdate");
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
    Route::get('', "DirectoryController@index");
    Route::group([
        'prefix' => 'direction'
    ], function () {
        Route::put('create', "DirectoryController@directionCreate");
        Route::post('{id}/update', "DirectoryController@directionUpdate");
        Route::post('{id}/delete', "DirectoryController@directionDelete");
    });
    Route::group([
        'prefix' => 'region'
    ], function () {
        Route::get('', "DirectoryController@getRegions");
        Route::put('create', "DirectoryController@regionCreate");
        Route::post('{id}/update', "DirectoryController@regionUpdate");
        Route::post('{id}/delete', "DirectoryController@regionDelete");
    });
    Route::post('status/save', "DirectoryController@statusSave");
});

Route::group([
    'prefix' => 'option'
], function () {
    Route::post('save', "OptionController@updateOrCreate");
});

Route::group(['prefix' => 'insurance'], function () {
    Route::get('', "InsuranceController@index");
    Route::post('/create', "InsuranceController@create");
    Route::post('update', "InsuranceController@update");
    Route::post('{id}/delete', "InsuranceController@delete");
});

Route::group([
    'prefix' => 'admin'
], function () {
    Route::post('statistic', "UserController@statistic");
});

Route::group([], function () {
    Route::post('users', "UserController@index");
    Route::post('user/show', "AdminController@getUser");
    Route::post('user/updates', "AdminController@updateUser");
    Route::post('manager', "AdminController@getManagers");
});


/**
 * End Ver1.0
 */
