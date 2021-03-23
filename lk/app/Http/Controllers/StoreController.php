<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Store;
use App\Models\StoreOrder;
use App\Models\User;

class StoreController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        return response()->json(Store::orderByDesc('id')->paginate(10));
    }

    public function store(Request $request)
    {
        if ($request->user()->role == 'ROLE_ADMIN') {
            $order = Store::create($request->only([
                'title',
                'short_description',
                'description',
                'tags',
                'price'
            ]));
            return $order;
        }
    }

    /**
     * @param Request $request
     * @param $orderId
     * @return JsonResponse
     */
    public function view(Request $request, $orderId)
    {
        return Store::findOrFail($orderId);
    }

    /**
     * @param Request $request
     * @param $orderId
     * @return JsonResponse
     */
    public function orderBuy(Request $request, $orderId)
    {
        $user = User::find($request->user()->id);
        $order = Store::find($orderId);
        if ($user->balance >= $order->price)
        {
            $user->balance = ceil($user->balance - $order->price);
            StoreOrder::create([
                'user_id' => $user->id,
                'store_id' => $order->id
            ]);
            return response()->json([
                'success' => true,
                'message' => "Товар \"{$order->title}\" приобретён"
            ]);
        } 
        return response()->json([
            'success' => false,
            'message' => 'Недостаточно средств'
        ]);
    }

    /**
     * @param Request $request
     * @param $orderId
     * @return JsonResponse
     */
    public function delete(Request $request, $orderId)
    {
    }
}
