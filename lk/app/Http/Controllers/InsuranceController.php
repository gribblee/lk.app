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
        return response()->json(Insurance::all(), 200);
    }

    public function create(Request $request)
    {
    }

    public function update(Request $request)
    {
    }

    public function delete(Request $request, int $id)
    {
    }
}
