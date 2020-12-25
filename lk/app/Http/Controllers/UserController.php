<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Deal;
use App\Models\Disput;
use App\Models\Category;
use App\Models\Notification;
use App\Models\HistoryPayment;

use App\Helpers\stdObject;

class UserController extends Controller
{
    public function meUser(Request $request)
    {
        return response()->json([
            'data' => User::with('manager')->find($request->user()->id)
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
            'bonus' => 0.0
        ]);

        $Response->balance = $request->user()->balance;
        $Response->bonus = $request->user()->bonus;
        $Response->deals_count = Deal::getCount($request->user()->role);
        $Response->type_name = Category::find($request->user()->category_id)->name;
        $Response->notifications = Notification::nonView($request->user()->id)->get();
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
            $user->update(['with_bonus' => $request->input('is')]);
            return response()->json([
                'with_bonus' => $request->input('is')
            ]);
        }
    }

    /**
     * @return JsonResponse
     */
    public function history(Request $request)
    {
        $historyPayment = HistoryPayment::selectRaw('
            0 as type_transaction,
            SUM(paysum) AS paysum,
            SUM(paybonus) AS paybonus,
            (SELECT before_balance FROM payment_history hp WHERE hp.user_id = ' . intval($request->user()->id) . ' ORDER BY hp.id ASC LIMIT 1) AS before_balance,
            (SELECT after_balance FROM payment_history hp WHERE hp.user_id = ' . intval($request->user()->id) . ' ORDER BY hp.id DESC LIMIT 1) AS after_balance,
            SUM(before_bonus) AS before_bonus,
            SUM(after_bonus) AS after_bonus,
            created_at AS date_at,
            array_agg(id) AS key
        ')->where('user_id', intval($request->user()->id))
            ->orderBy('created_at', 'DESC')
            ->groupByRaw('payment_history.created_at')
            ->paginate(20);

        foreach ($historyPayment->items() as $idx => $data) {
            $historyPayment->items()[$idx]['children'] = HistoryPayment::selectRaw('type_transaction,
                    paysum,
                    paybonus,
                    before_balance,
                    after_balance,
                    before_bonus,
                    after_bonus,
                    created_at as date_at,
                    id AS key
                ')->where('user_id', intval($request->user()->id))
                ->whereRaw('created_at = NOW()')
                ->orderBy('date_at', 'DESC')->get();
        }
        return response()->json($historyPayment);
    }

    /**
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        $user = User::find($request->user()->id);
        if ($user) {
            if ($request->has('name') && empty($request->name) == false && !empty($request->email)) {
                if ($request->has('name')) {
                    $user->name = $request->name;
                }
                if ($request->has('email')) {
                    $user->email = $request->email;
                }
                if ($request->has('email_notification')) {
                    $user->email_notification = $request->email_notification;
                }
                if ($request->has('phone')) {
                    $user->phone = $request->phone;
                }
                $user->save();
                return response()->json([
                    'success' => true,
                    'msg' => 'Настройки обновлены'
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
