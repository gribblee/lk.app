<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\News;

class NewsController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        return response()->json(News::paginate(10));
    }

    public function store(Request $request)
    {
        if ($request->user()->role === 'ROLE_ADMIN' || $request->user()->role === 'ROLE_MANAGER') {
            News::create([
                'title' => $request->title,
                'short_description' => $request->short_description,
                'description' => $request->description,
                'tags' => $request->tags,
                'user_id' => $request->user()->id
            ]);
            return response()->json([
                'message' => 'Созданно'
            ]);
        }
    }

    /**
     * @param Request $request
     * @param $newsId
     * @return JsonResponse
     */
    public function view(Request $request, $newsId)
    {
        $news = News::findOrFail($newsId);
        return response()->json($news);
    }

    /**
     * @param Request $request
     * @param $newsId
     * @return JsonResponse
     */
    public function update(Request $request, $newsId)
    {
        if ($request->user()->role === 'ROLE_ADMIN' || $request->user()->role === 'ROLE_MANAGER') {
            News::where('id', $newsId)->update([
                'title' => $request->title,
                'short_description' => $request->short_description,
                'description' => $request->description,
                'tags' => $request->tags
            ]);
            return response()->json([
                'message' => 'Созданно'
            ]);
        }
    }

    /**
     * @param Request $request
     * @param $newsId
     * @return JsonResponse
     */
    public function delete(Request $request, $newsId)
    {
        
    }
}
