<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCompany
{
	public function handle(Request $request, Closure $next): Response
	{
		// Skip for company selection and auth routes
		if ($request->routeIs('company.*') || $request->routeIs('login') || $request->routeIs('logout')) {
			return $next($request);
		}

		// System admins can access without company
		if (auth()->check() && auth()->user()->user->isSystemAdmin()) {
			return $next($request);
		}

		// Check if user has selected a company
		if (!session('current_company_id')) {
			return redirect()->route('company.select');
		}

		return $next($request);
	}
}
