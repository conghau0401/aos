<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Facades\Cafe24ApiService;
use App\Models\Mall;
use Illuminate\Support\Facades\Log;

class AuthAdmin
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
        $mall = Mall::where('mall_id', config('aos.mall_id'))->first();
        $result = Cafe24ApiService::get($mall->mall_id, $mall->access_token, 'store');
        if (isset($result->error)) {
            Log::info("Error admin token: " . json_encode($result) . json_encode($request->header()));
            return response()->json($result);
        }
        return $next($request);
    }
}
