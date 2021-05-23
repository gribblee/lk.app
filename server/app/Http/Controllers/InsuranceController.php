<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Insurance;

class InsuranceController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $insurance = [];
        if ($request->user()->role == 'ROLE_ADMIN') {
            $insurance = Insurance::orderBy('id', 'DESC')->get();
        } else {
            $insurance = Insurance::where('status', 'Y')->orderBy('id', 'DESC')->get();
        }
        return response()->json([
            'success' => true,
            'data' => $insurance
        ]);
    }

    public function create(Request $request)
    {
        if ($request->user()->role == 'ROLE_ADMIN') {
            Insurance::create([
                'name' => '',
                'price' => 0,
                'count' => 0
            ]);
            return response()->json([
                'success' => true
            ]);
        }
        return response('Доступ запрещён!', 403);
    }

    public function update(Request $request)
    {
        if ($request->user()->role == 'ROLE_ADMIN') {
            Insurance::where('id', $request->id)
                ->update([
                    'name' => $request->name,
                    'price' => $request->price,
                    'count' => $request->count,
                    'status' => $request->status
                ]);
            return response()->json([
                'success' => true,
                'message' => 'Сохранено'
            ]);
        }
        return response('Доступ запрещён!', 403);
    }

    public function delete(Request $request, int $id)
    {
        if ($request->user()->role == 'ROLE_ADMIN') {
            Insurance::destroy($id);
            return response()->json([
                'success' => true,
                'message' => 'Удаленно'
            ]);
        }
        return response('Доступ запрещён!', 403);
    }
}
