<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserAccess
{

    public function handle(Request $request, Closure $next, $userRole)
    {
        if(auth()->user()->user_role == $userRole){
            return $next($request);
        }

        return response()->json(['You do not have permission to access for this page.']);
    }

}
