<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\HistoryPayment;
use App\Models\Payment;

use App\Helpers\HelperPayment;

class AdminController extends Controller
{
    //

    /**
     * @return JsonResponse
     */
    public function getManagers(Request $request)
    {
        if ($request->user()->role == 'ROLE_MANAGER' || $request->user()->role == 'ROLE_ADMIN') {
            $search_f = (object)$request->input('search');
            $userQuery = User::withCount('bids')->when($request->user()->role == 'ROLE_MANAGER', function ($q) use ($request) {
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
                $users->items()[$idx]['subtable'] = HistoryPayment::selectRaw('type_transaction,
                paysum,
                paybonus,
                before_balance,
                after_balance,
                before_bonus,
                after_bonus,
                `created_at`,
                DATE_FORMAT(`created_at`, "%Y-%m-%d %H:%i") as `date_at`,
                `id` AS `key`
            ')->where('user_id', $data['id'])
                    ->orderBy('created_at', 'DESC')->limit(15)->get();
            }
            return response()->json($users);
        }
        return response('Доступ запрещён!', 403);
    }

    /**
     * @return JsonResponse
     */
    public function getUser(Request $request)
    {
        if ($request->user()->role == 'ROLE_ADMIN' || $request->user()->role == 'ROLE_MANAGER') {
            $user = User::where('id', $request->user_id);
            if ($request->user()->role == 'ROLE_MANAGER') {
                $user->where('manager_id', $request->user()->id);
            }
            return response()->json($user->first());
        }
        return response('Доступ запрещён!', 403);
    }

    /**
     * @return JsonResponse
     */

    public function updateUser(Request $request)
    {

        if ($request->user()->role == 'ROLE_ADMIN' || $request->user()->role == 'ROLE_MANAGER') {
            $user = User::where('id', $request->user_id);
            if ($request->user()->role == 'ROLE_MANAGER') {
                $user->where('manager_id', $request->user()->id);
            }
            if ($request->user()->role == 'ROLE_ADMIN' || $request->user()->role == 'ROLE_MANAGER') {
                $updated = $request->update;
                $user = User::where('id', $request->user_id)->first();
                if (isset($updated['balance']) && $request->user()->role == 'ROLE_ADMIN') {
                    HistoryPayment::create([
                        'user_id' => $request->user_id,
                        'type_transaction' => '14',
                        'paysum' => $updated['balance'],
                        'paybonus' => 0,
                        'before_balance' => $user['balance'],
                        'after_balance' => $updated['balance'],
                        'before_bonus' => $user['bonus'],
                        'after_bonus' => $updated['balance']
                    ]);
                    Payment::create([
                        'type' => HelperPayment::TYPE_REN,
                        'status' => HelperPayment::STATUS_COMPLETE,
                        'requisites_id' => null,
                        'user_id' => $request->user_id,
                        'paysum' => $updated['balance'],
                        'before_balance' => $user['balance'],
                        'after_balance' => $updated['balance'],
                    ]);
                }
                if ($request->user()->role == 'ROLE_MANAGER') {
                    unset($updated['role']);
                    $user->where('manager_id', $request->user()->id);
                }
                $user->update($updated);
                return response()->json([
                    'success' => true,
                    'message' => 'Обновлено',
                    'user' => User::find($request->user_id)
                ]);
            }
            return response('Доступ запрещён!', 403);
            return response()->json($user->first());
        }
        return response('Доступ запрещён!', 403);
    }

    /**
     * @return JsonResponse
     */

    public function addUser(Request $request)
    {
        if ($request->user()->role == 'ROLE_ADMIN') {
            $user = User::create([
                'name' => time(),
                'balance' => 0.0,
                'bonus' => 0.0,
                'phone' => '',
                'role' => 'ROLE_USER'
            ]);
            return response()->json([
                'success' => true,
                'user_id' => $user->id
            ]);
        }
        return response('Доступ запрещён!', 403);
    }

    /**
     * @return JsonResponse
     */
    public function showUser(Request $request)
    {
        if ($request->user()->role == 'ROLE_ADMIN' || $request->user()->role == 'ROLE_MANAGER') {
            $user = User::where('id', $request->user_id);
            if ($request->user()->role == 'ROLE_MANAGER') {
                $user->where('manager_id', $request->user()->id);
            }
            return response()->json($user->first());
        }
        return response('Доступ запрещён!', 403);
    }
    /**
     * @return JsonResponse
     */
    public function deleteUser(Request $request)
    {
        if ($request->user()->role == 'ROLE_ADMIN' && $request->has('ids')) {
            $ids = [];
            foreach ($request->ids as $user) {
                $ids[] = $user['id'];
            }
            User::whereIn('id', $ids)->update(['is_delete' => true]);
            return response()->json([
                'success' => true
            ]);
        }
        return response('Доступ запрещён!', 403);
    }

    /**
     * @return JsonResponse
     */
    public function deleteActive(Request $request)
    {
        if ($request->user()->role == 'ROLE_ADMIN' && $request->has('ids')) {
            $ids = [];
            foreach ($request->ids as $user) {
                $ids[] = $user['id'];
            }
            User::whereIn('id', $ids)->update(['is_delete' => false]);
            return response()->json([
                'success' => true
            ]);
        }
        return response('Доступ запрещён!', 403);
    }

    /**
     * @return JsonResponse
     */
    public function blockedUser(Request $request)
    {
        if ($request->user()->role == 'ROLE_ADMIN' && $request->has('ids')) {
            $ids = [];
            foreach ($request->ids as $user) {
                $ids[] = $user['id'];
            }
            User::whereIn('id', $ids)->update(['is_block' => true]);
            return response()->json([
                'success' => true
            ]);
        }
        return response('Доступ запрещён!', 403);
    }

    /**
     * @return JsonResponse
     */
    public function unblockedUser(Request $request)
    {
        if ($request->user()->role == 'ROLE_ADMIN' && $request->has('ids')) {
            $ids = [];
            foreach ($request->ids as $user) {
                $ids[] = $user['id'];
            }
            User::whereIn('id', $ids)->update(['is_block' => false]);
            return response()->json([
                'success' => true
            ]);
        }
        return response('Доступ запрещён!', 403);
    }
}
