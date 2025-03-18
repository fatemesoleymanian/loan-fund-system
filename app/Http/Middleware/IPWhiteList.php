<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IPWhiteList
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
        $allowedIps = explode(',',env('ALLOWED_IPS'));
        $clientIp = $request->ip();

        if (!in_array($clientIp,$allowedIps)){
            return response()->json([
                'error' => 'جازه دسترسی به برنامه به شما داده نشده!'
//                'dd'=> $token
            ],401);
        }
        return $next($request);
    }
}
