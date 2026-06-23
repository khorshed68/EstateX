<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('admin_user_id')) {
            return redirect()->route('admin.login')->with('error', 'Please log in to access the administrator panel.');
        }

        // Validate via raw query
        $admin = DB::select("SELECT * FROM users WHERE id = :id AND roleId = 1 AND status = 'active'", [
            'id' => session('admin_user_id')
        ]);

        if (empty($admin)) {
            session()->forget(['admin_user_id', 'admin_user_name']);
            return redirect()->route('admin.login')->with('error', 'Access denied. Administrator privileges required.');
        }

        return $next($request);
    }
}
