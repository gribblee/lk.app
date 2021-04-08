<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Collection;

use Illuminate\Http\Request;
use App\Helpers\BitrixApi;
use App\Helpers\HelperPayment;
use App\Helpers\stdObject;
use App\Models\AppToken;
use App\Models\Bid;
use App\Models\Deal;
use App\Models\User;
use App\Models\Option;
use App\Models\HistoryPayment;
use App\Models\Status;
use App\Models\Region;
use Exception;
use Illuminate\Support\Facades\Log;

use App\Helpers\RunOn;

use App\Helpers\Math\SVDMatrix;
use App\Models\Category;

class RunOnController extends Controller
{
    protected $bitrix24;
    protected $Response;

    function __construct()
    {
        $this->bitrix24 = new BitrixApi('h58v8sy0z4ltt2zh');
        $this->RunOn = new RunOn;
        $this->Response = new stdObject([
            'success' => false,
            'status' => 0,
            'data' => (object)[]
        ]);
    }

    /**
     * @param Request $request
     * @param string $hash
     * @return JsonResponse
     */
    public function append(Request $request, string $hash)
    {
        Log::info("Referer: {$request->header('referer')}");
        Log::info("Request: " . json_encode($request->all()));

        $Response = new stdObject([
            'status' => 'ENTERED',
            'message' => 'Заявка поступила'
        ]);
        try {
            $AppToken = AppToken::where('hash', $hash)->firstOrFail();


            $region = $this->definitionRegion($request); //Определение региона
            $deal = $this->createDeal($request, $AppToken, $region); //Создание заявки
            $notClaimsId = $this->foundedDealsClaimsId($AppToken, $request);
            $claims = $this->findClaims($AppToken, $region, $notClaimsId); //Поиск заявок

            Log::info("Deal: " . json_encode($deal));
            Log::info("Region: " . json_encode($region) . "\r\n");

            if (!$claims->isEmpty()) {
                Log::info("Claims is not Empty");
                $claim = $this->RunOn->claimsMatch($claims, $region); //Поиск совпадения
                if ($claim) { //Вторая проверка
                    Log::info("Claim Founed ID {$claim->id} Consumption {$claim->consumption}");
                    $user = User::where(['role' => 'ROLE_USER', 'is_delete' => false])->find($claim->user_id);
                    if ($user) { //Заявка не должна попадать менеджеру или администратору
                        Log::info("Claim User success ID {$user->id}");
                        //** */ Списание страховки
                        // ($claim->insurance) > 0 ?
                        //     Bid::find($claim->id)
                        //     ->update([
                        //         'insurance' => $claim->insurance - 1
                        //     ])
                        //     : false;
                        //** Посылка в битрикс и оплата
                        $category = Category::find($user->category_id);
                        ($user->contact_id != null && $user->contact_id != 0)
                            ? $this->addBitrix($request, $deal, $claim, $category->source_id)
                            : false;
                        $userPay = $this->userPayment($user, $claim); //Оплата
                        //** Обновление поступившей заявки
                        $deal->update([
                            'amount_bonus' => $userPay['bonus'] ?? 0,
                            'amount' => $claim->consumption,
                            'bid_id' => $claim->id,
                            'is_insurance' => $claim->is_insurance,
                            'status_id' => Status::firstStatus()->id,
                        ]);
                        //** Отправка на почту
                        $this->sendMail($user);

                        Log::info("Claim: " . json_encode($claim));
                        $Response->status = 'CONFIRMED';
                        $Response->message = 'Заявка распределена';
                    }
                }
            }
            return response()->json($Response->toArray());
        } catch (Exception $e) {
            Log::notice("Notice: {$e->getMessage()}");
            return response()->json([
                'message' => 'OK',
            ], 200);
        }
    }

    protected function userPayment($user, $claim)
    {
        $optionBonus = Option::getValue('bill_bonus') ?? 50;
        $bonus = (($optionBonus / 100) * $claim->consumption);

        $paymentStory = new stdObject([
            'user_id' => $user->id,
            'type_transaction' => '12',
            'paysum' => 0,
            'paybonus' => 0,
            'before_balance' => 0,
            'after_balance' => 0,
            'before_bonus' => 0,
            'after_bonus' => 0
        ]);
        $userUpdate = new stdObject([
            'balance' => $user->balance,
            'bonus' => ceil($user->bonus)
        ]);

        $payBonus = 0;

        if ($user->with_bonus && $user->bonus >= $bonus) {
            $userUpdate->balance = ceil($user->balance - ($claim->consumption - $bonus));
            $userUpdate->bonus = ceil($user->bonus - $bonus);

            $paymentStory->before_balance = ceil($user->balance);
            $paymentStory->after_balance = ceil($userUpdate->balance);
            $paymentStory->paysum = $claim->consumption;
            $paymentStory->paybonus = ceil($bonus);
            $paymentStory->before_bonus = ceil($user->bonus);
            $paymentStory->after_bonus = ceil($userUpdate->bonus);
            $payBonus = ceil($bonus);
        } else {
            $userUpdate->balance = ceil($user->balance - $claim->consumption);

            $paymentStory->before_balance = ceil($user->balance);
            $paymentStory->after_balance = ceil($userUpdate->balance);
            $paymentStory->paysum = $claim->consumption;
            $paymentStory->before_bonus = ceil($user->bonus);
            $paymentStory->after_bonus = ceil($user->bonus);
        }

        $hp = HistoryPayment::create($paymentStory->toArray());
        $user->update($userUpdate->toArray());

        Log::info('User payment: ' . json_encode($user));
        Log::info("User ID-{$user->id} history payment: " . json_encode($hp));
        return [
            'user' => $user,
            'bonus' => $payBonus
        ];
    }

    protected function foundedDealsClaimsId($AppToken, $request)
    {
        $notClaimsId = [];
        $foundDeal = Deal::where([
            [
                'phone', 'LIKE', "%{$request->phone}%"
            ],
            [
                'email', 'LIKE', "%{$request->email}%"
            ],
            [
                'direction_id', '=', $AppToken->direction_id
            ]
        ])->where('is_delete', false)
            ->whereNotNull('bid_id')
            ->get(); //У заявки нужно ещ добавить чтобы там считался интервал по дням
        if (!$foundDeal->isEmpty()) {
            foreach ($foundDeal as $fd) {
                if ($fd) {
                    $notClaimsId[] = $fd->bid_id;
                }
            }
        }
        return $notClaimsId;
    }

    protected function definitionRegion(Request $request)
    {
        $region = [];
        if ($request->has('kladr_id') || $request->has('region_text')) {
            $region = Region::when($request->has('kladr_id'), function ($q) use ($request) {
                return $q->where('kladr_id', str_pad($request->kladr_id, 13, '0', STR_PAD_RIGHT));
            })->when($request->has('region_text'), function ($q) use ($request) {
                return $q->orWhere('name_with_type', 'LIKE', "%{$request->region_text}%");
            })->first();
        }

        return $region;
    }

    protected function findClaims(AppToken $appToken, $region, $notClaimId)
    {
        /**
         * Ищет группы созданых направлений
         */
        return Bid::selectRaw("*, (select count(*) from
                deals where bids.id = (deals.bid_id)
                and date_trunc('day', deals.created_at) = date_trunc('day', current_date)
            ) as deals_today_count")->with('direction')
            ->with('user')
            ->where([
                'is_launch' => true,
                'is_delete' => false,
                'direction_id' => $appToken->direction_id
            ])
            ->whereHas('user', function ($q) {
                return $q->whereRaw('(users.id = bids.user_id) 
                    AND (users.balance >= bids.consumption)');
            })
            ->whereHas('direction', function ($q) {
                return $q->whereRaw('(directions.id = bids.direction_id) AND bids.consumption >= (directions.cost_price + (directions.cost_price * (directions.extra / 100)))');
            })
            ->where(function ($q) use ($region) { //Определение региона надеюсь сработает
                return $q->whereJsonContains('regions', [
                    ['id' => $region->id ?? 0]
                ])->orWhere(function ($qw) {
                    return $qw->whereJsonLength('regions', 0);
                });
            })
            ->whereNotIn('id', $notClaimId)
            ->orderByDesc('consumption')
            ->limit(100)
            ->get(); //Пока установим лимит для оптимизации
    }

    protected function createDeal(Request $std, AppToken $appToken, $region)
    {
        /**
         * Создаёт пустую заявку
         */
        return Deal::create([
            'name' => $std->name ?? '',
            'phone' => $std->phone ?? '',
            'email' => $std->email ?? '',
            'amount' => 0,
            'region_id' => $region->id ?? null,
            'direction_id' => $appToken->direction_id ?? null,
            'utm' => json_encode(
                $this->getUTM($std->all())
            ),
            'request' => json_encode([
                'REQUEST' => $std->all(),
                'API_INFO' => $appToken,
                'REGIONS' => $region,
            ]),
            'token_id' => $appToken->id,
            'is_view' => false,
            'is_manager_view' => false,
            'is_delete' => false,
            'bid_id' => null,
            'is_insurance' => false,
            'status_id' => Status::noDistributed()->id
        ]);
    }

    protected function addBitrix(Request $request, Deal $deal, $Bid, $sourceId = 3)
    {
        $region = Region::find($deal->region_id ?? 0);
        $this->bitrix24
            ->lead('default', $request->header('referer')
                ?? $request->input('referer')
                ?? 'https://lk.leadz.monster')
            ->utm($request->all())
            ->field('ADDRESS_CITY', $region->name ?? 'Не определно')
            ->field('UF_CRM_1602571646472', $Bid->direction->name)
            // ->field('OPPORTUNITY', $Bid->consumption)
            // ->field('CURRENCY_ID', 'RUB')
            ->field('EMAIL', $deal->email, 'WORK')
            ->field('PHONE', $deal->phone, 'WORK')
            ->field('NAME', $deal->name ?? 'Без имени')
            ->field('SOURCE_ID', $sourceId)
            ->field('ASSIGNED_BY_ID', $Bid->user->contact_id)
            ->add();
        Log::info("Bitrix data: " . json_encode($this->bitrix24->getData()));
    }

    protected function sendMail(User $user)
    {
        /**
         * Отправка на почту 
         */
        try {
            Mail::send([], [], function ($message) use ($user) {
                $userMail = $user->email;
                $message->from('system@b2l.online', 'Leadz.Monster');
                $message->subject('Вам поступила заявка');
                if ($user->email_notification) {
                    $userMail = $user->email_notification;
                }
                $message->to($userMail)->cc($userMail);
                $message->setBody('<p>Вам поступила новая заявка <a href="http://lk.leadz.monster/deals">посмотреть</a></p>', 'text/html');
            });
        } catch (Exception $e) {
            Log::error("Error send mail: {$e->getMessage()}");
        }
    }

    protected function getUTM(array $input)
    {
        $UTM_MARK = [];
        foreach ($input as $k_utm => $v_utm) {
            (strpos(strtoupper($k_utm), 'UTM_') !== false)
                ? $UTM_MARK[strtoupper($k_utm)] = $v_utm
                : false;
        }
        return $UTM_MARK;
    }
}
