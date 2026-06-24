<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class BuyerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('buyer_user_id')) {
            return redirect()->route('buyer.login')->with('error', 'Please sign in to access your dashboard.');
        }

        // Validate via raw query to check status and role constraints
        $buyer = DB::select("SELECT * FROM users WHERE id = :id AND roleId = 3 AND status = 'active'", [
            'id' => session('buyer_user_id')
        ]);

        if (empty($buyer)) {
            session()->forget(['buyer_user_id', 'buyer_user_name']);
            return redirect()->route('buyer.login')->with('error', 'Access denied. Buyer privileges required.');
        }

        return $next($request);
    }
}
