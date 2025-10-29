<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SystemAdminOnly
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
	 */
	public function handle(Request $request, Closure $next): Response
	{
		if (!auth()->check() || !auth()->user()->user->isSystemAdmin()) {
			return redirect()->route('dashboard')->with('error', 'System administrator access required.');
		}

		return $next($request);    }
}
