<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

use App\Models\Deal;
use App\Models\DealFile;

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
            ->when($request->user()->role == 'ROLE_ADMIN', function ($q)
            use ($request) {
                return $q->whereHas('bids', function ($query)
                use ($request) {
                    return $query->where('bids.user_id', $request->user()->id);
                });
            });
        return response()->json($Deals->paginate(10));
    }


    public function show(Request $request, int $dealId)
    {
        $deal = Deal::select('*', 'deals.id as DEAL_ID')
            ->where('deals.id', $dealId)
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
     * End Ver 1.0
     */
}
