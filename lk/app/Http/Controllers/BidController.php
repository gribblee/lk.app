<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\Bid;
use App\Models\Deal;
use App\Models\Direction;
use App\Models\Region;

use App\Helpers\stdObject;

class BidController extends Controller
{

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
            ->where('user_id', $request->user()->id)
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

    public function show(Request $request, int $id)
    {
        $bid = Bid::where('id', $id)
            ->where('is_delete', false)
            ->with('direction')
            ->firstOrFail();

        $bidRecommend = Bid::selectRaw('(FLOOR(MAX(bids.consumption) + (MAX(bids.consumption) * 0.05))) AS MAX_RATE')->where('is_launch', true)
            ->where('is_delete', false)
            ->when($bid->all_region == false, function ($q) use ($id, $bid, $request) {
                $regionSort = [];
                foreach ($bid->regions as $region) {
                    $regionSort[] = $region['id'];
                }
                return $q->whereJsonContains('regions', $regionSort);
            })->first();
        if ($bidRecommend['MAX_RATE'] == -1) {
            $bid['MAX_RATE'] = $bid['direction']['cost_price'] + ($bid['direction']['cost_price'] * ($bid['direction']['extra'] / 100) + ($bid['direction']['cost_price'] * 0.05));
        } else {
            $bid['MAX_RATE'] = $bidRecommend['MAX_RATE'];
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
                $minRate = $bid->direction->cost_price + ($bid->direction->cost_price * ($bid->direction->extra / 100)) + ($bid->direction->cost_price * 0.05);
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
            'msg' => $msg,
            'code' => $code
        ]);
    }

    public function buyInsurance(Request $request, int $id)
    {
    }

    public function rateFixRegions(Request $request, int $id)
    {
    }

    public function update(Request $request, int $id)
    {
        $bid = Bid::with('direction')->findOrFail($id);
        $bidRecommend = Bid::selectRaw('(MAX(bids.consumption) + (MAX(bids.consumption) * 0.05)) AS MAX_RATE')->where('is_launch', true)
            ->where('is_delete', false)
            ->when($bid->all_region == false, function ($q) use ($id, $bid, $request) {
                $regionSort = [];
                foreach ($bid->regions as $region) {
                    $regionSort[] = $region['id'];
                }
                return $q->whereJsonContains('regions', $regionSort);
            })->first()->first();

        if ($bid->user_id === $request->user()->id) {
            if (isset($request->perRate)) {
                $bid->consumption = $request->perRate;
                if ($request->perRate < $bid->direction['cost_price'] + ($bid->direction['cost_price'] * ($bid->direction['extra'] / 100))) {
                    $bid->is_launch = false;
                }
            }
            if ($request->has('dailyLimit')) {
                if ($request->dailyLimit < 0 || empty($request->dailyLimit)) {
                    $request->dailyLimit = 0;
                }
                $bid->daily_limit = $request->dailyLimit;
                if ($request->dailyLimit > 0 && $request->dailyLimit < 5) {
                    $bid->is_launch = false;
                }
            }
            if (isset($request->direction_id) && $bid->is_update == false) {
                $bid->direction_id = $request->direction_id;
                $bid->is_update = true;
            }
            $bid->save();
            return response()->json([
                'success' => true,
                'MAX_RATE' => $bidRecommend['MAX_RATE'],
                'direction_description' => $bid->direction->description,
                'conversion_contract' => $bid->direction['conversion_contract'],
                'conversion_meetings' => $bid->direction['conversion_meetings'],
                'average_check' => $bid->direction['average_check'],
                'min_per_rate' => $bid->direction['cost_price'] * ($bid->direction['cost_price'] * ($bid->direction['extra'] / 100)),
                'direction' => $bid->direction

            ], 200);
        }
        return response()->json([
            'success' => false
        ], 403);
    }

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
