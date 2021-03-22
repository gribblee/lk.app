<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
    }

    public function store(Request $request)
    {
    }

    /**
     * @param Request $request
     * @param $orderId
     * @return JsonResponse
     */
    public function view(Request $request, $orderId)
    {
    }

    /**
     * @param Request $request
     * @param $orderId
     * @return JsonResponse
     */
    public function orderBuy(Request $request, $orderId)
    {
    }

    /**
     * @param Request $request
     * @param $orderId
     * @return JsonResponse
     */
    public function delete(Request $request, $orderId)
    {
    }
}
