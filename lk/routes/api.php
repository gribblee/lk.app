<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * Здесь Get Запросы
 */

use App\Helpers\Dreamkas\Api as Dreamkas;
use App\Helpers\Dreamkas\CustomerAttributes;
use App\Helpers\Dreamkas\exceptions\ValidationException;
use App\Helpers\Dreamkas\Payment as DreamkasPayment;
use App\Helpers\Dreamkas\Position;
use App\Helpers\Dreamkas\Receipt;
use App\Helpers\Dreamkas\TaxMode;
use GuzzleHttp\Exception\ClientException;

Route::get('/receipt/test', function () {
    $dreamkasApi = new Dreamkas('2aabc4ab-4844-4378-ab37-9ee60bde7064', 129174, Dreamkas::MODE_MOCK);
    $receipt = new Receipt();
    $receipt->taxMode = TaxMode::MODE_PATENT;
    $receipt->positions[] = new Position([
        'name' => 'лк - тест',
        'quantity' => 1,
        'price' => 1, // цена в копейках за 1 шт. или 1 грамм
    ]);
    $receipt->payments[] = new DreamkasPayment([
        'sum' => 1, // стоимость оплаты по чеку
    ]);
    $receipt->attributes = new CustomerAttributes([
        'email' => 'monsidev@gmail.com', // почта покупателя
        'phone' => '+7(938) 305-07-24', // телефон покупателя
    ]);
    $receipt->calculateSum();
    // А можно завалидировать чек
    $receipt->validate();
    $response = [];
    try {
        $response = $dreamkasApi->postReceipt($receipt);
    } catch (ValidationException $e) {
        return response()->json([
            'errors_message' => $e->getMessage()
        ], 400);
    } catch (ClientException $e) {
        return response($e->getResponse()->getBody());
        // Это исключение кидается, когда при передачи чека в Дрикас произошла ошибка. Лучше отправить чек ещё раз
        // Если будут дубли - потом отменяйте через $receipt->type = Receipt::TYPE_REFUND;
    }
    return response()->json([
        'response' => $response,
    ]);
});

//EMPTY
Route::get('/directory/categories', function () {
    return \DB::table('categories')->get();
});

/**
 * Здесь Post запросы
 */
Route::group([
    'middleware' => 'throttle:20,5'
], function () {
    Route::post('/register', 'AuthController@register');
    Route::post('/login', 'AuthController@login');
    Route::post('/sign/login', 'AuthController@authorizeLogin');
    Route::post('/sign/register', 'AuthController@registerLogin');
    Route::post('/authorize', 'AuthController@authorizeToken');
});


Route::group([
    'prefix' => 'runon'
], function () {
    Route::match(['get', 'post'], '{hash}/append', "RunOnController@append");
});

Route::group([
    'prefix' => 'app'
], function () {
    Route::match(['get', 'post'], '{hash}/push', "RunOnController@append");
});


// Route::group([
//     'prefix' => 'app',
//     'middleware' => ['app.auth']
// ], function () {
//     Route::post('{hash}/push', "AppController@push");
// });


/**
 * Миграции
 */
// Route::get('migrate', function () {
//     dd(\Artisan::call('migrate'));
// });

// Route::get('migrate/fresh', function () {
//     dd(\Artisan::call('migrate:fresh'));
// });

// Route::get('migrate/seed', function () {
//     dd(\Artisan::call('migrate --seed'));
// });

// Route::get('migrate/fresh/seed', function () {
//     dd(\Artisan::call('migrate:fresh --seed'));
// });

/**
 * Ver 1.0
 */
Route::post('/payment', "PaymentController@payment");
Route::get('/deal/{id}/storage/{storage_id}', "DealController@storage"); //В далнейшем поменять на безопаснее
Route::get('/payment/{id}/doc', "PaymentController@paymentDocument");
