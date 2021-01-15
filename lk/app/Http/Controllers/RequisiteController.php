<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Requisite;

class RequisiteController extends Controller
{
    //

    public function index(Request $request)
    {
        return Requisite::getNonDelete($request->user()->id)
                        ->orderBy('created_at', 'DESC')
                        ->get();
    }

    public function show(Request $request, int $id)
    {
        
    }

    public function create(Request $request)
    {
        $requisite = Requisite::create([
            'name' => 'Имя компании',
            'ogrn' => '',
            'inn' => '',
            'kpp' => '',
            'bik' => '',
            'bank' => '',
            'ksch' => '',
            'rsch' => '',
            'jour_address' => '',
            'poste_address' => '',
            'director' => '',
            'user_id' => $request->user()->id,
            'is_delete' => false,
        ]);
        return response()->json($requisite);
    }

    public function update(Request $request, int $id)
    {
        /**
         * Start Ver 1.0
         */
        $requisite = Requisite::where('id', $id)->first();
        if ($requisite->user_id === $request->user()->id || $request->user()->role === 'ROLE_ADMIN') {
            $requisite->update($request->only([
                'name',
                'ogrn',
                'inn',
                'kpp',
                'bik',
                'bank',
                'ksch',
                'rsch',
                'jour_address',
                'poste_address',
                'director'
            ]));
            return response()->json([
                'success' => true,
                'message' => 'Реквизиты обновлены'
            ]);
        }
        return response()->json([
            'sucess' => false,
            'error' => 'ДОСТУП ЗАПРЕЩЁН'
        ], 403);
        /**
         * End Ver 1.0
         */
    }

    public function delete(Request $request, int $id)
    {
        $requisite = Requisite::where('id', $id)->first();
        if (
            $requisite->user_id === $request->user()->id
            || $request->user()->role === 'ROLE_ADMIN'
        ) {
            $requisite->update([
                'is_delete' => true
            ]);
            return response()->json([
                'success' => true
            ], 200);
        }
        return response()->json([
            'success' => false,
            'error' => 'Доступ запрещён!'
        ], 403);
    }
}
