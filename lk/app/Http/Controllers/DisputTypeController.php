<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\DisputType;
use App\Models\Disput;

class DisputTypeController extends Controller
{
    //
    /**
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->user()->role == 'ROLE_ADMIN') {
            return response()->json(DisputType::orderByDesc('order_by')->get());
        }

        return response('Доступ запрещён!', 403);
    }

    /**
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        if ($request->user()->role == 'ROLE_ADMIN') {
            DisputType::create([
                'name' => ''
            ]);
            return response()->json([
                'success' => true
            ]);
        }

        return response('Доступ запрещён!', 403);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id)
    {
        if ($request->user()->role == 'ROLE_ADMIN') {
            if ($request->has('name')) {
                DisputType::find($id)->update([
                    'name' => $request->name,
                    'disput_type_id' => $request->disput_type_id,
                    'order_by' => $request->order_by
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Обновлено'
                ]);
            }
            return response()->json([
                'success' => true,
                'error' => 'Поле имя нужно ввести'
            ]);
        }

        return response('Доступ запрещён!', 403);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function delete(Request $request, int $id)
    {
        if ($request->user()->role == 'ROLE_ADMIN') {
            Disput::where('disput_type_id', $id)->update(['disput_type_id' => 5]);
            DisputType::find($request->id)->delete();
            return response()->json([
                'succes' => true
            ]);
        }

        return response('Доступ запрещён!', 403);
    }
}
