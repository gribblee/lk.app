<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

use App\Models\Payment;
use App\Models\User;
use App\Models\HistoryPayment;
use App\Models\PaymentCredit;
use App\Models\Requisite;
use App\Models\Notification;
use App\Models\Option;
use App\Helpers\SendPulse;

use App\Helpers\Tinkoff;
use App\Helpers\HelperPayment;

use Barryvdh\DomPDF\PDF;
use PHPUnit\TextUI\Help;

use App\Helpers\Dreamkas\Api as Dreamkas;
use App\Helpers\Dreamkas\CustomerAttributes;
use App\Helpers\Dreamkas\exceptions\ValidationException;
use App\Helpers\Dreamkas\Payment as DreamkasPayment;
use App\Helpers\Dreamkas\Position;
use App\Helpers\Dreamkas\Receipt;
use App\Helpers\Dreamkas\TaxMode;
use App\Models\DreamkasReceipt;
use GuzzleHttp\Exception\ClientException;


class PaymentController extends Controller
{
    protected $bookIdBill;
    protected $bookIdBalance;
    protected $Dreamkas;

    function __construct()
    {
        $options = Option::getKeyValue();

        $this->sendPulse = new SendPulse;
        $this->bookIdBill = $options->bookIdBill ?? 0;
        $this->bookIdBalance = $options->bookIdBalance ?? 0;
        $this->dreamkasApi = new Dreamkas(config('dreamkassa.token'), config('dreamkassa.deviceId'), Dreamkas::MODE_PRODUCTION);
    }

    /**
     * @return JsonResponse
     */
    public function bills(Request $request)
    {
        if ($request->user()->role == 'ROLE_ADMIN') {
            $payments = Payment::with('user', 'requisite')->where('type', HelperPayment::TYPE_RQ)
                ->whereNotIn('status', [
                    HelperPayment::RQ_STATUS_CANCEL,
                    HelperPayment::RQ_STATUS_ERROR
                ])->orderBy('id', 'DESC')->orderBy('status', 'DESC');
            if ($request->has('action')) {
                switch ($request->action) {
                    case 1:
                        $payments->where('status', HelperPayment::RQ_STATUS_CREATE);
                        break;
                    case 2:
                        $payments->where('status', HelperPayment::RQ_STATUS_PARTIAL_PAYMENT);
                        break;
                    case 7:
                        $payments->where('status', HelperPayment::RQ_STATUS_PAID);
                        break;
                }
            }
            if ($request->has('search')) {
                foreach ($request->search as $key => $value) {
                    $payments->whereHas('requisite', function ($q) use ($key, $value) {
                        return $q->where($key, 'LIKE',  "%$value%");
                    });
                }
            }
            return response()->json($payments->paginate(20));
        }
        return response()->json(['Доступ запрещён!'], 403);
    }

    /**
     * @return JsonResponse
     */
    public function payment(Request $request)
    {
        if ($request->has('OrderId')) {
            $payment = Payment::find($request->OrderId);

            Log::info(json_encode($request->all()), ['stack']);

            if ($request->Status == 'AUTHORIZED' && $request->Success == true) {
                if ($payment->status == HelperPayment::CD_STATUS_CREATE) {
                    $payment->status = HelperPayment::CD_STATUS_AUTHORIZE;
                }
            }
            if ($request->Status == 'CONFIRMED' && $request->Success == true) {
                $user = User::find($payment->user_id);
                $user->balance = $user->balance + ($request->Amount / 100);
                $user->save();

                $payment->after_balance = $user->balance;
                HistoryPayment::create([
                    'user_id' => $user->id,
                    'type_transaction' => '10',
                    'paysum' => ($request->Amount / 100),
                    'paybonus' => 0,
                    'before_balance' => $user->balance - ($request->Amount / 100),
                    'after_balance' => $user->balance,
                    'before_bonus' => $user->bonus,
                    'after_bonus' => $user->bonus
                ]);

                $payment->status = HelperPayment::CD_STATUS_PAID;
                $payment->payment_id = $request->PaymentId;
                $payment->card = $request->Pan;
                /**
                 * Здесь DreamKassa
                 */
                $receipt = new Receipt();
                $receipt->taxMode = TaxMode::MODE_PATENT;
                $receipt->positions[] = new Position([
                    'name' => 'Пополнение личного кабинета Лидз.Монстер',
                    'quantity' => 1,
                    'price' => $request->Amount * 100, // цена в копейках за 1 шт. или 1 грамм
                ]);
                $receipt->payments[] = new DreamkasPayment([
                    'sum' => $request->Amount * 100, // стоимость оплаты по чеку
                    'type' => 'CASHLESS'
                ]);
                $receipt->attributes = new CustomerAttributes([
                    'email' => $user->email, // почта покупателя
                    'phone' => $user->phone, // телефон покупателя
                ]);
                $response = [];
                try {
                    $response = $this->dreamkasApi->postReceipt($receipt);
                    // Запись чека в БД
                    DreamkasReceipt::create([
                        'receiptId' => $response['id'],
                        'status' => $response['status'],
                        'user_id' => $user->id,
                        'amount' => $request->Amount * 100,
                        'type' => 'CASHLESS'
                    ]);
                } catch (ValidationException $e) {
                    Log::error("Receipt Valid Error: " . json_encode($e->getMessage()) . "\r\n");
                } catch (ClientException $e) {
                    Log::error("Receipt Response Error: " . json_encode(['body' => $e->getResponse()->getBody()]) . "\r\n");
                    // Это исключение кидается, когда при передачи чека в Дрикас произошла ошибка. Лучше отправить чек ещё раз
                    // Если будут дубли - потом отменяйте через $receipt->type = Receipt::TYPE_REFUND;
                }

                $this->sendPulse->addEmails($this->bookIdBalance, [
                    [
                        'email' => $user->email,
                        'variables' => [
                            'phone' => $user->phone,
                            'name' => $user->name,
                            'balanceBefore' => $user->balance - ($request->Amount / 100),
                            'balanceAfter' => $user->balance,
                            'paysum' => ($request->Amount / 100)
                        ]
                    ]
                ]);
            } else {
                $payment->status = HelperPayment::CD_STATUS_ERROR;
            }
            $payment->save();
            return response('OK');
        }
    }

    /**
     * @return JsonResponse
     */
    public function creditPayment(Request $request)
    {
        if ($request->has('id') && $request->has('status')) {
            $payment = Payment::where('id', $request->id)->first();
            $paymentCredit = PaymentCredit::where('payment_id', $request->id)->first();

            $requestInput = [
                'committed' => (bool)$request->committed,
                'credit_amount' => $request->credit_amount,
                'demo' => (bool)$request->demo,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'middle_name' => $request->middle_name,
                'first_payment' => $request->first_payment,
                'order_id' => '',
                'monthly_payment' => $request->monthly_payment,
                'order_amount' => $request->order_amount,
                'phone' => $request->phone,
                'product' => $request->product,
                'status' => $request->status,
                'term' => $request->term,
                'payment_id' => $request->id,
                'created_at' => date("Y-m-d H:i:s")
            ];

            if (!$paymentCredit) {
                $paymentCredit = PaymentCredit::create($requestInput);
            }

            switch ($request->status) {
                case 'approved':
                    Payment::where('id', $request->id)->update(['status' => HelperPayment::CT_STATUS_APPROVED]);
                    $paymentCredit->update($requestInput);
                    break;
                case 'signed':
                    PaymentCredit::where('id', $paymentCredit->id)->update($requestInput);
                    $user = User::find($payment->user_id);
                    $user->balance = $user->balance + $payment->paysum;
                    $user->save();
                    $payment->after_balance = $user->balance;
                    $payment->status = HelperPayment::CT_STATUS_SIGNED;
                    $payment->save();

                    HistoryPayment::create([
                        'user_id' => $user->id,
                        'type_transaction' => '15',
                        'paysum' => $payment->paysum,
                        'paybonus' => 0,
                        'before_balance' => $user->balance - $payment->paysum,
                        'after_balance' => $user->balance,
                        'before_bonus' => $user->bonus,
                        'after_bonus' => $user->bonus
                    ]);
                    Notification::create([
                        'description' => "Ваш баланс пополнен на {$payment->paysum} ₽",
                        'user_id' => $payment->user_id
                    ]);
                    break;
                case 'rejected':
                    Payment::where('id', $request->id)->update(['status' => HelperPayment::CT_STATUS_REJECT]);
                    PaymentCredit::where('id', $paymentCredit->id)->update($requestInput);
                    break;
                case 'canceled':
                    Payment::where('id', $request->id)->update(['status' => HelperPayment::CT_STATUS_CANCELED]);
                    PaymentCredit::where('id', $paymentCredit->id)->update($requestInput);
                    break;
            }

            $paymentCredit->save();
            return response('OK');
        }
        return response('ACCESS DENIED!');
    }

    /**
     * @return JsonResponse
     */

    public function requisitePayment(Request $request)
    {
        if ($request->user()->role == 'ROLE_ADMIN') {
            if ($request->has('ids') && $request->has('action')) {
                $payment = Payment::whereIn('id', $request->ids);
                switch ($request->action) {
                    case 3:
                        $payment->update(['status' => HelperPayment::RQ_STATUS_PARTIAL_PAYMENT]);
                        break;
                    case 4:
                        $paid = Payment::with('user')->whereIn('id', $request->ids)->get();
                        foreach ($paid as $pid) {
                            $bonus = $pid->user->bonus;
                            if (
                                ($pid->paysum > 3000 && $pid->paysum < 10000) //Здесь поменять на данные с опций
                                && ($pid->user->balance + ($pid->paysum / 2))
                            ) {
                                $bonus = ($pid->paysum / 100) * 10;
                            }
                            $pid->user->balance = floor($pid->user->balance + $pid->paysum);
                            $pid->user->bonus = floor($pid->user->bonus + $bonus);
                            $pid->user->save();
                            $pid->status = HelperPayment::RQ_STATUS_PAID;
                            $pid->after_balance = $pid->user->balance;
                            $pid->save();
                            $msg = "Ваш баланс пополнен на {$pid->paysum} ₽";
                            if ($bonus > 0) {
                                $msg .= "\r\n\r\n Вы получили {$bonus} бонусных бала";
                            }
                            Notification::create([
                                'description' => $msg,
                                'user_id' => $pid->user->id
                            ]);
                            HistoryPayment::create([
                                'user_id' => $pid->user->id,
                                'type_transaction' => '11',
                                'paysum' => $pid->paysum,
                                'paybonus' => $bonus,
                                'before_balance' => $pid->user->balance - $pid->paysum,
                                'after_balance' => $pid->user->balance,
                                'before_bonus' => $pid->user->bonus - $bonus,
                                'after_bonus' => $pid->user->bonus
                            ]);
                        }
                        $payment->update([
                            'after_balance' => $payment->user->balance,
                            'status' => HelperPayment::RQ_STATUS_PAID
                        ]);
                        /**
                         * Здесь DreamKassa
                         */
                        $receipt = new Receipt();
                        $receipt->taxMode = TaxMode::MODE_PATENT;
                        $receipt->positions[] = new Position([
                            'name' => 'Пополнение личного кабинета Лидз.Монстер',
                            'quantity' => 1,
                            'price' => $pid->paysum * 100, // цена в копейках за 1 шт. или 1 грамм
                        ]);
                        $receipt->payments[] = new DreamkasPayment([
                            'sum' => $pid->paysum * 100, // стоимость оплаты по чеку
                        ]);
                        $receipt->attributes = new CustomerAttributes([
                            'email' => $pid->user->email, // почта покупателя
                            'phone' => $pid->user->phone, // телефон покупателя
                        ]);
                        $response = [];
                        try {
                            $response = $this->dreamkasApi->postReceipt($receipt);
                            // Здесь запись чека в БД
                            DreamkasReceipt::create([
                                'receiptId' => $response['id'],
                                'status' => $response['status'],
                                'user_id' => $pid->user->id,
                                'amount' => $pid->paysum * 100,
                                'type' => 'CONSIDERATION'
                            ]);
                        } catch (ValidationException $e) {
                            Log::error("Receipt Valid Error: " . json_encode($e->getMessage()) . "\r\n");
                        } catch (ClientException $e) {
                            Log::error("Receipt Response Error: " . json_encode(['body' => $e->getResponse()->getBody()]) . "\r\n");
                            // Это исключение кидается, когда при передачи чека в Дрикас произошла ошибка. Лучше отправить чек ещё раз
                            // Если будут дубли - потом отменяйте через $receipt->type = Receipt::TYPE_REFUND;
                        }
                        break;
                    case 5:
                        $payment->update(['status' => HelperPayment::RQ_STATUS_CANCEL]);
                        break;
                }
                return response()->json([
                    'success' => true,
                    'message' => 'Обновлено'
                ]);
            }
            return response()->json([
                'success' => false,
                'message' => 'Не хватает данных'
            ]);
        }
        return response('Доступ запрещён!', 403);
    }
    /**
     * @return JsonResponse
     */
    public function cardCreate(Request $request)
    {
        /**
         * Ver 1.0 Start
         */
        if (
            $request->has('paysum')
            && !empty($request->input('paysum'))
        ) {
            if ($request->paysum >= 10000) {
                $payment = Payment::create([
                    'type' => HelperPayment::TYPE_CD,
                    'status' => HelperPayment::CD_STATUS_CREATE,
                    'requisite_id' => null,
                    'user_id' => $request->user()->id,
                    'paysum' => $request->paysum,
                    'before_balance' => $request->user()->balance,
                    'after_balance' => $request->user()->balance
                ]);
                $tinkoff = new Tinkoff(config('tinkoff.url'), config('tinkoff.terminalKey'), config('tinkoff.secretKey'), config('tinkoff.credit_url'));
                $paymentTinkoff = [
                    'OrderId'       => $payment->id,
                    'Amount'        => $request->paysum,
                    'Language'      => config('tinkoff.lang'),
                    'Description'   => config('tinkoff.description'),
                    'Email'         => $request->user()->email,
                    'Phone'         => $request->user()->phone,
                    'Name'          => $request->user()->name,
                    'Taxation'      => config('tinkoff.taxation')
                ];
                $tinkoffSettings[] = [
                    'Name'  => 'Пополнение личного счета',
                    'Price' => $request->paysum,
                    'NDS'   => config('tinkoff.nds'),
                ];
                $paymentURL = $tinkoff->paymentURL($paymentTinkoff, $tinkoffSettings);

                if (!$paymentURL) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Ошибка не возможно произвести оплату'
                    ]);
                }
                return response()->json([
                    'success' => true,
                    'payment_url' => $paymentURL,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Сумма должна быть не менее 10 000 руб.'
                ]);
            }
        }
        return response()->json([
            'success' => false,
            'message' => 'Ошибка не указана сумма'
        ], 400);
        /**
         * Ver 1.0 End
         */
    }

    /**
     * @return JsonResponse
     */
    public function requisiteCreate(Request $request)
    {
        /**
         * Ver 1.0 Start
         */
        if (
            ($request->has('requisite_id')
                && $request->has('paysum')) &&
            (!empty($request->input('requisite_id'))
                && !empty($request->input('paysum')))
        ) {
            if ($request->paysum >= 10000) {
                $requis = Requisite::where('user_id', $request->user()->id)->find($request->requisite_id);
                if ($requis) {
                    $paymentData = Payment::create([
                        'type' => HelperPayment::TYPE_RQ,
                        'status' => HelperPayment::RQ_STATUS_CREATE,
                        'requisite_id' => $requis->id,
                        'user_id' => $request->user()->id,
                        'paysum' => $request->paysum,
                        'before_balance' => $request->user()->balance,
                        'after_balance' => $request->user()->balance
                    ]);

                    /**
                     *  BEGIN
                     */
                    $pdfPath = '/payment/pdf/' . date("dmY", strtotime($paymentData->created_at)) . '-' . str_pad($paymentData->id . $paymentData->requisite_id, 6, '000000', STR_PAD_LEFT) . '.pdf';

                    $pdf = PDF::loadView('pdf.contract', [
                        'payment' => $paymentData,
                        'requisite' => $requis,
                        'title' => date("dmY", strtotime($paymentData->created_at)) . '-' . str_pad($paymentData->id . $paymentData->requisite_id, 6, '000000', STR_PAD_LEFT),
                        'bill' => str_pad($paymentData->id . $paymentData->requisite_id, 6, '000000', STR_PAD_LEFT),
                        'sum_to_string' => HelperPayment::number2string($paymentData->paysum)
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
                    $paymentId = date("dmY", strtotime($paymentData->created_at)) . '-' . str_pad($paymentData->id . $paymentData->requisite_id, 6, '000000', STR_PAD_LEFT);

                    Requisite::where('id', $requis->id)->update([
                        'requisite_payment_id' => $paymentId
                    ]);

                    Mail::send([], [], function ($message) use ($request, $pdfPath, $paymentData, $paymentId) {
                        $message->from('system@b2l.online', 'Leadz.Monster');
                        $message->subject('Счёт на пополнение в сервисе Leadz.Monster');
                        $message->to($request->user()->email)->cc($request->user()->email);
                        $message->attach(Storage::disk('public')->path($pdfPath));
                        $message->setBody('<h1>Здравствуйте</h1><br/><p>Вам выставлен счёт № ' . $paymentId . ' на сумму ' . $paymentData->paysum . ' ₽ в сервисе Leadz.Monster</p>', 'text/html');
                    });

                    $this->sendPulse->addEmails($this->bookIdBill, [
                        [
                            'email' => $request->user()->email,
                            'variables' => [
                                'phone' => $request->user()->phone,
                                'name' => $request->user()->name,
                            ]
                        ]
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Успешно',
                        'data' => $paymentData
                    ], 200);
                }
                return response()->json([
                    'success' => false,
                    'message' => 'Не найдены такие реквизиты'
                ], 404);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Сумма должна быть не менее 10 000 руб.'
                ]);
            }
        }
        return response()->json([
            'success' => false,
            'message' => 'Ошибка не указана сумма или реквизиты'
        ], 400);
        /**
         * Ver 1.0 End
         */
    }

    /**
     * @return JsonResponse
     */
    public function creditCreate(Request $request)
    {
        /**
         * Ver 1.0 Start
         */
        if (
            $request->has('sum')
            && !empty($request->input('sum'))
        ) {
            if ($request->sum >= 10000) {
                $payment = Payment::create([
                    'type' => HelperPayment::TYPE_CT,
                    'status' => HelperPayment::CT_STATUS_CREATE,
                    'requisite_id' => null,
                    'user_id' => $request->user()->id,
                    'paysum' => $request->sum,
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
                    'Sum'        => $request->sum,
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
                    'Price' => $request->sum,
                    'VendorCode' => 'L-' . $payment->id . '-' . date("dmy-his"),
                    'Category'   => 'Leadz.Monster - Лиды',
                ];
                $paymentResponse = $tinkoff->creditCreate($paymentTinkoff, $tinkoffSettings);
                if (!$paymentResponse) {
                    return response()->json([
                        'success' => false,
                        'message' => $tinkoff->error
                    ]);
                }
                $payment->update([
                    'tcv_id' => $paymentResponse->id
                ]);

                return response()->json([
                    'success' => true,
                    'payment_url' => $paymentResponse->link,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Сума должна быть не менее 10 000 руб.'
                ]);
            }
        }
        /**
         * Ver 1.0 End
         */
    }


    public function generalHistory(Request $request)
    {
        if ($request->user()->role == 'ROLE_ADMIN') {
            return response()->json(Payment::with('user')
                ->with('requisite')
                ->orderBy('created_at', 'DESC')
                ->orderBy('updated_at', 'DESC')
                ->paginate(10), 200);
        }
        return response()->json([
            'Дотсуп запрещён!'
        ], 403);
    }


    public function paymentDocument(Request $request, int $id)
    {
        $payment = Payment::with('requisite')->findOrFail($id);
        $pdfPath = '/payment/pdf/' . date("dmY", strtotime($payment->created_at)) . '-' . str_pad($payment->id . $payment->requisite_id, 6, '000000', STR_PAD_LEFT) . '.pdf';

        return Storage::disk('public')->download($pdfPath);
        // response(
        //     Storage::disk('public')->download($pdfPath)
        // )
        //     ->header('Accept-Ranges', 'bytes')
        //     ->header('Content-type', 'application/pdf')
        //     ->header('Content-length', Storage::disk('public')->size($pdfPath))
        //     ->header('Content-Range', 'bytes 0-' . Storage::disk('public')->size($pdfPath));
    }
}
