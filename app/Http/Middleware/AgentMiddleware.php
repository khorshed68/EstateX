<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class AgentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('agent_user_id')) {
            return redirect()->route('agent.login')->with('error', 'Please sign in to access your agent dashboard.');
        }

        // Validate via raw query to check status and role constraints (roleId = 2 for agents)
        $agent = DB::select("
            SELECT u.*, a.id AS agent_table_id 
            FROM users u 
            JOIN agents a ON u.id = a.userId 
            WHERE u.id = :id AND u.roleId = 2 AND u.status = 'active'
        ", [
            'id' => session('agent_user_id')
        ]);

        if (empty($agent)) {
            session()->forget(['agent_user_id', 'agent_user_name', 'agent_id']);
            return redirect()->route('agent.login')->with('error', 'Access denied. Real estate agent privileges required.');
        }

        // Cache the agent's primary key from the agents table
        if (!session()->has('agent_id')) {
            session(['agent_id' => $agent[0]->agent_table_id]);
        }

        return $next($request);
    }
}
