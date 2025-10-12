<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        foreach ($guards ?: [null] as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::user();

                if ($user->hasRole(['super-admin', 'admin'])) {
                    return redirect()->route('admin.dashboard');
                }

                if ($user->hasRole('client')) {
                    return redirect()->route('dashboard');
                }

                return redirect('/');
            }
        }

        return $next($request);
    }
}
