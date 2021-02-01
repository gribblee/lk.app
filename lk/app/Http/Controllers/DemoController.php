<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Bid;
use App\Models\Direction;
use App\Models\DemoBid;
use App\Models\Requisite;
use App\Models\Payment;
use App\Models\User;
use App\Models\Region;

use App\Helpers\stdObject;
use App\Helpers\HelperPayment;
use App\Helpers\Tinkoff;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

use PDF;

class DemoController extends Controller
{
    protected $Response;

    function __construct()
    {
        $this->Response = new stdObject([
            'success' => false,
            'message' => '',
            'error' => '',
        ]);
    }

    public function create(Request $request)
    {
        $this->createBid($request);
        $paySum = 0;
        switch ($request->tariffId) {
            case "1001":
                $paySum = 5000;
                break;
            case "1002":
                $paySum = 10000;
                break;
            case "1003":
                $paySum = 25000;
                break;
        }
        switch ($request->target) {
            case 0:
                $this->paymentCard($request, $paySum);
                break;
            case 1:
                $this->paymentRequisite($request->user(), $request->requisiteId, $paySum);
                break;
            case 2:
                $this->paymentCredit($request, $paySum);
                break;
        }
        User::where('id', $request->user()->id)->update([
            'is_demo' => true
        ]);
        return response()->json($this->Response, 200);
    }

    public function requisiteCreate(Request $request)
    {
        if ($request->has('requisiteData')) {
            $validator = $this->validator($request->requisiteData);

            if (!$validator->fails()) {
                $this->Response->success = true;

                $Requisite = Requisite::create([
                    'name' => $request->requisiteData['name'],
                    'ogrn' => $request->requisiteData['ogrn'],
                    'inn' => $request->requisiteData['inn'],
                    'kpp' => $request->requisiteData['kpp'],
                    'bik' => $request->requisiteData['bik'],
                    'bank' => $request->requisiteData['bank'],
                    'ksch' => $request->requisiteData['ksch'],
                    'rsch' => $request->requisiteData['rsch'],
                    'jour_address' => $request->requisiteData['jour_address'],
                    'poste_address' => $request->requisiteData['poste_address'],
                    'director' => $request->requisiteData['director'],
                    'user_id' => $request->user()->id,
                ]);
                $this->Response->requisiteId = $Requisite->id;
            } else {
                $this->Response->success = false;
                $this->Response->error = $validator->errors();
            }
        } else {
            $this->Response->error = 'Нет данных';
        }
        return response()->json($this->Response, 200);
    }

    public function getRequisite(Request $request)
    {
        $requisiteData = Requisite::findOrFail($request->id);
        $this->Response->data = $requisiteData;
        return response()->json($this->Response, 200);
    }
    protected function paymentRequisite($user, int $requisiteId, int $paySum)
    {
        $requis = Requisite::where('user_id', $user->id)->find($requisiteId);
        if ($requis) {
            $paymentData = Payment::create([
                'type' => HelperPayment::TYPE_RQ,
                'status' => HelperPayment::RQ_STATUS_CREATE,
                'requisite_id' => $requis->id,
                'user_id' => $user->id,
                'paysum' => $paySum,
                'before_balance' => $user->balance,
                'after_balance' => $user->balance
            ]);

            /**
             *  BEGIN
             */
            $pdfPath = '/payment/pdf/' . date("dmY", strtotime($paymentData->created_at)) . '-' . str_pad($paymentData->id . $paymentData->requisites_id, 6, '000000', STR_PAD_LEFT) . '.pdf';

            $pdf = PDF::loadView('pdf.contract', [
                'payment' => $paymentData,
                'requisite' => $requis,
                'title' => date("dmY", strtotime($paymentData->created_at)) . '-' . str_pad($paymentData->id . $paymentData->requisites_id, 6, '000000', STR_PAD_LEFT),
                'bill' => str_pad($paymentData->id . $paymentData->requisites_id, 6, '000000', STR_PAD_LEFT),
                'sum_to_string' => HelperPayment::number2string($paySum)
            ]);
            $pdf->save(Storage::disk('public')->path($pdfPath));
            /**
             * END
             */
            $paymentData['bill'] = date("dmY") . '-' . str_pad($paymentData->id . $requis->id, 6, '000000', STR_PAD_LEFT);
            $paymentData['url'] = "/api/payment/{$paymentData->id}/doc";
            /**
             * Отправка на почту 
             */
            Mail::send([], [], function ($message) use ($user, $requisiteId, $pdfPath, $paymentData, $paySum) {
                $message->from('system@b2l.online', 'Leadz.Monster');
                $message->subject('Счёт на пополнение в сервисе Leadz.Monster');
                $message->to($user->email)->cc($user->email);
                $message->attach(Storage::disk('public')->path($pdfPath));
                $message->setBody('<h1>Здравствуйте</h1><br/><p>Вам выставлен счёт № ' . date("dmY", strtotime($paymentData->created_at)) . '-' . str_pad($paymentData->id . $paymentData->requisiteId, 6, '000000', STR_PAD_LEFT) . ' на сумму ' . $paySum . ' ₽ в сервисе Leadz.Monster</p>', 'text/html');
            });

            $this->Response->success = true;
            $this->Response->message = 'Успешно';
            $this->Response->data = $paymentData;
        } else {
            $this->Response->success = false;
            $this->Response->message = 'Не найдены такие реквизиты';
        }
    }

    protected function paymentCard(Request $request, int $paySum)
    {
        $payment = Payment::create([
            'type' => HelperPayment::TYPE_CD,
            'status' => HelperPayment::CD_STATUS_CREATE,
            'requisite_id' => null,
            'user_id' => $request->user()->id,
            'paysum' => $paySum,
            'before_balance' => $request->user()->balance,
            'after_balance' => $request->user()->balance
        ]);
        $tinkoff = new Tinkoff(config('tinkoff.url'), config('tinkoff.terminalKey'), config('tinkoff.secretKey'), config('tinkoff.credit_url'));
        $paymentTinkoff = [
            'OrderId'       => $payment->id,
            'Amount'        => $paySum,
            'Language'      => config('tinkoff.lang'),
            'Description'   => config('tinkoff.description'),
            'Email'         => $request->user()->email,
            'Phone'         => $request->user()->phone,
            'Name'          => $request->user()->name,
            'Taxation'      => config('tinkoff.taxation')
        ];
        $tinkoffSettings[] = [
            'Name'  => 'Пополнение личного счета',
            'Price' => $paySum,
            'NDS'   => config('tinkoff.nds'),
        ];
        $paymentURL = $tinkoff->paymentURL($paymentTinkoff, $tinkoffSettings);

        $this->Response->success = true;
        $this->Response->payment_url = $paymentURL;
        $this->Response->status = 'card';

        if (!$paymentURL) {
            $this->Response->success = false;
            $this->Response->message = 'Ошибка не возможно произвести оплату';
        }
    }

    protected function paymentCredit(Request $request, int $paySum)
    {
        $payment = Payment::create([
            'type' => HelperPayment::TYPE_CT,
            'status' => HelperPayment::CT_STATUS_CREATE,
            'requisite_id' => null,
            'user_id' => $request->user()->id,
            'paysum' => $paySum,
            'before_balance' => $request->user()->balance,
            'after_balance' => $request->user()->balance,
        ]);

        $tinkoff = new Tinkoff(config('tinkoff.url'), config('tinkoff.terminalKey'), config('tinkoff.secretKey'), config('tinkoff.credit_url'));

        /**
         * Разбивка на ФИО
         */
        $fio = [];
        $fioExplode = explode(" ", $request->user()->name);
        $fio['lastName'] = $fioExplode[0] ?? '';
        $fio['firstName'] = $fioExplode[1] ?? '';
        $fio['middleName'] = $fioExplode[2] ?? '';

        $paymentTinkoff = [
            'ShopId'     => config('tinkoff.shop_id'),
            'ShowcaseId' => config('tinkoff.showcase_id'),
            'Sum'        => $paySum,
            'OrderNumber' => $payment->id,
            'Values'     => [
                'contact' => [
                    'fio' => $fio,
                    'mobilePhone' => preg_replace("/^(\+?(7|8))/", "", $request->user()->phone),
                    'email' => $request->user()->email
                ]
            ]
        ];
        $tinkoffSettings[] = [
            'Name'  => 'Оформление рассрочки на сервисе Leadz.Monster',
            'Quantity' => 1,
            'Price' => $paySum,
            'VendorCode' => 'L-' . $payment->id . '-' . date("dmy-his"),
            'Category'   => 'Leadz.Monster - Лиды',
        ];
        $paymentResponse = $tinkoff->creditCreate($paymentTinkoff, $tinkoffSettings);
        if (!$paymentResponse) {
            $this->Response->success = false;
            $this->Response->message = $tinkoff->error;
        }
        $payment->update([
            'tcv_id' => $paymentResponse->id
        ]);

        $this->Response->success = true;
        $this->Response->payment_url = $paymentResponse->link;
        $this->Response->status = 'credit';
    }

    protected function createBid(Request $request)
    {

        $qOnly = new stdObject($request->only(['allRegion', 'regions', 'target', 'directions']));
        foreach ($qOnly->directions as $dir) {
            $QDir = Direction::find($dir);

            $bData = new stdObject([
                'direction_id' => $QDir->id,
                'category_id' => $request->user()->category_id,
                'regions' => $qOnly->allRegion ? [] : Region::whereIn('id', $qOnly->regions)->get(),
                'user_id' => $request->user()->id,
                'consumption' => $QDir->cost_price + ($QDir->cost_price * ($QDir->extra / 100) + ($QDir->cost_price * 0.05)),
                'is_launch' => false,
                'is_notification' => false,
                'daily_limit' => 5,
                'is_delete' => false,
                'insurance' => 0
            ]);

            if ($qOnly->target == 1) {
                $maxConsumption = Bid::maxConsumption();
                $bData->consumption = ceil($maxConsumption + ($maxConsumption * 0.05));
                $bData->daily_limit = 0;
            }

            $Bid = Bid::create($bData->toArray());
            $DemoBid = DemoBid::create([
                'bid_id' => $Bid->id,
                'user_id' => $request->user()->id,
                'request' => json_encode($bData)
            ]);
        }
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'ogrn' => [
                'required',
                'regex:/^([0-9]+)$/',
            ],
            'inn' => [
                'required',
                'regex:/^([0-9]+)$/',
            ],
            'kpp' => [
                'regex:/^([0-9]+)$/',
            ],
            'bik' => [
                'required',
                'regex:/^([0-9]+)$/',
            ],
            'bank' => [
                'required',
            ],
            'ksch' => [
                'required',
                'regex:/^([0-9]+)$/',
            ],
            'rsch' => [
                'required',
                'regex:/^([0-9]+)$/',
            ],
            'jour_address' => [
                'required',
            ],
            'poste_address' => [],
            'name' => [],
            'fio' => [],
        ], [
            'ogrn.required' => 'Обязательно введите ОГРН',
            'ogrn.regex' => 'ОГРН должен быть числами',

            'inn.required' => 'Обязательно введите ИНН',
            'inn.regex' => 'ИНН должен быть числами',

            'kpp.regex' => 'КПП должно быть числами',

            'bik.required' => 'Обязательно введите БИК',
            'bik.regex' => 'БИК должен быть числами',

            'bank.required' => 'Обязательно введите банк',

            'ksch.required' => 'Обязательно введите К/СЧ',
            'ksch.regex' => 'К/СЧ должно быть числами',

            'rsch.required' => 'Обязательно введите Р/СЧ',
            'rsch.regex' => 'Р/СЧ должно быть числами',

            'jour_address.required' => 'Обязательно введите юр. адресс',
        ]);
    }
}
