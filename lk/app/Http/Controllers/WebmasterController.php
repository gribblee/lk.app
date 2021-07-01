<?php

namespace App\Http\Controllers;

use App\Models\AppToken;
use App\Models\Direction;
use App\Models\Requisite;

use Illuminate\Http\Request;
use Dirape\Token\Token;


class WebmasterController extends Controller
{

    /**
     * @return JsonResponse
     */
    public function tokenLoad(Request $request)
    {
        if (
            $request->user()->role === 'ROLE_WEBMASTER'
            || $request->user()->role === 'ROLE_ADMIN'
        ) {
            return response()->json(
                AppToken::with('direction')->when($request->role == 'ROLE_WEBMASTER', function ($q) use ($request) {
                    return $q->where('user_id', $request->user()->id);
                })->get(),
                200
            );
        }
    }

    /**
     * @return JsonResponse
     */
    public function tokenCreate(Request $request)
    {
        if ($request->has('directionId')) {
            $direction = Direction::findOrFail($request->directionId);
            AppToken::create([
                'user_id' => $request->user()->id,
                'hash' => (new Token())->Unique('api_token', 'hash', '40'),
                'count_deals' => 0,
                'direction_id' => $direction->id
            ]);
            return response()->json([
                'message' => 'Создано'
            ], 201);
        }
        return response()->json([
            'message' => 'Укажите направление'
        ], 400);
    }

    /**
     * @return JsonResponse
     */
    public function tokenDelete(Request $request)
    {
        if ($request->user()->role === 'ROLE_WEBMASTER' || $request->user()->role === 'ROLE_ADMIN') {
            $deleteIds = [];
            foreach ($request->selectedRows as $row) {
                $deleteIds[] = $row['id'];
            }
            AppToken::whereIn('id', $deleteIds)->delete();
            return response()->json(
                [
                    'message' => 'Удалено'
                ],
                200
            );
        }
        return response()->json([
            'message' => 'Доступ запрещён'
        ], 403);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function meRequisite(Request $request)
    {
    }

    /** Создать реквизиты
     * @param Request $request
     * @return JsonResponse
     */
    public function createRequisite(Request $request)
    {
    }

    /** Обновить реквизиты
     * @param Request $request
     * @return JsonResponse
     */
    public function updateRequisite(Request $request, $id)
    {
    }

    /** Удалить реквизиты
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteRequisite(Request $request)
    {
    }
}
