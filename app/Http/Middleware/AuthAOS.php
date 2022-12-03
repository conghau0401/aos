<?php

namespace App\Http\Middleware;

use App\Facades\AOSAPI;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthAOS
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
        Log::channel('request')->info('Request: ' . jEncode($request->all()) . $request->ip() . jEncode($request->header()));
        $checkToken = AOSAPI::post(request()->bearerToken(), 'account/api');
        if (isset($checkToken->error)) {
            return response('');
        }
        return $next($request);
    }
}
