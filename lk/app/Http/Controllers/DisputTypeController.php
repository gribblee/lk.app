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
            return response()->json(DisputType::all());
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
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        if ($request->user()->role == 'ROLE_ADMIN') {
            if ($request->has('name') && $request->has('id')) {
                DisputType::find($request->id)->update([
                    'name' => $request->name
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
     * @return JsonResponse
     */
    public function delete(Request $request)
    {
        if ($request->user()->role == 'ROLE_ADMIN') {
            if ($request->has('id')) {
                Disput::where('disput_type_id', $request->id)->update(['disput_type_id' => 5]);
                DisputType::find($request->id)->delete();
                return response()->json([
                    'succes' => true
                ]);
            }
        }

        return response('Доступ запрещён!', 403);
    }
}
