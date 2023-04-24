<?php

namespace App\Http\Middleware;

use Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Closure;
use Carbon\Carbon;
use App\Models\User;


class CheckTwoFactorAuthentication
{
    public function handle($request, Closure $next)
    {
            if(auth()->user()->expires_at < Carbon::now())
            {
                auth()->user()->resetTwoFactorAuthCode();
                auth()->logout();
               return response()->json([ 'message' => 'The two factor code has expired. Please login again.']);
              }
            
            return $next($request);
    }
}




