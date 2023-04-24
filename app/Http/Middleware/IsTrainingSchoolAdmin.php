<?php

namespace App\Http\Middleware;

use App\Traits\ResponsesTrait;
use Closure;
use Illuminate\Http\Request;

class IsTrainingSchoolAdmin
{
    use ResponsesTrait;
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if(!$user)
            return $this->errorResponse("Only login users can access this route", 401);

        if(!$user->isTrainingSchoolAdmin()) return $this->errorResponse("Only training school admin can access this route", 401);
        return $next($request);
    }
}
