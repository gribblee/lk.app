<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

use App\Models\Deal;
use App\Models\User;
use App\Models\Status;
use App\Models\Bid;
use App\Models\Option;
use App\Helpers\stdObject;
use App\Models\HistoryPayment;

use App\Helpers\BitrixApi;

use Exception;

class DistributedController extends Controller
{
    //

    function __construct()
    {
        $this->bitrix24 = new BitrixApi('h58v8sy0z4ltt2zh');
    }

    public function index(Request $request)
    {
        if ($request->user()->role === 'ROLE_ADMIN' || $request->user()->role === 'ROLE_WEBMASTER') {
            $distributed = Deal::select('*', 'deals.id AS deal_id')
                ->with('region')
                ->with('direction')
                ->with('status')
                ->when($request->has('break'), function ($q) {
                    return $q->where('deals.is_delete', true)->whereNotNull('deals.bid_id');
                })
                ->when(!$request->has('break'), function ($q) {
                    return $q->whereHas('status', function ($q) {
                        $q->where('type', 1004);
                    })->where('deals.is_delete', false);
                })
                ->orderBy('deals.created_at', 'DESC')
                ->paginate(15);
            return response()->json([
                'success' => true,
                'distributed' => $distributed
            ]);
        }
        return response('Доступ запрещён', 403);
    }

    public function deal(Request $request, int $id)
    {
        if ($request->user()->role === 'ROLE_ADMIN' || $request->user()->role === 'ROLE_WEBMASTER') {
            $deal = Deal::select('*', 'deals.id as deal_id')
                ->where('deals.id', $id)
                ->with('direction')
                ->with('disput')
                ->with('region')
                ->with('status')
                ->where('is_delete', false)
                ->firstOrFail();
            return response()->json($deal);
        }
        return response('Доступ запрещён', 403);
    }

    public function status(Request $request, int $id)
    {
        if ($request->user()->role === 'ROLE_ADMIN' || $request->user()->role === 'ROLE_WEBMASTER') {
            $deal = Deal::with('region')->findOrFail($id);
            $status = Status::firstStatus();
            $statusNo = Status::noDistributed();
            /**
             * После этого заявку нужно отправить на распределение
             * BEGIN CAAP
             */
            $bid = Bid::with('user')
                ->with('direction')
                ->selectRaw('*, (
                (FLOOR(RANDOM() * consumption))
            ) AS weight')
                ->where('direction_id', $deal->direction_id)
                ->where(function ($qAnd) use ($deal) {
                    return $qAnd->where(function ($query) use ($deal) {
                        return $query->when(isset($deal->region->id), function ($q) use ($deal) {
                            return $q->whereJsonContains('regions', [
                                ['id' => $deal->region->id]
                            ]);
                        })->orWhere(function ($query) {
                            return $query->whereJsonLength('regions', 0);
                        });
                    }); //->whereRaw("(select count(*) from
                    //        deals where bids.id = (deals.bid_id)
                    //        and deals.created_at = date_trunc('day', current_date)
                    //    ) < bids.daily_limit OR bids.daily_limit = 0");
                })
                ->whereExists(function ($query) {
                    return $query->select(\DB::raw(1))
                        ->from('users')
                        ->whereRaw('users.id = bids.user_id AND bids.consumption <= users.balance');
                })
                ->where('is_launch', true)
                ->where('is_delete', false)
                ->orderByDesc('weight')->first();

            if ($bid) {
                $option = Option::getKeyValue();
                $optionBonus = Option::getValue('bill_bonus') ?? 50;
                $bonus = (($optionBonus / 100) * $bid->consumption);

                $user = User::findOrFail($bid->user->id);
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

                $insuranceRate = ceil($bid->consumption + ($bid->consumption * ($option->insurance_rate / 100)));

                if ($user->with_bonus && $user->bonus >= $bonus) {

                    $paymentStory->before_balance = $user->balance;
                    $paymentStory->before_bonus = ceil($user->bonus);
                    $paymentStory->after_balance = ceil($user->balance - ($bid->consumption - $bonus));
                    $paymentStory->paybonus = ceil($bonus);
                    $paymentStory->paysum = ceil($bid->consumption);
                    $paymentStory->after_bonus = ceil($user->bonus - $bonus);
                    if ($bid->is_insurance && $user->balance >= $insuranceRate) {
                        $user->balance = abs(ceil($user->balance - ceil($insuranceRate - $bonus)));
                    } else {
                        $user->balance = abs(ceil($user->balance - $insuranceRate));
                    }


                    $user->bonus = ceil($user->bonus - $bonus);
                } else {
                    $paymentStory->before_balance = ceil($user->balance);
                    $paymentStory->before_bonus = ceil($user->bonus);
                    $paymentStory->after_balance = ceil($user->balance - $bid->consumption);
                    // $paymentStory->paybonus = ceil($bonus);
                    $paymentStory->paysum = $bid->consumption;
                    $paymentStory->after_bonus = ceil($user->bonus);

                    if ($bid->is_insurance && $user->balance >= $insuranceRate) {
                        $user->balance = abs(ceil($user->balance - $insuranceRate));
                    } else {
                        $user->balance = abs(ceil($user->balance - $bid->consumption));
                    }
                }

                if ($bid->is_insurance) {
                    if ($user->balance >= $insuranceRate) {
                        $deal->is_insurance = true;
                        if ($user->with_bonus && $user->bonus >= $bonus) {
                            $deal->amount = ceil($insuranceRate - $bonus);
                            $deal->amount_bonus = ceil($bonus);
                        } else {
                            $deal->amount = ceil($insuranceRate);
                        }
                    } else {
                        if ($user->with_bonus && $user->bonus >= $bonus) {
                            $deal->amount = ceil($bid->consumption - $bonus);
                            $deal->amount_bonus = ceil($bonus);
                        } else {
                            $deal->amount = ceil($bid->consumption);
                        }
                    }
                } else {
                    if ($user->with_bonus && $user->bonus >= $bonus) {
                        $deal->amount = ceil($bid->consumption - $bonus);
                        $deal->amount_bonus = ceil($bonus);
                    } else {
                        $deal->amount = ceil($bid->consumption);
                    }
                }

                // if ($bid->is_insurance) { //Если заявка по страховке
                //     if ($user->with_bonus && $user->bonus >= $bonus) {
                //         $insuranceAmount = ($bid->consumption + ($bid->consumption * ($option['insurance_rate'] / 100))) - $bonus;
                //     } else {
                //         $insuranceAmount = $bid->consumption + ;
                //     }
                //     if ($bid->is_insurance && $bid->user->balance >= $insuranceAmount) {
                //         $deal->is_insurance = true;
                //         $deal->amount = $insuranceAmount;
                //     } else {
                //         $deal->amount = $bid->consumption;
                //     }
                // }

                $hp = HistoryPayment::create($paymentStory->toArray());

                if ($user->balance < $bid->consumption) {
                    $bid->is_launch = false;
                    $bid->save();
                }
                $user->save();

                $deal->bid_id = $bid->id;
                $deal->status_id = $status->id;
                $deal->save();
                $bitrixError = '';
                try {
                    if (
                        $bid->user->contact_id != null
                        && $bid->user->contact_id != 0
                    ) {
                        $this->addBitrix($request, $deal, $bid);
                    }
                } catch (Exception $e) {
                    $bitrixError = $e->getMessage();
                }

                $this->sendMail($bid->user);

                return response()->json([
                    'success' => true,
                    'msg' => 'Статус обновлён, заявка распределена'
                ]);
            } else {
                /**
                 * END CAAP
                 */
                $deal->status_id = $statusNo->id;
                $deal->save();
                return response()->json([
                    'success' => false,
                    'error' => 'Статус не обновлён, заявка не распределена',
                ]);
            }
        }
        return response('ДОСУТП ЗАПРЕШЁН', 403);
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

    protected function addBitrix(Request $request, Deal $deal, Bid $Bid)
    {
        $this->bitrix24
            ->lead('default', $request->header('referer')
                ?? $request->input('referer')
                ?? 'https://lk.leadz.monster')
            ->utm($request->all())
            ->field('ADDRESS_CITY', $deal->region->name ?? 'Не определно')
            ->field('UF_CRM_1602571646472', $Bid->direction->name)
            ->field('OPPORTUNITY', $Bid->consumption)
            ->field('CURRENCY_ID', 'RUB')
            ->field('EMAIL', $deal->email, 'WORK')
            ->field('PHONE', $deal->phone, 'WORK')
            ->field('NAME', $deal->name ?? 'Без имени')
            ->field('ASSIGNED_BY_ID', $Bid->user->contact_id)
            ->add();
    }

    public function update(Request $request, int $id)
    {
        if ($request->user()->role === 'ROLE_ADMIN' || $request->user()->role === 'ROLE_WEBMASTER') {
            $deal = Deal::findOrFail($id);
            $msg = '';
            $isUpd = false;

            if ($request->has('direction_id')) {
                $deal->direction_id = $request->input('direction_id');
                $msg = 'Направление обновлено';
                $isUpd = true;
            }

            if ($request->has('region_id')) {
                $deal->region_id = $request->input('region_id');
                $msg = 'Регион обновлён';
                $isUpd = true;
            }
            if ($isUpd) {
                $deal->save();
                return response()->json([
                    'success' => true,
                    'msg' => $msg
                ]);
            }
            return response()->json([
                'success' => false,
                'error' => 'Ничего не обновлено'
            ]);
        }
        return response('ДОСУТП ЗАПРЕШЁН', 403);
    }

    public function delete(Request $request, int $id)
    {
        if ($request->user()->role === 'ROLE_ADMIN' || $request->user()->role === 'ROLE_WEBMASTER') {
            $deal = Deal::find($id);
            if ($deal) {
                $deal->is_delete = true;
                $deal->save();
                return response()->json([
                    'success' => true,
                    'msg' => 'Заявка успешно помещена в брак'
                ]);
            }
            return response()->json([
                'success' => false,
                'error' => 'Нет такой заявки'
            ]);
        }
        return response('ДОСУТП ЗАПРЕШЁН', 403);
    }
}
