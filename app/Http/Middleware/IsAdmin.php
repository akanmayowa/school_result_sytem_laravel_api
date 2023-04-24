<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Traits\ResponsesTrait;

class IsAdmin
{
   use ResponsesTrait;

    public function handle(Request $request, Closure $next)
    {
         $user = auth()->user();

        if(!$user)
            return $this->errorResponse("Only login users can access this route", 401);

        if(!($user->isAdmin() || $user->isSuperAdmin())){
            return $this->errorResponse("Only admin and super admin can access this route", 401);
        }
        return $next($request);
    }
}
