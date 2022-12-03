<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Facades\Cafe24ApiService;
use Illuminate\Support\Facades\Log;

class AuthApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $result = Cafe24ApiService::get(config('aos.mall_id'), $request->bearerToken(), 'store', [], ['aos_request' => true]);
        if (isset($result->error)) {
            Log::channel('api')->info("Error token: " . json_encode($result) . json_encode($request->header()));
            return response()->json($result);
        }
        Log::channel('api')->info("Request: " . json_encode($request->all(), JSON_UNESCAPED_UNICODE) . json_encode($request->header()));

        // set shop no
        $request->merge(['cafe24_shop_no' => 1, 'mall_id' => config('aos.mall_id')]);
        return $next($request);
    }
}
