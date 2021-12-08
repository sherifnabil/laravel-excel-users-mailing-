<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HasToken
{
    public function handle(Request $request, Closure $next)
    {
        $myToken = 'Bearer someTokenHere';
        if (!$request->header('Authorization') || $request->header('Authorization') != $myToken) {
            return response()->json([
                'message'   =>  'Unauthorized'
            ], 401);
        }
        return $next($request);
    }
}
