<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Models\Deal;
use App\Models\DealFile;

class DealController extends Controller
{
    /**
     * Start Ver 1.0
     */
    public function index(Request $request)
    {
        $search_f = (object)$request->input('search_');
        $deals = Deal::select('*', 'deal.id AS DEAL_ID')
            ->join('bids', function ($join) use ($request) {
                $join->on('deals.bid_id', '=', 'bids.id');
            })
            ->with('region')
            ->with('direction')
            ->with('status')
            ->with('disput')
            ->with('bids.user')
            ->when($request->user()->role != 'ROLE_ADMIN' && $request->user()->role != 'ROLE_MANAGER', function ($q) use ($request) {
                return $q->where('bids.user_id', $request->user()->id);
            })
            ->when($request->has('order_by'), function ($q) use ($request) {
                if ($request->order_by != 'DEF') {
                    return $q->orderBy('deals.created_at', $request->order_by);
                }
            })
            ->when($request->user()->role == 'ROLE_ADMIN' && empty($search_f->manager) == false, function ($qa) use ($request, $search_f) {
                return $qa->whereHas('bids.user', function ($qam) use ($request, $search_f) {
                    return $qam->where('name', 'LIKE', "%{$search_f->manager}%");
                });
            })
            ->when($request->user()->role == 'ROLE_MANAGER', function ($q) use ($request, $search_f) {
                return $q->whereHas('bids.user', function ($qu) use ($request, $search_f) {
                    return $qu->where('manager_id', $request->user()->id)->when(empty($search_f->manager) == false, function ($qm) use ($request, $search_f) {
                        $qm->where('name', 'LIKE', "%{$search_f->manager}%");
                    });;
                });
            })
            // ->when($request->user()->role == 'ROLE_MANAGER', function ($q) use ($request, $search_f) {
            //     return $q->whereHas('bids.user', function ($qu) use ($request, $search_f) {
            //         return $qu->where('manager_id', $request->user()->id)->when(empty($search_f->manager), function ($qm) use ($request, $search_f) {
            //             return $qm->whereHas('user', function ($qum) use ($request, $search_f) {
            //                 return $qum->where('name', 'LIKE', "%{$search_f->manager}%");
            //             });
            //         });
            //     });
            // })
            ->withCount('disput')
            ->whereHas('status', function ($q) use ($search_f) {
                if (empty($search_f->status_id) === false) {
                    $q->where('id', $search_f->status_id);
                } else {
                    $q->whereNotIn('type', [1003]);
                }
            })
            ->where('bids.category_id', $request->user()->type)
            ->where('deals.is_delete', false)
            ->orderBy('deals.created_at', 'DESC');
        if (empty($search_f->name) === false) {
            $deals->where('deals.name', 'LIKE', "%{$search_f->name}%");
        }
        if (empty($search_f->region_id) === false) {
            $deals->where('deals.region_id', $search_f->region_id);
        }
        if (empty($search_f->direction_id) === false) {
            $deals->where('deals.direction_id', $search_f->direction_id);
        }

        return response()->json($deals->paginate(10));
    }


    public function show(Request $request, int $dealId)
    {
        $deal = Deal::select('*', 'deals.id as DEAL_ID')
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
            ->with('deals_files')
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
