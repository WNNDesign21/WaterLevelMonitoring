<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;

class CheckSystemLockdown
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $lockdown = DB::table('system_settings')->where('key', 'system_lockdown')->value('value');

        if ($lockdown === '1') {
            // Only allow 'Administrator IT' to bypass lockdown
            if (!auth()->check() || auth()->user()->role !== 'Administrator IT') {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'System under emergency lockdown.'], 503);
                }
                
                // You can also create a custom view for this, 
                // but for now, 503 Service Unavailable with custom message is good.
                abort(503, 'SYSTEM_EMERGENCY_LOCKDOWN: Akses dibatasi oleh Administrator Pusat.');
            }
        }

        return $next($request);
    }
}
