<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Store;
use App\Models\StoreOrder;
use App\Models\User;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Option;

use Illuminate\Support\Facades\Log;

class StoreController extends Controller
{
    protected $validationRules = [
        'title' => 'required|string|min:3|max:255',
        'short_description' => 'required',
        'description' => 'required',
        'tags' => 'nullable|string',
        'price' => 'nullable|string',
        'images.*' => 'nullable',
    ];
    protected $options = [];

    function __construct()
    {
        $this->options = Option::getKeyValue();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $onPage = 20;
        $page = (int) $request->page ?? 1;

        $items = Store::orderByDesc('id')
            ->where('is_delete', false)
            ->offset(
                ($page - 1) * $onPage
            )->limit($onPage)->get();
        $items->map(function ($item) {
            $item->images = json_decode($item->images, true) ?? [];
        });
        return response()->json($items);
    }

    public function upload(Request $request)
    {
        if ($request->hasFile('file')) {
            return $request->file->store(
                implode(DIRECTORY_SEPARATOR, ['images', 'store', Carbon::now()->format('Ymd')]),
                'public'
            );
        }
    }

    public function store(Request $request)
    {
        if ($request->user()->role == 'ROLE_ADMIN') {
            try {
                $validator = Validator::make(
                    $request->only('title', 'short_description', 'description', 'tags', 'images', 'price'),
                    $this->validationRules
                );

                if ($validator->fails())
                    return response()->json(['errors' => $validator->errors()], 422);
                $images = [];
                if ($request->has('images')) {
                    $images = $request->images;
                }
                $order = Store::create([
                    'title' => $request->title,
                    'short_description' => $request->short_description,
                    'description' => $request->description,
                    'tags'  => $request->tags,
                    'price' => $request->price,
                    'images' => json_encode($images, JSON_UNESCAPED_UNICODE),
                ]);
                return response()->json($order);
            } catch (Exception $e) {
                Log::info("Order.Create.Error: " . json_encode($e->getMessage()));
                return response()->json([
                    'message' => 'Ошибка'
                ], 422);
            }
        }
    }

    /**
     * @param Request $request
     * @param $orderId
     * @return JsonResponse
     */
    public function view(Request $request, $orderId)
    {
        $order = Store::findOrFail($orderId);
        $order->images = json_decode($order->images, JSON_UNESCAPED_UNICODE);
        $order->recomended = $this->recomended($order);
        return response()->json($order);
    }

    protected function recomended($order)
    {
        $tags = collect(explode(",", trim($order->tags)));
        $orderRecomended = collect([]);
        $orderNotIds = [$order->id];
        $tags->each(function ($tag) use ($orderRecomended, $orderNotIds) {
            $tagInOrder = trim($tag);
            $tagOrder = Store::where('tags', 'LIKE', "%{$tagInOrder}%")
                ->whereNotIn('id', $orderNotIds)
                ->orderByDesc('id')
                ->limit(5)
                ->get();
            if (!empty($tagOrder) && $tagOrder != null && count($tagOrder) > 0) {
                $tagOrder->each(function ($tagNw) use ($orderRecomended, $orderNotIds) {
                    if (!array_key_exists($tagNw->id, $orderNotIds)) {
                        $orderNotIds[] = $tagNw->id;
                        $tagNw->images = json_decode($tagNw->images, JSON_UNESCAPED_UNICODE);
                        $orderRecomended->push($tagNw);
                    }
                });
            }
        });
        return $orderRecomended;
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
        if ($user->balance >= $order->price) {
            $bonus = ceil($order->price + (($order->price / 100) * $this->options->bonus_store));
            if ($user->with_bonus && $user->bonus >= $bonus) {
                $user->balance = ceil($user->balance - ($order->price - $bonus));
                $user->bonus = ceil($user->bonus - $bonus);
            } else {
                $user->balance = ceil($user->balance - $order->price);
            }
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
    public function orderDelete(Request $request, $orderId)
    {
        if ($request->user()->role === 'ROLE_ADMIN') {
            Store::where('id', $orderId)->update(['is_delete' => true]);
            return response()->json([
                'success' => true,
                'message' => "Товар  удалён"
            ]);
        }
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
