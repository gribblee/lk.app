<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

use App\Models\Deal;
use App\Models\DealFile;
use App\Models\Status;

class DealController extends Controller
{
    /**
     * Start Ver 1.0
     */
    public function index(Request $request)
    {
        $Deals = Deal::selectRaw('*, deals.id AS deal_id')
            ->with('status')
            ->with('region')
            ->with('direction')
            ->with('disput')
            ->with('bids.user')
            ->when($request->user()->role == 'ROLE_USER', function ($q)
            use ($request) {
                return $q->whereHas('bids', function ($query)
                use ($request) {
                    return $query->where('bids.user_id', $request->user()->id);
                });
            })->when($request->user()->role == 'ROLE_MANAGER', function($q) use($request) {
                return $q->whereHas('bids.user', function ($query) use($request) {
                    return $query->where('manager_id', $request->user()->id);
                });
            })->where('is_delete', false);
        return response()->json($Deals->paginate(10));
    }


    /**
     * @param $id
     * @return JsonResponse
     */
    public function uploadAudio(Request $request, int $id)
    {
        $DFiles = new DealFile;
        $request->validate([
            'file' => 'required|mimes:mpeg3,x-mpeg-3,mpeg,x-mpeg,wav,x-wav,mp3|max:16384'
        ]);

        if ($request->file()) {
            $fileName = time() . '_' . $request->file->getClientOriginalName();
            $filePath = $request->file('file')->storeAs('deals', $fileName, 'public');
            $DFiles->name = $fileName;
            $DFiles->ext = $request->file('file')->getMimeType();
            $DFiles->deal_id = $id;
            $DFiles->save();
            return response()->json([
                'uid' => $DFiles->id,
                'name' => $fileName,
                'status' => 'done',
                'thumbUrl' => \App::environment('APP_URL') . "/api/deal/{$id}/storage/{$DFiles->id}",
                'url' => \App::environment('APP_URL') . "/api/deal/{$id}/storage/{$DFiles->id}"
            ]);
        }
    }

    public function storageDelete(Request $request, int $id, string $storage_id)
    {
        $deal = Deal::with('bids')
            ->findOrFail($id);
        if ($deal->bids->user_id === $request->user()->id) { //Првоеряем принадлежит ли заявка пользователию
            $dealFile = DealFile::findOrFail($storage_id);
            Storage::disk('public')
                ->delete('/deals/' . $dealFile->name);
            DealFile::destroy(
                $dealFile->id
            );
            return response('done', 200);
        }
        return response('ERROR 404', 404);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function show(Request $request, int $id)
    {
        $deal = Deal::select('*', 'deals.id as deal_id')
            ->where('deals.id', $id)
            ->whereHas('bids', function ($q) use ($request) {
                return $q->when($request->user()->role != 'ROLE_ADMIN' && $request->user()->role != 'ROLE_MANAGER', function ($q) use ($request) {
                    return $q->where('bids.user_id', $request->user()->id);
                });
            })
            ->with('bids')
            ->with('disput')
            ->with('region')
            ->with('status')
            ->with('deal_files')
            ->when($request->user()->role == 'ROLE_MANAGER', function ($q) use ($request) {
                return $q->whereHas('bids.user', function ($qb) use ($request) {
                    return $qb->where('manager_id', $request->user()->id);
                });
            })
            ->where('is_delete', false)
            ->firstOrFail();
        return response()->json($deal);
        if (
            $deal->status->id === 3 &&
            ($request->user()->role != 'ROLE_ADMIN' && $request->user()->role != 'ROLE_WEBMASTER')
        ) {
            unset($deal->name);
            unset($deal->phone);
            unset($deal->email);

            unset($deal->region);
            unset($deal->bids);
            $deal->bids = [
                'direction' => ''
            ];
            $deal->region = [
                'name' => ''
            ];
            $deal->deal_files = [];
        }
        if ($request->user()->role != 'ROLE_ADMIN' && $request->user()->role != 'ROLE_WEBMASTER') {
            unset($deal->request);
            unset($deal->referer);
            unset($deal->utm);
            unset($deal->api_id);
        }
        if ($deal->is_view == false && $request->user()->id == $deal->bids->user_id) {
            Deal::find($deal->id)->update(['is_view' => true]);
        }
        return response()->json($deal, 200);
    }

    public function storage(Request $request, int $id, string $storage_id)
    {
        // $deal = Deals::with('bids')->findOrFail($id);
        // if ($deal->bids->user_id === $request->user()->id) { //Првоеряем принадлежит ли заявка пользователию
        $dealFile = DealFile::findOrFail($storage_id);
        return response(
            Storage::disk('public')->get('/deals/' . $dealFile->name)
        )
            ->header('Accept-Ranges', 'bytes')
            ->header('Content-type', $dealFile->ext)
            ->header('Content-length', Storage::disk('public')->size('/deals/' . $dealFile->name))
            ->header('Content-Range', 'bytes 0-' . Storage::disk('public')->size('/deals/' . $dealFile->name));
        // }
        return response('ERROR 404', 404);
    }

    /**
     * @return JsonResponse
     */

    public function statusUpdate(Request $request, int $id)
    {
        $deal = Deal::with('disput')->findOrFail($id);
        $status = Status::where('id', $request->input('status_id'))->first();
        // if($status->type === 1003 && $deal->disput == null) {
        //     return response()->json([
        //         'status' => 'error',
        //         'error' => 'Вы не можете переключить на статус спорная'
        //     ]);
        // }
        $deal->status_id = $status->id;
        $deal->save();
        return response()->json([
            'status' => 'OK',
            'data' => $status
        ]);
    }
    /**
     * End Ver 1.0
     */
}
