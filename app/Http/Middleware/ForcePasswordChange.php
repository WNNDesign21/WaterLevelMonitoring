<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->must_change_password && !$request->routeIs('password.change', 'logout')) {
            return redirect()->route('password.change')->with('error', 'Anda harus mengganti password sebelum melanjutkan.');
        }

        return $next($request);
    }
}
