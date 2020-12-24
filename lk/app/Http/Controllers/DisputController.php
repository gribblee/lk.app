<?php

namespace App\Http\Controllers;

use App\Models\Disput;

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
}
