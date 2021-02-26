<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\HistoryPayment;
use App\Models\User;
use App\Models\Bid;
use Exception;

class ManagerController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->user()->role === 'ROLE_ADMIN') {
            return response()->json(User::where([
                'role' => 'ROLE_MANAGER',
                'is_delete' => false
            ])->get());
        }
    }


    /**
     * @return JsonResponse
     */
    public function getLeads(Request $request)
    {
        if ($request->user()->role == 'ROLE_ADMIN' || $request->user()->role == 'ROLE_MANAGER') {
            try {
                return User::with(['category', 'region'])->where([
                    'manager_id' => null,
                    'role' => 'ROLE_USER'
                ])->orderByDesc('created_at')->paginate(10);
            } catch (Exception $e) {
                return response()->json([
                    'message' => "Ошибка!"
                ], 500);
            }
        }
    }

    /**
     * @param $userId
     * @return JsonResponse
     */
    public function takeLead(Request $request, int $userId)
    {
        if ($request->user()->role == 'ROLE_ADMIN' || $request->user()->role == 'ROLE_MANAGER') {
            try {
                $lead = User::where('manager_id', null)->findOrFail($userId);
                $lead->update([
                    'manager_id' => $request->user()->id
                ]);
            } catch (Exception $e) {
                return response()->json([
                    'message' => 'Не найдено'
                ], 404);
            }
        }
    }

    /**
     * @return JsonResponse
     */
    public function getUsers(Request $request)
    {
        if ($request->user()->role == 'ROLE_MANAGER' || $request->user()->role == 'ROLE_ADMIN') {
            $search_f = (object)$request->input('search');
            $userQuery = User::selectRaw("*, to_char(created_at, 'FMDD-MM-YYYY HH:MI') AS registration_at, to_char(created_at, 'FMDD-MM-YYYY HH:MI') AS was_online")->withCount('bids')->when($request->user()->role == 'ROLE_MANAGER', function ($q) use ($request) {
                return $q->where('manager_id', $request->user()->id);
            })->when($request->has('order_by'), function ($q) use ($request) {
                if ($request->order_by != 'DEF') {
                    return $q->orderBy('users.balance', strtoupper($request->order_by));
                }
            })
                ->when($request->has('order_by_register'), function ($q) use ($request) {
                    if ($request->order_by_register != 'DEF') {
                        return $q->orderBy('users.created_at', strtoupper($request->order_by_register));
                    }
                });
            if (empty($search_f->name) === false) {
                $userQuery->where('name', 'LIKE', "%{$search_f->name}%");
            }
            if (empty($search_f->email) === false) {
                $userQuery->where('email', 'LIKE', "%{$search_f->email}%");
            }
            if (empty($search_f->phone) === false) {
                $userQuery->where('phone', 'LIKE', "%{$search_f->phone}%");
            }
            $users = $userQuery->orderBy('id', 'DESC')->paginate(10);
            foreach ($users->items() as $idx => $data) {
                $users->items()[$idx]['subtable'] = HistoryPayment::selectRaw("type_transaction,
                paysum,
                paybonus,
                before_balance,
                after_balance,
                before_bonus,
                after_bonus,
                created_at,
                to_char(payment_history.created_at, 'FMDD.MM.YYYY') AS date_at,
                id AS key
            ")->where('user_id', $data['id'])
                    ->orderBy('created_at', 'DESC')->limit(15)->get();
            }
            return response()->json($users);
        }
        return response('Доступ запрещён!', 403);
    }

    /**
     * @param $bidId
     * @return JsonResponse
     */
    public function updateBidUser(Request $request, int $bidId)
    {
        try {
            $bid = Bid::findOrFail($bidId);
            $user = User::findOrFail($request->user_id);
            $bid->update([
                'user_id' => $user->id
            ]);
            return response()->json($user);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Ошибка запроса!'
            ], 400);
        }
    }
}
