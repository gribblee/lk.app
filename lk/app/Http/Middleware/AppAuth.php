<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;

use App\Models\AppToken;
use App\Models\Region;
use Exception;

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
        try {
            $appToken = AppToken::with('direction')->where('hash', $request->hash)->firstOrFail();
            /* Определение региона */

            if ($request->has('kladr_id')) {
                $request->region = Region::where('kladr_id', str_pad($request->kladr_id, 13, '0', STR_PAD_RIGHT))->first();
                $request->http_region = [];
            } elseif ($request->has('phone') || $request->has('Phone')) {
                try {
                    $phone = $request->has('phone') ? $request->phone : $request->Phone;
                    $httpResponse =
                        Http::timeout(3)->get('https://api.regius.name/iface/phone-number.php?phone=' . str_replace(
                            [' ', '-', '(', ')'],
                            '',
                            $phone
                        ))->json();
                    $indexOf = strpos($httpResponse->region ?? 'Не определено', ' * ');
                    $indexOf ? $httpRegion = substr(
                        $httpResponse->region,
                        $indexOf + 3,
                        strlen($httpResponse->region)
                    ) : $httpRegion = $httpResponse->region ?? 'Не определено';
                    $request->httpRegion = $httpResponse;
                    $request->http_region = $httpRegion;
                    if (isset($httpResponse->kladr)) {
                        $request->region = Region::where('kladr_id', $httpResponse->kladr)->first();
                    } else {
                        $request->region = [];
                    }
                } catch (ConnectionException $e) {
                    $request->http_region = [];
                    $request->region = [];
                }
            } else {
                return response('OK', 200);
            }
            return $next($request);
        } catch (Exception $e) {
            abort(401);
        }
    }
}
