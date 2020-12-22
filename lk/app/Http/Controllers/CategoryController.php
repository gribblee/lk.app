<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category;

class CategoryController extends Controller
{

    public function index()
    {
        return response()->json(Category::orderBy('id', 'DESC')->get(), 200);
    }
}
