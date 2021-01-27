<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\Bid;
use App\Models\Deal;
use App\Models\Direction;
use App\Models\Region;
use App\Models\Insurance;
use App\Models\User;

use App\Helpers\stdObject;

class BidController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        /**
         * Start Ver 1.0
         */
        $bids = Bid::select(
            '*',
            'bids.id as id',
            'bids.consumption AS max_rate',
            'bids.is_launch as status',
            'bids.daily_limit as day_limit'
        )
            ->when($request->user()->role === 'ROLE_USER', function ($query) use ($request) {
                return $query->where('user_id', $request->user()->id);
            })
            ->when($request->user()->role === 'ROLE_MANAGER', function ($query) use ($request) {
                return $query->whereHas('user', function ($qw) use ($request) {
                    return $qw->where('manager_id', $request->user()->id);
                });
            })
            ->where('is_delete', false)
            ->with('direction')
            ->withCount('deals');
        if ($request->has('status')) {
            switch ($request->status) {
                case 1:
                    $bids->where('is_launch', true);
                    break;
                case 2:
                    $bids->where('is_launch', false);
                    break;
                case 3:
                    $bids->selectRaw('(SELECT COUNT(*) FROM deals WHERE deals.bid_id = bids.id) AS MAX_DEALS')->orderBy('MAX_DEALS', 'DESC')->where('is_launch', true);
                    break;
                case 4:
                    $bids->selectRaw('((SELECT COUNT(*) FROM deals WHERE deals.bid_id = bids.id) * bids.consumption) AS AVG_DEALS')->orderBy('AVG_DEALS', 'ASC')->where('is_launch', true);
                    break;
            }
        }

        $bidsData = $bids->where('category_id', $request->user()->category_id)->orderBy('bids.created_at', 'DESC')->paginate(10);

        return response()->json([
            'balance' => $request->user()->balance,
            'statistic' => [
                'AVG_RATE' => Bid::avgConsumption(
                    $request->user()->id,
                    $request->user()->category_id
                ),
                'AVG_OLD_RATE' => Bid::avgConsumption(
                    $request->user()->id,
                    $request->user()->category_id,
                    true
                ),
                'DEALS_COUNT' => Bid::dealsCount(
                    $request->user()->id,
                    $request->user()->category_id
                ),
                'DEALS_OLD_COUNT' => Bid::dealsCount(
                    $request->user()->id,
                    $request->user()->category_id,
                    true
                ),
                'SPENT_TODAY' => Bid::spentDeals(
                    $request->user()->id,
                    $request->user()->category_id
                ),
                'SPENT_OLDDAY' => 0,
            ],
            'bids' => $bidsData
        ], 200);
        /**
         * End Ver 1.0
         */
    }

    /**
     * @return JsonResponse
     */
    public function show(Request $request, int $id)
    {
        $bid = Bid::where('id', $id)
            ->where('is_delete', false)
            ->with('direction')
            ->with('user')
            ->when(
                $request->user()->role === 'ROLE_USER',
                function ($query) use ($request) {
                    return $query->where('user_id', $request->user()->id);
                }
            )->when($request->user()->role === 'ROLE_MANAGER', function ($query) use ($request) {
                return $query->whereHas('user', function ($qw) use ($request) {
                    return $qw->where('manager_id', $request->user()->id)->orWhere('id', $request->user()->id);
                });
            })->firstOrFail();
        $bidRecommend = Bid::selectRaw('coalesce((FLOOR(MAX(bids.consumption) + (MAX(bids.consumption) * 0.05))), 0) AS max_rate')->where('is_launch', true)
            ->where('is_delete', false)
            ->whereNotIn('id', [$id])
            ->when($bid->all_region == false, function ($q) use ($id, $bid, $request) {
                $regionSort = [];
                foreach ($bid->regions as $region) {
                    $regionSort[]['id'] = $region['id'];
                }
                return $q->whereJsonContains('regions', $regionSort);
            })->first();
        if ($bidRecommend['max_rate'] == -1) {
            $bid['max_rate'] = $bid['direction']['cost_price'] + ($bid['direction']['cost_price'] * ($bid['direction']['extra'] / 100) + ($bid['direction']['cost_price'] * 0.05));
        } else {
            $bid['max_rate'] = $bidRecommend['max_rate'];
        }
        $bid['direction_description'] = $bid->direction->description;

        // $options_buff = Options::all();
        // $options = [];
        // foreach($options_buff as $option) {
        //     $options[$option->name] = $option->value;
        // }
        $bid['options'] = [
            'conversion_contract' => $bid->direction['conversion_contract'],
            'conversion_meetings' => $bid->direction['conversion_meetings'],
            'average_check' => $bid->direction['average_check'],
            'min_per_rate' => $bid->direction['cost_price'] + ($bid->direction['cost_price'] * ($bid->direction['extra'] / 100)),
        ];
        return response()->json($bid, 200);
    }

    /**
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        /**
         * Start Ver 1.0
         */
        $bidsColumns = Bid::selectRaw('direction_id')
            ->where('bids.category_id', $request->user()->category_id)
            ->groupBy('direction_id')
            ->where('bids.user_id', $request->user()->id)
            ->where('bids.is_delete', false)
            ->whereHas('direction', function ($q) use ($request) {
                return $q->whereJsonContains('categories', $request->user()->category_id);
            })
            ->get();
        $bidsDirection = [];
        foreach ($bidsColumns as $bca) {
            $bidsDirection[] = $bca->direction_id;
        }
        $direction = Direction::whereNotIn('id', $bidsDirection)->where('categories', 'LIKE', "%{$request->user()->type}%")->get();
        if (count($direction) > 0) {
            $drt = $direction[mt_rand(0, count($direction) - 1)];

            $bid = Bid::create([
                'direction_id' => $drt->id,
                'category_id' => $request->user()->category_id,
                'regions' => [],
                'user_id' => $request->user()->id,
                'consumption' => $drt->cost_price + ($drt->cost_price * ($drt->extra / 100)),
                'is_launch' => false,
                'is_notification' => false,
                'daily_limit' => 0,
                'is_delete' => false,
                'insurance' => 0
            ]);
            return response()->json([
                'STATUS' => 'OK',
                'ID' => $bid->id
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Нельзя больше создавать заявки'
            ]);
        }
        /**
         * End Ver 1.0
         */
    }

    /**
     * @return JsonResponse
     */
    public function launch(Request $request, int $id)
    {
        $bid = Bid::with('user')->with('direction')->findOrFail($id);
        $bid->is_launch = !$bid->is_launch;
        $msg = '';
        $isLaunch = $bid->is_launch;
        $code = 0;
        if ($bid) {
            if ($bid->user->balance < $bid->consumption && $bid->is_launch == true) {
                $isLaunch = false;
                $msg = 'Недостаточно средств';
                $code = 1004;
            } elseif ($bid->user->balance < $bid->consumption && $bid->is_launch == false) {
                $bid->is_launch = false;
                $isLaunch = false;
                $bid->save();
                $code = 1000;
            } elseif ($bid->user->balance >= $bid->consumption) {
                $minRate = $bid->direction->cost_price + ($bid->direction->cost_price * ($bid->direction->extra / 100)); // + ($bid->direction->cost_price * 0.05);
                if ($bid->consumption < $minRate || ($bid->day_limit > 0 && $bid->day_limit < 5)) {
                    $bid->is_launch = false;
                    $isLaunch = false;
                    $bid->save();
                    $msg = 'При такой ставке вы не будете получатья заявки';
                    $code = 1003;
                } else {
                    $bid->save();
                    $isLaunch = $bid->is_launch;
                    $msg = '';
                    $code = 1000;
                }
            }
        } else {
            $isLaunch = false;
            $msg = 'Ошибка доступа';
            $code = 2003;
        }
        return response()->json([
            'is_launch' => $isLaunch,
            'message' => $msg,
            'code' => $code
        ]);
    }

    public function buyInsurance(Request $request, int $id)
    {
        $Response = new stdObject([
            'success' => false,
            'message' => 'Вы не выбрали страховку',
            'data' => []
        ]);
        if ($request->has('insurance_id')) {
            $insurance = Insurance::findOrFail($request->insurance_id);

            if ($request->user()->balance < $insurance->price) {
                $Response->success  = false;
                $Response->message = 'Не достаточно средств';
                $Response->data = [];
            } else {
                $bid = Bid::find($id)->where('user_id', $request->user()->id);
                if ($bid) {
                    $user = User::findOrFail($request->user()->id);
                    $bid->insurance = $bid->insurance + $insurance->price;
                    $bid->update();

                    $user->balance = $user->balance - $insurance->price;
                    $user->update();

                    $Response->success = true;
                    $Response->message = 'Обновлено';
                    $Response->data = $bid;
                } else {
                    $Response->success = false;
                    $Response->message = 'Это не ваша заявка';
                    $Response->data = [];
                }
            }
        }
        return response()->json($Response, 200);
    }

    /**
     * @return JsonResponse
     */
    public function update(Request $request, int $id)
    {
        //Здесь куча хуйни всё поправить
        $bid = Bid::with('direction')->when(
            $request->user()->role === 'ROLE_USER',
            function ($query) use ($request) {
                return $query->where('user_id', $request->user()->id);
            }
        )->when($request->user()->role === 'ROLE_MANAGER', function ($query) use ($request) {
            return $query->whereHas('user', function ($qw) use ($request) {
                return $qw->where('manager_id', $request->user()->id)->orWhere('id', $request->user()->id);
            });
        })->findOrFail($id);
        $qOnly = $request->only([
            'regions',
            'direction_id',
            'consumption',
            'daily_limit',
            'insurance'
        ]);
        if ($request->has('daily_limit')) {
            if ($qOnly['daily_limit'] > 0 && $qOnly['daily_limit'] < 5) {
                return response()->json([
                    'success' => false,
                    'message' => 'Нужно чтобы заявок было не меньше 5'
                ]);
            }
        }
        $bid->update($qOnly);

        $bidReturn = Bid::with('direction')->with('user')->find($id);
        $bidRecommend = Bid::selectRaw('coalesce((FLOOR(MAX(bids.consumption) + (MAX(bids.consumption) * 0.05))), 0) AS max_rate')
            ->where('is_launch', true)
            ->whereNotIn('id', [$bid->id])
            ->where('is_delete', false)
            ->whereNotIn('id', [$bid->id])
            ->when($bid->all_region == false, function ($q) use ($id, $bid, $request) {
                $regionSort = [];
                foreach ($bid->regions as $region) {
                    $regionSort[]['id'] = $region['id'];
                }
                return $q->whereJsonContains('regions', $regionSort);
            })->first();

        if ($bidRecommend['max_rate'] == -1) {
            $bidReturn['max_rate'] = $bidReturn['direction']['cost_price'] + ($bidReturn['direction']['cost_price'] * ($bidReturn['direction']['extra'] / 100) + ($bidReturn['direction']['cost_price'] * 0.05));
        } else {
            $bidReturn['max_rate'] = $bidRecommend['max_rate'];
        }
        return response()->json([
            'success' => true,
            'message' => 'Обновлено',
            'data' => $bidReturn
        ]);
        return response()->json([
            'success' => false
        ], 403);
    }

    /**
     * @return JsonResponse
     */
    public function delete(Request $request, int $id)
    {
        $bid = Bid::find($id);
        $bid->is_delete = true;
        $bid->save();
        return response(null, Response::HTTP_OK);
    }

    /**
     * Start Ver 1.0
     */

    public function updateRegion(Request $request, int $bidId)
    {
        $Response = new stdObject([
            'success' => false,
            'message' => ''
        ]);
        $bidData = Bid::where('user_id', $request->user()->id)
            ->where('id', $bidId)
            ->first();
        if ($bidData && $request->has('regions')) {
            $Response->success = true;
            $Response->message = 'Обновлено';

            $bidData->update($request->only('regions'));
        } else {
            $Response->success = false;
            $Response->message = 'Это не ваша заявка или такой не существует';
        }

        return response()->json($Response, 200);
    }

    public function actionUpdate(Request $request)
    {
        if ($request->has('ids')) {
            $bids = Bid::whereIn('id', $request->ids);
            if ($request->has('action')) {
                switch ($request->action) {
                    case 1:
                        $bids->update(['is_launch' => false]);
                        break;
                    case 2:
                        $bids->update(['is_launch' => true]);
                        break;
                    case 3:
                        $bids->update(['is_delete' => true]);
                        break;
                }
                return response()->json([
                    'success' => true,
                    'message' => 'Обновлено',
                ]);
            }
        }
    }
    /**
     * End Ver 1.0
     */
}
