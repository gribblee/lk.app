<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Option;

class OptionController extends Controller
{
    //

    /**
     * @return JsonResponse
     */
    public function updateOrCreate(Request $request)
    {
        if ($request->user()->role == 'ROLE_ADMIN') {
            foreach ($request->formOptions as $key => $value) {
                Option::updateOrCreate([
                    'name' => $key
                ], [
                    'value' => $value
                ]);
            }
            return response()->json([
                'success' => true,
                'message' => 'Настройки сохранены'
            ]);
        }
        return response('Доступ запрещён!', 403);
    }
}
