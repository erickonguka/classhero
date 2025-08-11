<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureMfaVerified
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Check if MFA is required for admins
        if ($user->isAdmin() && !session('2fa_verified')) {
            return redirect()->route('mfa.show');
        }
        
        return $next($request);
    }
}