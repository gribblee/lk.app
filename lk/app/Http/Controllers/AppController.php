<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;

use Illuminate\Http\Request;
use App\Helpers\BitrixApi;
use App\Helpers\stdObject;
use App\Models\AppToken;
use App\Models\Bid;
use App\Models\Deal;
use App\Models\User;
use App\Models\Option;
use App\Models\HistoryPayment;
use App\Models\Status;
use Exception;

class AppController extends Controller
{
    protected $bitrix24;
    protected $Response;

    function __construct()
    {
        $this->bitrix24 = new BitrixApi('h58v8sy0z4ltt2zh');
        $this->Response = new stdObject([
            'success' => false,
            'status' => 0,
            'data' => []
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function index(Request $request)
    {
    }

    /**
     * @param $hash
     * @return JsonResponse
     */
    public function push(Request $request, string $hash)
    {

        $Deal = new Deal;
        $Bid = Bid::with('user')
            ->with('direction')
            ->selectRaw('*, (
                (FLOOR(RANDOM() * consumption))
            ) AS weight')
            ->where(function ($query) use ($request) {
                $query->when(isset($request->region->id), function ($q) use ($request) {
                    return $q->whereJsonContains('regions', [
                        ['id' => $request->region->id]
                    ])->orWhere(function ($query) {
                        return $query->whereJsonLength('regions', 0);
                    });
                });
            })
            ->where('direction_id', $request->appToken->direction->id)
            ->whereRaw("(select count(*) from
                        deals where bids.id = (deals.bid_id)
                        and deals.created_at = date_trunc('day', current_date)
                    ) < bids.daily_limit OR bids.daily_limit = 0")
            ->whereHas('user', function ($query) {
                return $query->whereRaw('bids.consumption <= users.balance');
            })
            ->where('is_launch', true)
            ->where('is_delete', false)
            ->orderByDesc('weight')->first();

        $name = $request->has('name') ? $request->name : $request->Name;
        $phone = $request->has('phone') ? $request->phone : $request->Phone;
        $email = $request->has('email') ? $request->email : $request->Email;

        $Deal->name = $name ?? '';
        $Deal->email = $email ?? '';
        $Deal->phone = $phone ?? '';
        $Deal->region_id = $request->region->id ?? null;
        $Deal->direction_id = $Bid->direction->id
            ?? $request->appToken->direction_id
            ?? null;
        $Deal->utm = json_encode(
            $this->getUTM($request->all())
        );
        $Deal->request = json_encode([
            'request' => $request->all(),
            'api_info' => $request->appToken,
            'region' => $request->region ?? [],
            'http_region' => $request->http_region ?? []
        ]);
        $Deal->token_id = $request->appToken->id;
        $Deal->is_view = false;
        $Deal->is_manager_view = false;
        $Deal->is_delete = false;

        if ($Bid && $request->region != null) {

            $optionBonus = Option::where('name', 'bill_bonus')->first()->bill_bonus ?? 1;
            $bonus = (($optionBonus / 100) * $Bid->consumption);

            $paymentStory = new stdObject([
                'user_id' => $Bid->user->id,
                'type_transaction' => '12',
                'paysum' => 0,
                'paybonus' => 0,
                'before_balance' => 0,
                'after_balance' => 0,
                'before_bonus' => 0,
                'after_bonus' => 0
            ]);

            if (
                $Bid->user->with_bonus
                && $Bid->user->bonus >= $bonus
            ) {
                $paymentStory->before_balance = $Bid->user->balance;
                $paymentStory->before_bonus = $Bid->user->bonus;

                $Bid->user->balance = $Bid->user->balance - ($Bid->consumption - $bonus);
                $Bid->user->bonus = $Bid->user->bonus - $bonus;

                $paymentStory->paysum = ($Bid->consumption - $bonus);
                $paymentStory->paybonus = $bonus;
                $paymentStory->after_balance = $Bid->user->balance;
                $paymentStory->after_bonus = $Bid->user->bonus;
            } else {
                $paymentStory->before_balance = $Bid->user->balance;
                $Bid->user->balance = $Bid->user->balance - $Bid->consumption;
                $paymentStory->after_balance = $Bid->user->balance;
                $paymentStory->paysum = $Bid->consumption;
            }

            if ($Bid->insurance > 0) { //Если заявка по страховке
                $Bid->insurance = $Bid->insurance - 1;
                $Deal->is_insurance = true;
            }

            if ($Bid->user->balance < $Bid->consumption) {
                $Bid->is_launch = false;
            }

            if (
                $Bid->user->contact_id != null
                && $Bid->user->contact_id != 0
            ) {
                $this->addBitrix($request, $Bid);
            }

            HistoryPayment::create($paymentStory->toArray());

            $Deal->bid_id = $Bid->id;
            $Deal->amount = $Bid->consumption;
            $Deal->status_id = Status::firstStatus()->id;

            $this->sendMail($Bid->user);

            $Bid->user->save();
            $Bid->save();

            $this->Response->success = true;
            $this->Response->status = 101;
        } else {
            $Deal->bid_id = null;
            $Deal->status_id = Status::noDistributed()->id;
            $this->Response->success = true;
            $this->Response->status = 102;
        }

        $request->appToken->update([
            'count_deals' => $request->appToken->count_deals + 1
        ]);

        $Deal->save();

        return response()->json($this->Response);
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
        }
    }

    protected function addBitrix(Request $request, Bid $Bid)
    {
        $this->bitrix24
            ->lead('register', $request->header('referer')
                ?? $request->input('referer')
                ?? 'http://lk.leadz.monster')
            ->utm($request->all())
            ->field('ADDRESS_CITY', $request->region->name ?? 'Не определно')
            ->field('UF_CRM_1602571646472', $Bid->direction->name)
            ->field('OPPORTUNITY', $Bid->consumption)
            ->field('CURRENCY_ID', 'RUB')
            ->field('EMAIL', $request->email, 'WORK')
            ->field('PHONE', $request->phone, 'WORK')
            ->field('NAME', $request->name ?? 'Без имени')
            ->field('ASSIGNED_BY_ID', $Bid->user->contact_id)
            ->add();
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
