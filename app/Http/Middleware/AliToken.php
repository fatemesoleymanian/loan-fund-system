<?php

namespace App\Http\Middleware;

use Closure;
use http\Env\Response;
use Illuminate\Http\Request;

class AliToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('Authorization');

        if($token !== 'Bearer '.env('ACCESS_TOKEN')){
            return response()->json([
                'error' => 'جازه دسترسی به برنامه به شما داده نشده!'
//                'dd'=> $token
            ],401);
        }
        return $next($request);
    }
}
