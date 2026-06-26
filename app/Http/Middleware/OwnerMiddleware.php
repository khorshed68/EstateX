<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class OwnerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('owner_user_id')) {
            return redirect()->route('owner.login')->with('error', 'Please sign in to access your owner dashboard.');
        }

        // Validate via raw query to check status and role constraints (roleId = 3 for standard users)
        $owner = DB::select("SELECT * FROM users WHERE id = :id AND roleId = 3 AND status = 'active'", [
            'id' => session('owner_user_id')
        ]);

        if (empty($owner)) {
            session()->forget(['owner_user_id', 'owner_user_name']);
            return redirect()->route('owner.login')->with('error', 'Access denied. Property owner privileges required.');
        }

        return $next($request);
    }
}
