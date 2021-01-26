<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Company\Company;
use App\Models\Company\Issues;
use Exception;

class IssuesController extends Controller
{
    //

    /**
     * @param conpanyId
     * @return JsonResponse
     */
    public function create(Request $request, int $companyId)
    {
        try {
            $company = Company::where('user_id', $request->user()->id)->findOrFail($companyId);
            return Issues::create([
                'title' => '',
                'description' => '',
                'direction_id' => null,
                'priceTo' => 0,
                'priceFrom' => 0,
                'company_id' => $company->id
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Ошибка на сервисе'
            ], 500);
        }
    }

    /**
     * @param conpanyId
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, int $companyId, int $id)
    {
        try {
            $issue = Issues::where('company_id', $companyId)
                ->whereHas('company', function ($query) use ($request) {
                    return $query->where('user_id', $request->user()->id);
                })->findOrFail($id);
            $update = $request->only([
                'title', 'description', 'priceTo', 'priceFrom', 'direction_id'
            ]);
            $issue->update($update);
            return response()->json(['message' => 'Решения обновлены']);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Ошибка на сервисе'
            ], 500);
        }
    }

    /**
     * @param conpanyId
     * @param $id
     * @return JsonResponse
     */
    public function delete(Request $request, int $companyId, int $id)
    {
        try {
            $issues = Issues::where('company_id', $companyId)
                ->whereHas('company', function ($query) use ($request) {
                    return $query->where('user_id', $request->user()->id);
                })->findOrFail($id);
            $issues->delete();
            return [
                'message' => "{$issues->title} удалено"
            ];
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Ошибка на сервисе'
            ], 500);
        }
    }
}
