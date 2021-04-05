<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\News;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class NewsController extends Controller
{
    protected $validationRules = [
        'title' => 'required|string|min:3|max:255',
        'short_description' => 'required',
        'description' => 'required',
        'tags' => 'nullable|string',
        'images.*' => 'nullable',
    ];

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $onPage = 20;
        $page = (int) $request->page ?? 1;

        $items = News::orderByDesc('id')
            ->offset(
                ($page - 1) * $onPage
            )->limit($onPage)->get();
        $items->map(function ($item) {
            $item->images = json_decode($item->images, true) ?? [];
        });
        return response()->json($items);
    }

    public function upload(Request $request)
    {
        if ($request->hasFile('file')) {
            return $request->file->store(
                implode(DIRECTORY_SEPARATOR, ['images', 'news', Carbon::now()->format('Ymd')]),
                'public'
            );
        }
    }

    public function store(Request $request)
    {
        if ($request->user()->role === 'ROLE_ADMIN' || $request->user()->role === 'ROLE_MANAGER') {
            try {
                $validator = Validator::make(
                    $request->only('title', 'short_description', 'description', 'tags', 'images'),
                    $this->validationRules
                );

                if ($validator->fails())
                    return response()->json(['errors' => $validator->errors()], 422);

                $images = [];
                if ($request->has('images')) {
                    $images = $request->images;
                }

                $news = News::create([
                    'title' => $request->title,
                    'short_description' => $request->short_description,
                    'images' => json_encode($images, JSON_UNESCAPED_UNICODE),
                    'description' => $request->description,
                    'tags' => $request->tags,
                    'user_id' => $request->user()->id
                ]);
                return response()->json([
                    'message' => 'Созданно',
                    'id' => $news->id
                ]);
            } catch (Exception $e) {
            }
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
        $news->images = json_decode($news->images, JSON_UNESCAPED_UNICODE);
        $news->recomended = $this->recomended($news);
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
            $news = News::where('id', $newsId)->update([
                'title' => $request->title,
                'short_description' => $request->short_description,
                'description' => $request->description,
                'tags' => $request->tags
            ]);
            return response()->json([
                'message' => 'Обновлено',
                'id' => $news->id
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
        if ($request->user()->role === 'ROLE_ADMIN') {
            try {
                $news = News::findOrFail($newsId);
                $news->delete();
                return response()->json([
                    'message' => 'Новость удалена'
                ]);
            } catch (Exception $e) {
                return response()->json([
                    'message' => 'Новость не найдена'
                ], 401);
            }
        }
        return response()->json([
            'message' => 'Доступ запрещён'
        ], 403);
    }

    protected function recomended($news)
    {
        $tags = collect(explode(",", trim($news->tags)));
        $newsRecomended = collect([]);
        $newsNotIds = [$news->id];
        $tags->each(function ($tag) use ($newsRecomended, $newsNotIds) {
            $tagInNews = trim($tag);
            $tagNews = News::where('tags', 'LIKE', "%{$tagInNews}%")
                ->whereNotIn('id', $newsNotIds)
                ->orderByDesc('id')
                ->limit(5)
                ->get();
            if (!empty($tagNews) && $tagNews != null && count($tagNews) > 0) {
                $tagNews->each(function ($tagNw) use ($newsRecomended, $newsNotIds) {
                    if (!array_key_exists($tagNw->id, $newsNotIds)) {
                        $newsNotIds[] = $tagNw->id;
                        $tagNw->images = json_decode($tagNw->images, JSON_UNESCAPED_UNICODE);
                        $newsRecomended->push($tagNw);
                    }
                });
            }
        });
        return $newsRecomended;
    }
}
