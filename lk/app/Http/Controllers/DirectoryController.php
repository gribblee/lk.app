<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Direction;
use App\Models\Region;
use App\Models\Category;
use App\Models\Option;
use App\Models\Status;
use App\Models\Bid;

use App\Helpers\stdObject;
use GrahamCampbell\ResultType\Success;
/**
 * 
 */
class DirectoryController extends Controller
{
    public function index(Request $request)
    {
        $Response = new stdObject([
            'directions' => [],
            'regions' => [],
            'status' => [],
            'options' => [],
            'categories' => []
        ]);

        $Response->directions = Direction::with(['maxRate' => function($q) {
            return $q->where('is_launch', true)->orderByDesc('consumption')->first();
        }])->when($request->user()->role == 'ROLE_ADMIN', function ($q) use ($request) {
            return $q->whereJsonContains('categories', $request->user()->category_id);
        })->orderByDesc('id')->get();
        $Response->options = Option::getKeyValue();
        $Response->status = Status::orderBy('order', 'DESC')->get();
        $Response->categories = Category::all();
        $Response->regions = Region::orderByRaw('name_with_type COLLATE "C"')->get();
        $Response->maxRate = ceil(Bid::where([
            'is_launch' => true,
            'is_delete' => false,
        ])->avg('consumption'));

        return response()->json($Response, 200);
    }

    /**
     * @return JsonResponse
     */
    public function directionCreate(Request $request)
    {
        if ($request->user()->role === 'ROLE_ADMIN') {
            try {
                Direction::create([
                    'average_check' => 0,
                    'cost_price' => 0,
                    'conversion_contract' => 0,
                    'conversion_meetings' => 0,
                    'description' => 'Описание',
                    'extra' => 0,
                    'iframe_url' => 'http://',
                    'name' => 'Новое направление',
                    'categories' => json_encode([$request->user()->category_id])
                ]);
                return response()->json([
                    'success' => true,
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error' => 'Ошибка',
                ]);
            }
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function directionUpdate(Request $request, int $id)
    {
        if ($request->user()->role === 'ROLE_ADMIN') {
            try {
                $direction = Direction::findOrFail($id);
                $updated = $request->only([
                    'average_check',
                    'cost_price',
                    'conversion_contract',
                    'conversion_meetings',
                    'description',
                    'extra',
                    'iframe_url',
                    'name',
                    'categories'
                ]);
                $direction->update($updated);
                $directionAmount = ceil($direction->cost_price + ($direction->cost_price * ($direction->extra / 100)));
                Bid::where([
                    ['direction_id', '=', $id],
                    ['consumption', '<', $directionAmount]
                ])->update(['consumption' => $directionAmount]);
                return response()->json([
                    'success' => true,
                    'message' => "Направление \"{$direction->name}\" обновлено"
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error' => 'Ошибка заполнения полей'
                ]);
            }
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function directionDelete(Request $request, int $id)
    {
        if ($request->user()->role === 'ROLE_ADMIN') {
            try {
                $direction = Direction::findOrFail($id);
                $newDirection = Direction::findOrFail($request->newDirectionId);

                Bid::where('direction_id', $direction->id)->update(['direction_id' => $newDirection->id]);
                $direction->delete();
                return response()->json([
                    'success' => true,
                    'message' => "Направление \"{$direction->name}\" удалено"
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error' => 'Ошибка'
                ]);
            }
        }
    }

    /**
     * @return JsonResponse
     */
    public function regionCreate(Request $request)
    {
        if ($request->user()->role === 'ROLE_ADMIN') {
            try {
                $maxId = Region::max('id') + 1;
                Region::create([
                    'id' => $maxId,
                    'name' => 'Название региона',
                    'type' => 'Тип',
                    'name_with_type' => 'Респ',
                    'federal_district' => '23',
                    'kladr_id' => '43',
                    'fias_id' => '43',
                ]);
                return response()->json([
                    'success' => true,
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error' => 'Ошибка',
                ]);
            }
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function regionUpdate(Request $request, int $id)
    {
        if ($request->user()->role === 'ROLE_ADMIN') {
            try {
                $region = Region::findOrFail($id);
                $updated = $request->only([
                    'name',
                    'type',
                    'name_with_type',
                    'federal_district',
                    'kladr_id',
                    'fias_id',
                ]);
                $region->update($updated);
                return response()->json([
                    'success' => true,
                    'message' => "Регион \"{$region->name_with_type}\" обновлено"
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error' => 'Ошибка заполнения полей'
                ]);
            }
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function regionDelete(Request $request, int $id)
    {
        if ($request->user()->role === 'ROLE_ADMIN') {
            try {
                $region = Region::findOrFail($id);

                $region->delete();
                return response()->json([
                    'success' => true,
                    'message' => "Регион \"{$region->name_with_type}\" удален"
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'msg' => $e->getMessage(),
                    'error' => 'Ошибка'
                ]);
            }
        }
    }

    /**
     * @return JsonResponse
     */
    public function getRegions(Request $request)
    {
        return Region::orderBy('name')->paginate(12);
    }
}
