<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Models\Company\Company;
use App\Models\Company\Issues;

use Exception;

class CompanyController extends Controller
{
    protected $disk = 'public';

    function __construct(Request $request)
    {
    }

    /**
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        return Company::with('issues')->with('user')->with('region')->orderByDesc('rating')->paginate(10);
    }

    /**
     * @param $request request
     */

    public function webCompanies(Request $request)
    {
        $companies = Company::with('issues')->with('user')->with('region')->orderByDesc('rating')->limit(10)->get();
        return response()->view('web.companies', [
            'companies' => json_encode($companies)
        ])->header('Content-Type', 'application/javascript');
    }

    /**
     * @param $regionId
     * @return JsonResponse
     */
    public function companiesInRegion(Request $request, int $regionId)
    {
        return Company::with('issues')->with('user')->with('region')->where('region_id', $regionId)->orderByDesc('rating')->paginate(10);
    }

    /**
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        try {
            $company = Company::create([
                'user_id' => $request->user()->id
            ]);
            return [
                'status' => 'success',
                'message' => 'Созданно',
                'id' => $company->id
            ];
        } catch (Exception $e) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Ошибка сервиса',
            ], 500);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function show(Request $request, int $id)
    {
        try {
            $company = Company::with('issues', 'issues.direction')->with('user')->findOrFail($id);
            return response()->json($company);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ошибка сервиса']);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function upload(Request $request, int $id)
    {

        $request->validate([
            'file' => 'required|mimes:jpeg|max:16384'
        ]);
        try {
            if ($request->hasFile('file')) {
                $uid = $request->user()->id . time();
                $fileName = $request->file->getClientOriginalName();
                $filePath = $request->file('file')->storeAs('company/' . $id, $fileName, 'public');
                return response()->json([
                    'uid' => $uid,
                    'name' => $fileName,
                    'status' => 'done',
                    'thumbUrl' => Storage::url($filePath),
                    'url' => Storage::url($filePath)
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Ошибка сервиса',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id)
    {
        try {
            $update = $request->only([
                'address',
                'description',
                'files',
                'name',
                'region_id',
                'files'
            ]);
            if ($request->user()->role === 'ROLE_ADMIN') {
                $update['rating'] = $request->rating;
            }
            $update['files'] = json_encode($update['files']);
            $company = Company::when($request->user()->role !== 'ROLE_ADMIN', function ($query) use ($request) {
                return $query->where('user_id', $request->user()->id);
            })->findOrFail($id);
            $company->update($update);
            return response()->json([
                'message' => "Компания {$company->name} обновлена"
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Ошибка сервиса'
            ], 500);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function delete(Request $request, int $id)
    {
        try {
            $company = Company::where('user_id', $request->user()->id)->findOrFail($id);
            $company->delete();
            return response()->json([
                'message' => "Компания {$company->name} удалена"
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Ошибка'
            ], 500);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function meCompanies(Request $request)
    {
        try {
            return  Company::where('user_id', $request->user()->id)
                ->orderByDesc('created_at')
                ->paginate(10);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка'
            ], 500);
        }
    }
}
