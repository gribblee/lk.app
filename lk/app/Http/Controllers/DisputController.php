<?php

namespace App\Http\Controllers;

use App\Models\Disput;
use App\Models\DisputType;
use App\Models\Notification;
use App\Models\Status;

use Illuminate\Http\Request;


class DisputController extends Controller
{
    /**
     * @return JsonResponse
     */

    public function index(Request $request)
    {
        if ($request->user()->role == 'ROLE_ADMIN' || $request->user()->role == 'ROLE_MODERATOR') {
            $disputs = Disput::where('status', Disput::STATUS_START)
                ->orderBy('created_at', 'DESC')
                ->paginate(15);

            return response()->json($disputs);
        }
        return response()->json([
            'success' => false,
            'error' => 'Доступ запрещён!'
        ], 403);
    }

    /**
     * @return JsonResponse
     */
    public function info(Request $request)
    {
        $disput_type = DisputType::get();
        return response()->json([
            'success' => true,
            'disput_type' => $disput_type
        ], 200);
    }

    /**
     * @return JsonResponse
     */
    public function close(Request $request, int $id)
    {
        if ($request->user()->role == 'ROLE_ADMIN' || $request->user()->role == 'ROLE_WEBMASTER' || $request->user()->role == 'ROLE_MODERATOR') {
            $disput = Disput::where('id', $id)
                ->with('deal', 'deal.bids', 'deal.bids.user')
                ->first();
            switch ($request->status) {
                case 2006:
                    Notification::create([
                        'description' => "Заявка #{$disput->deal->id} не соответствует критериям некачественной, поэтому мы вернули её Вам",
                        'user_id' => $disput->deal->bids->user->id
                    ]);
                    $status = Status::where('type', 1000)->first();
                    $disput->status = Disput::STATUS_REJECT;
                    $disput->deal->status_id = $status->id ?? null;
                    $disput->deal->is_view = false;
                    $disput->deal->save();
                    break;
                case 2010:
                    $disput->status = Disput::STATUS_CONFIRMED;
                    $disput->deal
                        ->bids
                        ->user
                        ->update([
                            'bonus' => $disput->deal->bids->user->bonus += $disput->deal->bids->rate
                        ]);
                    Notification::create([
                        'description' => "Спор по заявке #{$disput->deal->id} закрыт в Вашу пользу, мы вернули {$disput->deal->bids->rate} руб. на Ваш бонусный счёт",
                        'user_id' => $disput->deal->bids->user->id
                    ]);
                    $disput->deal->is_delete = true;
                    $disput->deal->save();
                    break;
            }
            $disput->close = true;
            $disput->save();
            return response()->json([
                'success' => true,
            ]);
        }
        return response()->json([
            'success' => false,
            'error' => 'ACCESS DENIED'
        ], 403);
    }
}
