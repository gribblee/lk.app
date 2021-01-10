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
        if ($request->user()->role == 'ROLE_ADMIN' && $request->has('name') && $request->has('description')) {
            Category::where('id', $request->id)->update([
                'name' => $request->name ?? '',
                'description' => $request->description ?? ''
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
    public function delete(Request $request)
    {
        if ($request->user()->role == 'ROLE_ADMIN' && $request->has('id')) {
            Category::where('id', $request->id)->delete();
            return response()->json([
                'success' => true
            ]);
        }
        return response('Доступ запрещён!', 403);
    }
}
