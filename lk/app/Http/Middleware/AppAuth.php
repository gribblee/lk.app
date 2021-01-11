<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;

use App\Models\AppToken;
use App\Models\Region;

class AppAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $appToken = AppToken::with('direction')->where('hash', $request->hash)->get();
        $appToken->isNotEmpty() || abort(401, '401 Unauthorized');
        $request->appToken = $appToken->first();

        /* Определение региона */


        if ($request->has('kladr_id')) {
            $request->region = Region::where('kladr_id', str_pad($request->kladr_id, 13, '0', STR_PAD_RIGHT))->first();
            $request->http_region = [];
        } elseif ($request->input('phone')) {
            try {
                $httpResponse =
                    Http::timeout(3)->get('https://api.regius.name/iface/phone-number.php?phone=' . str_replace(
                        [' ', '-', '(', ')'],
                        '',
                        $request->input('phone')
                    ))->json();
                $indexOf = strpos($httpResponse->region ?? 'Не определено', ' * ');
                $indexOf ? $httpRegion = substr(
                    $httpResponse->region,
                    $indexOf + 3,
                    strlen($httpResponse->region)
                ) : $httpRegion = $httpResponse->region ?? 'Не определено';
                $request->httpRegion = $httpResponse;
                $request->http_region = $httpRegion;
                $request->region = Region::where('kladr_id', str_pad($httpResponse->kladr ?? '0100000000', 13, '0', STR_PAD_RIGHT))->first();
            } catch (ConnectionException $e) {
                $request->http_region = [];
                $request->region = [];
            }
        }
        return $next($request);
    }
}
