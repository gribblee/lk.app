<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Direction;
use App\Models\Region;
use App\Models\Category;
use App\Models\Option;
use App\Models\Status;

use App\Helpers\stdObject;

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

        $Response->directions = Direction::when($request->user()->role == 'ROLE_ADMIN', function ($q) use ($request) {
            return $q->whereJsonContains('categories', $request->user()->category_id);
        })->get();
        $Response->options = Option::getKeyValue();
        $Response->status = Status::orderBy('order', 'DESC')->get();
        $Response->categories = Category::all();
        $Response->regions = Region::all();

        return response()->json($Response, 200);
    }

    /**
     * @return JsonResponse
     */

    public function directionSave(Request $request)
    {
    }
}
