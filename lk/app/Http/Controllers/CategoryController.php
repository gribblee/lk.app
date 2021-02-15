<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category;

class CategoryController extends Controller
{

    /**
     * @return JsonResponse
     */
    public function index()
    {
        return response()->json(Category::orderBy('id', 'DESC')->get(), 200);
    }

    /**
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        if ($request->user()->role == 'ROLE_ADMIN') {
            Category::create([
                'name' => '',
                'description' => ''
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
        if ($request->user()->role == 'ROLE_ADMIN' && $request->has('name') && $request->has('description')) {
            Category::where('id', $id)->update([
                'name' => $request->name ?? '',
                'description' => $request->description ?? '',
                'source_id' => $request->source_id ?? 0
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
    public function delete(Request $request, int $id)
    {
        if ($request->user()->role == 'ROLE_ADMIN') {
            Category::where('id', $id)->delete();
            return response()->json([
                'success' => true
            ]);
        }
        return response('Доступ запрещён!', 403);
    }
}
