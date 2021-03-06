<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

use App\Models\User;
use App\Models\Deal;
use App\Models\Disput;
use App\Models\Category;
use App\Models\Notification;
use App\Models\HistoryPayment;
use App\Models\Bid;
use App\Models\AppToken;
use App\Models\Region;

use Illuminate\Support\Facades\Hash;


use App\Helpers\stdObject;
use App\Models\Direction;
use Exception;

use function PHPUnit\Framework\isNull;

class UserController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $orderField = $request->has('order_field') ? (empty($request->order_field) ? 'id' : $request->order_field) : 'id';
        $orderSort = $request->has('order_by') ? ($request->order_by == 'DEF' ? 'DESC' : $request->order_by) : 'DESC';

        $users = User::withCount('bids')->with('manager')->orderBy($orderField, $orderSort);
        if ($request->has('is_delete')) {
            $users->where('is_delete', $request->is_delete);
        } else {
            $users->where('is_delete', false);
        }
        if ($request->has('is_block')) {
            $users->where('is_block', $request->is_block);
        } else {
            $users->where('is_block', false);
        }
        if ($request->has('search')) {
            $search = (object)$request->search;
            if (empty($search->id) == false) {
                $users->where('id', $search->id);
            } else {
                if (empty($search->name) == false) {
                    $users->where('name', 'LIKE', "%{$search->name}%");
                }
                if (empty($search->phone) == false) {
                    $users->where('phone', 'LIKE', "%{$search->phone}%");
                }
                if (empty($search->email) == false) {
                    $users->where('email', 'LIKE', "%{$search->email}%");
                }
                if (empty($search->role) == false) {
                    $users->where('role', $search->role);
                }
                if (empty($search->manager) == false) {
                    $users->whereHas('manager', function ($q) use ($search) {
                        return $q->where('name', 'LIKE', "%{$search->manager}%")->orWhere('email', 'LIKE', "%{$search->manager}%");
                    });
                }
            }
        }
        return response()->json($users->paginate(10));
    }

    /**
     * @return JsonResponse
     */
    public function statistic(Request $request)
    {
        if (
            $request->user()->role === 'ROLE_ADMIN'
            || $request->user()->role == 'ROLE_WEBMASTER'
        ) {
            $source = [];
            $bidCollect = Bid::with('direction')
                ->with('user')
                ->where([
                    'is_launch' => true,
                    'is_delete' => false
                ])
                ->when($request->has('directionSort') && !empty($request->directionSort), function ($query) use ($request) {
                    return $query->where('direction_id', $request->directionSort)->groupByRaw('bids.user_id, bids.id');
                })
                ->when($request->has('regionSort') && !empty($request->regionSort) && $request->regionSort != 0, function ($query) use ($request) {
                    return $query->whereJsonContains('regions', [
                        ['id' => $request->regionSort]
                    ])->groupByRaw('bids.direction_id, bids.user_id, bids.id');
                })
                ->orderByDesc('consumption')
                ->get();

            $regions = [];
            $regions[0] = [];
            $source = [];
            $rgC = 1;

            foreach ($bidCollect as $cl) {
                if (count($cl->regions) > 0 && ($cl->consumption >= ceil($cl->direction->cost_price +
                    ($cl->direction->cost_price * ($cl->direction->extra / 100))))) {
                    $rgC = 1;
                    foreach ($cl->regions as $region) {
                        if ($request->has('regionSort')) {
                            $region['id'] = $region['id'] . $cl->direction->id;
                        }
                        if (isset($regions[$region['id']])) {
                            $regions[$region['id']]['LEAD_COUNT'] = $regions[$region['id']]['LEAD_COUNT'] + 1;
                            $regions[$region['id']]['balance'] = $regions[$region['id']]['balance'] + $cl->user->balance;
                            if ($regions[$region['id']]['MAX_RATE'] < $cl->consumption) {
                                $regions[$region['id']]['MAX_RATE'] = 0;
                            }
                            $regions[$region['id']]['AVG_RATE'] = $regions[$region['id']]['AVG_RATE'] + $cl->consumption;
                            $regions[$region['id']]['COUNT'] = $rgC;
                            $regions[$region['id']]['IDS'][] = $cl->id;
                        } else {
                            $regions[$region['id']] = [
                                'REGION_NAME' => $region['name'],
                                'LEAD_COUNT' => 0,
                                'balance' => $cl->user->balance,
                                'USERS_COUNT' => 1,
                                'MAX_RATE' => $cl->consumption,
                                'AVG_RATE' => $cl->consumption,
                                'LAST_DEAL_CREATE' => '02-09-2020 12:33',
                                'LAST_DEAL_DISTRIBUTION' => '02-09-2020 12:33',
                                'COUNT' => $rgC,
                                'direction' => $cl->direction
                            ];
                            $regions[$region['id']]['IDS'][] = $cl->id;
                        }
                    }
                } else {
                    $rgC = 1;
                    if (count($regions[0]) > 0) {
                        $regions[0]['IDS'][] =  $cl->id;
                        $regions[0]['LEAD_COUNT'] = $regions[0]['LEAD_COUNT'] + 1;
                        $regions[0]['balance'] = $regions[0]['balance'] + $cl->user->balance;
                        if (isset($regions[0]['MAX_RATE']) < $cl->consumption) {
                            $regions[0]['MAX_RATE'] = 0;
                        }
                        $regions[0]['AVG_RATE'] = $regions[0]['AVG_RATE'] + $cl->consumption;
                        $regions['COUNT'] = $rgC;
                    } else {
                        $regions[0] = [
                            'REGION_NAME' => 'Регион не определён',
                            'LEAD_COUNT' => 0,
                            'USERS_COUNT' => 1,
                            'MAX_RATE' => $cl->consumption,
                            'AVG_RATE' => $cl->consumption,
                            'LAST_DEAL_CREATE' => '02-09-2020 12:33',
                            'LAST_DEAL_DISTRIBUTION' => '02-09-2020 12:33',
                            'balance' => $cl->user->balance,
                            'COUNT' => $rgC,
                            'direction' => $cl->direction
                        ];
                        $regions[0]['IDS'][] = $cl->id;
                    }
                }
                $rgC++;
            }
            // if (count($regions) > 0 || count($regions[0]) > 0) {
            foreach ($regions as $region_id => $region) {
                if (is_array($region)) {
                    if (count($region) > 0) {
                        $region['USERS_COUNT'] = User::whereExists(function ($query) use ($region_id) {
                            return $query->select(\DB::raw(1))
                                ->from('bids')
                                ->whereRaw('bids.user_id = users.id')
                                ->where('bids.is_launch', true)
                                ->where(function ($q) use ($region_id) {
                                    return $q->whereJsonContains('regions', [
                                        ['id' => $region_id]
                                    ]);
                                });
                        })->count();
                        $region['AVG_RATE'] = $region['AVG_RATE'] == 0 ? 1 : $region['AVG_RATE'];
                        $region['COUNT'] = $region['COUNT'] == 0 ? 1 : $region['COUNT'];
                        $region['USERS_COUNT'] = $region['USERS_COUNT'] == 0 ? 1 : $region['USERS_COUNT'];

                        $region['AVG_RATE'] = ceil($region['AVG_RATE'] / $region['COUNT']) / $region['USERS_COUNT'];
                        $region['LEAD_COUNT'] = ceil($region['balance'] / $region['AVG_RATE']);

                        $region['budget'] = $region['direction']->cost_price * $region['LEAD_COUNT'];

                        $source[] = collect([
                            'IDS' => $region['IDS'],
                            'DIRECTION_NAME' => $region['direction']->name,
                            'REGION_NAME' => $region['REGION_NAME'],
                            'LEAD_COUNT' => $region['LEAD_COUNT'],
                            'USERS_COUNT' => $region['USERS_COUNT'],
                            'MAX_RATE' => $region['MAX_RATE'],
                            'AVG_RATE' => $region['AVG_RATE'],
                            'LAST_DEAL_CREATE' => $region['LAST_DEAL_CREATE'],
                            'LAST_DEAL_DISTRIBUTION' => $region['LAST_DEAL_DISTRIBUTION'],
                            'balance' => $region['balance'],
                            'budget' => $region['budget']
                        ]);
                    }
                }
            }
            $src = collect($source);
            $srcDat = null;
            switch ($request->order_by) {
                case 'DEF':
                    $srcDat = $src->sortBy($request->order_field);
                    break;
                case 'ASC':
                    $srcDat = $src->sortBy($request->order_field);
                    break;
                case 'DESC':
                    $srcDat = $src->sortByDesc($request->order_field);
                    break;
            }
            //            }
            return response()->json([
                'success' => true,
                'statistic' => [
                    'source' => $srcDat->values()->all(),
                    'general' => $this->getGeneralStatistic($request)
                ]
            ]);
        }
        return response('ДОСТУП ЗАПРЕЩЁН', 403);
    }

    protected function getGeneralStatistic(Request $request)
    {
        $bidNoPause = Bid::where([
            'is_launch' => true,
            'is_delete' => false
        ])->get();
        $bidUser = Bid::with('user')
            ->groupByRaw('bids.user_id, bids.id')
            ->get();
        $dayLeadGenerate = (($bidUser->sum('user.balance') / $bidUser->avg('consumption'))) / 3.2;
        $LastDealDistributionData = Deal::orderByDesc('updated_at')->first();
        $LastDealCreateData = Deal::orderByDesc('created_at')->first();
        $LastNoDistributionData = Deal::where('is_delete', false)
            ->whereHas('status', function ($q) {
                $q->where('type', 1004);
            })->first();

        if ($LastDealCreateData) {
            $LastDealCreate = Carbon::createFromFormat("Y-m-d H:i:s", $LastDealCreateData->created_at)->format("d-m-Y H:i:s");
        } else {
            $LastDealCreate = 'Нету';
        }

        if ($LastDealDistributionData) {
            $LastDealDistribution = Carbon::createFromFormat("Y-m-d H:i:s", $LastDealDistributionData->updated_at)->format("d-m-Y H:i:s");
        } else {
            $LastDealDistribution = 'Нету';
        }

        if ($LastNoDistributionData) {
            $LastNoDistribution = Carbon::createFromFormat("Y-m-d H:i:s", $LastDealCreateData->created_at)->format("d-m-Y H:i:s");
        } else {
            $LastNoDistribution = 'Нету';
        }

        $BIDS_USER_COUNT = User::whereExists(function ($query) {
            return $query->select(\DB::raw(1))
                ->from('bids')
                ->whereRaw('bids.user_id = users.id')
                ->where('bids.is_launch', true);
        })->count();
        $LEAD_COUNT = ceil($bidUser->sum('user.balance') / $bidUser->avg('consumption'));
        return [
            'DEALS_COUNT' => Deal::all()->count(),
            'API_APP_COUNT' => AppToken::all()->count(),
            'LEAD_COUNT' => $LEAD_COUNT,
            'AVG_COST_PRICE' => $bidUser->avg('direction.cost_price'),
            'BUDGET_MIN_LEAD_GENERATE' => $LEAD_COUNT * 400,
            'DAY_LEAD_GENERATE' => ceil($dayLeadGenerate),
            'BIDS_USER_COUNT' => $BIDS_USER_COUNT, //$bidUser->count(),
            'MAX_BID_RATE' => $bidNoPause->max('consumption'),
            'AVG_RATE' => ceil($bidNoPause->avg('consumption')),
            'BIDS_NO_PAUSE' => $bidNoPause->count(),
            'BIDS_ON_PAUSE' => Bid::where([
                'is_launch' => false,
                'is_delete' => false
            ])->get()->count(),
            'LAST_DEAL_CREATE' => $LastDealCreate,
            'LAST_DEAL_DISTRIBUTION' => $LastDealDistribution,
            'DEALS_NO_DISTRIBUTION' => $LastNoDistribution,
        ];
    }

    /**
     * @return JsonResponse
     */
    public function statisticGenerated(Request $request)
    {
        if ($request->user()->role == 'ROLE_ADMIN') {
            $Response = new stdObject([
                'received' => 0,
                'distributed' => 0,
                'distributedInsurance' => 0,
                'noDistributed' => 0,
                'returnedInsurance' => 0,
                'sumDistributed' => 0,
                'sumDistributedBonus' => 0,
                'sumReturned' => 0,
            ]);
            $Response->received = Deal::count();
            $Response->distributed = Deal::whereHas('status', function ($q) {
                return $q->whereNotIn('type', [1004]);
            })
                ->where('is_delete', false)
                ->count('id');
            $Response->distributedInsurance = Deal::whereHas('status', function ($q) {
                return $q->whereNotIn('type', [1004]);
            })
                ->where('is_insurance', true)
                ->where('is_delete', false)
                ->count('id');
            $Response->noDistributed = Deal::whereHas('status', function ($q) {
                return $q->where('type', 1004);
            })
                ->where('is_delete', false)
                ->count('id');
            $Response->returnedInsurance = Deal::whereHas('disput', function ($q) {
                return $q->where('status', Disput::STATUS_CONFIRMED);
            })->count();
            $Response->sumDistributed =  Deal::whereHas('status', function ($q) {
                return $q->whereNotIn('type', [1004]);
            })
                ->where('is_delete', false)
                ->sum('amount');

            $Response->sumDistributedBonus =  Deal::whereHas('status', function ($q) {
                return $q->whereNotIn('type', [1004]);
            })
                ->where('is_delete', false)
                ->sum('amount_bonus');
            $Response->sumReturned = Deal::whereHas('disput', function ($q) {
                return $q->where('status', Disput::STATUS_CONFIRMED);
            })->sum('amount');

            return response()->json($Response->toArray());
        }
    }

    /**
     * @return JsonResponse
     */
    public function meUser(Request $request)
    {
        return response()->json([
            'data' => User::with('region')->with('manager')->find($request->user()->id)
        ], 200);
    }

    public function updated(Request $request)
    {
        $Response = new stdObject([
            'deals_count' => 0,
            'type_name' => '',
            'msg_count' => 0,
            'disput_count' => 0,
            'distributed_count' => 0,
            'notifications' => [],
            'balance' => 0.0,
            'bonus' => 0.0,
        ]);

        $Response->balance = $request->user()->balance;
        $Response->bonus = $request->user()->bonus;
        $Response->deals_count = Deal::getCount($request->user()->role);
        $Response->type_name = Category::find($request->user()->category_id)->name;
        $Response->notifications = Notification::nonView($request->user()->id)->get();
        User::where('id', $request->user()->id)->update([
            'was_online' => Carbon::now() //->format("d-m-Y H:i:s")
        ]);
        Notification::updatedAllView($request->user()->id, true);

        if (
            $request->user()->role == 'ROLE_ADMIN'
            || $request->user()->role == 'ROLE_MANAGER'
        ) {
            $Response->disput_count = Disput::noClose()->count();
            $Response->distributed_count = Deal::noDistributed()->count();
        }

        return response()->json($Response, 200);
    }

    /**
     * Start Ver 1.0
     */

    /**
     * @return JsonResponse
     */
    public function payBonus(Request $request)
    {
        if ($request->has('is')) {
            $user = User::findOrFail($request->user()->id);
            $user->with_bonus = boolval($request->input('is'));
            $user->save();
            return response()->json([
                'with_bonus' => $user->with_bonus
            ]);
        }
    }

    /**
     * @return JsonResponse
     */
    public function history(Request $request)
    {
        $historyPayment = HistoryPayment::selectRaw("
            0 as type_transaction,
            SUM(paysum) AS paysum,
            SUM(paybonus) AS paybonus,
            (SELECT before_balance FROM payment_history hp WHERE hp.user_id = {$request->user()->id} ORDER BY hp.id ASC LIMIT 1) AS before_balance,
            (SELECT after_balance FROM payment_history hp WHERE hp.user_id = {$request->user()->id} ORDER BY hp.id DESC LIMIT 1) AS after_balance,
            SUM(before_bonus) AS before_bonus,
            SUM(after_bonus) AS after_bonus,
            to_char(payment_history.created_at, 'FMDD-MM-YYYY') AS date_at,
            to_char(payment_history.created_at, 'FMDD-MM-YYYY') as created_at,
            array_agg(id) AS key
        ")->where('user_id', intval($request->user()->id))
            ->orderBy('date_at')
            ->groupByRaw("date_at")
            ->paginate(20);

        foreach ($historyPayment->items() as $idx => $data) {
            $historyPayment->items()[$idx]['children'] = HistoryPayment::selectRaw("
                    type_transaction,
                    paysum,
                    paybonus,
                    before_balance,
                    after_balance,
                    before_bonus,
                    after_bonus,
                    to_char(payment_history.created_at, 'FMDD-MM-YYYY HH:MI') as date_at,
                    id AS key
                ")->where('user_id', intval($request->user()->id))
                // ->whereRaw('created_at = NOW()')
                ->whereRaw("to_char(payment_history.created_at, 'FMDD-MM-YYYY') = '{$data['date_at']}'")
                ->orderByDesc('date_at')->get();
        }
        return response()->json($historyPayment);
    }

    /**
     * @return JsonResponse
     */

    public function updateRegion(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'region_id' => ['required']
            ]);
            if (!$validator->fails()) {
                User::where('id', $request->user()->id)->update([
                    'region_id' => $request->region_id,
                ]);
                return response()->json([
                    'message' => 'Регион пользователя обновлён',
                    'region' => Region::find($request->region_id)
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Ошибка сервиса!'
            ], 400);
        }
    }

    /**
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        $user = User::find($request->user()->id);
        $errors = [];
        if ($user) {
            if ($request->has('name') && empty($request->name) == false && !empty($request->email)) {
                if ($request->has('name')) {
                    $user->name = $request->name;
                }
                if ($request->has('email')) {
                    $user->email = $request->email;
                }
                if ($request->has('emailNotification')) {
                    $user->email_notification = $request->emailNotification;
                }
                if ($request->has('phone')) {
                    $user->phone = $request->phone;
                }
                if ((empty($user->password) || $user->password == null) && $request->has('password_new')) {
                    $user->password = Hash::make($request->password_new);
                } elseif($request->has('password') && $request->has('password_new')) {
                    if (Hash::check($request->password, $user->password)) {
                        $user->password = Hash::make($request->password_new);
                    } else {
                        $errors['pass'] = 'Не правильный пароль';
                    }
                }
                if ($request->has('category_id')) {
                    $user->category_id = $request->category_id;
                }
                if ($request->has('region_id')) {
                    $user->region_id = $request->region_id;
                }
                $user->save();
                return response()->json([
                    'success' => true,
                    'message' => 'Настройки обновлены',
                    'errors' => $errors
                ]);
            }
            return response()->json([
                'success' => false,
                'error' => 'Поля заполнены не правильно'
            ]);
        }
        return response('ДОСТУП ЗАПРЕЩЁН', 403);
    }

    public function updateCategory(Request $request)
    {
        $Response = new stdObject([
            'success' => false,
            'message' => ''
        ]);
        $httpCode = 403;

        if ($request->has('type')) {
            User::find($request->user()->id)
                ->update([
                    'category_id' => $request->type
                ]);
            $httpCode = 200;
            $Response->success = true;
            $Response->message = 'Обновлено!';
        }
        return response()->json($Response, $httpCode);
    }
    /**
     * End Ver 1.0
     */
}
