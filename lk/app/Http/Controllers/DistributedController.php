<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Deal;
use App\Models\User;
use App\Models\Status;
use App\Models\Bid;

class DistributedController extends Controller
{
    //

    public function index(Request $request)
    {
        if ($request->user()->role === 'ROLE_ADMIN' || $request->user()->role === 'ROLE_WEBMASTER') {
            $distributed = Deal::select('*', 'deals.id AS deal_id')
                ->with('region')
                ->with('direction')
                ->with('status')
                ->whereHas('status', function ($q) {
                    $q->where('type', 1004);
                })->where('deals.is_delete', false)
                ->orderBy('deals.created_at', 'DESC')
                ->paginate(10);
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
                    return $query->when($deal->region->id, function ($q) use ($deal) {
                        return $q->whereJsonContains('regions', [
                            ['id' => $deal->region->id]
                        ]);
                    })->orWhere(function ($query) {
                        return $query->whereJsonLength('regions', 0);
                    });
                })->whereRaw("(select count(*) from
                            deals where bids.id = (deals.bid_id)
                            and deals.created_at = date_trunc('day', current_date)
                        ) < bids.daily_limit OR bids.daily_limit = 0");
            })
            ->whereExists(function ($query) {
                return $query->select(\DB::raw(1))
                    ->from('users')
                    ->whereRaw('bids.consumption <= users.balance');
            })
            ->where('is_launch', true)
            ->where('is_delete', false)
            ->orderByDesc('weight')->first();
            
            if ($bid) {
                $user = User::findOrFail($bid->user->id);
                $user->balance = $user->balance - $bid->consumption;
                if ($bid->insurance > 0) { //Если заявка по страховке
                    $bid->insurance = $bid->insurance - 1;
                    $deal->is_insurance = true;
                }
                if ($user->balance < $bid->consumption) {
                    $bid->is_launch = false;
                    $bid->save();
                }
                $user->save();

                $deal->bid_id = $bid->id;
                $deal->status_id = $status->id;
                $deal->amount = $bid->consumption;
                $deal->save();

                return response()->json([
                    'success' => true,
                    'msg' => 'Статус обновлён, заявка распределена'
                ]);
            }
            /**
             * END CAAP
             */
            $deal->status_id = $statusNo->id;
            $deal->save();
            return response()->json([
                'success' => false,
                'error' => 'Статус не обновлён, заявка не распределена'
            ]);
        }
        return response('ДОСУТП ЗАПРЕШЁН', 403);
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
