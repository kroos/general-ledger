<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
	/**
	 * Display the login view.
	 */
	public function create(): View
	{
		return view('auth.login');
	}

	/**
	 * Handle an incoming authentication request.
	 */
	public function store(LoginRequest $request): RedirectResponse
	{
		// return redirect()->intended(route('dashboard', absolute: false));

		// Standard Laravel Breeze authentication
		$request->authenticate();
		$request->session()->regenerate();

		/** @var \App\Models\Login $login */
		$login = Auth::user();

		// Mark login activity
		$login->markLogin($request->ip());

		// Check if email verification is required
		if (method_exists($login, 'hasVerifiedEmail') && !$login->hasVerifiedEmail()) {
			return redirect()->route('verification.notice');
		}

		// Get user's companies
		$companies = $login->user->accessibleCompanies();

		if ($companies->count() === 1) {
			// Auto-select if only one company
			$company = $companies->first();
			$this->setCurrentCompany($company, $login->user);
			return redirect()->intended(route('dashboard', absolute: false));
		} elseif ($companies->count() > 1) {
			// Redirect to company selection
			return redirect()->route('company.select');
		} else {
			// No companies assigned
			Auth::logout();
			return back()->withErrors([
				'login' => 'No companies assigned to your account. Please contact administrator.',
			])->onlyInput('login');
		}

	}

	/**
	 * Destroy an authenticated session.
	 */
	public function destroy(Request $request): RedirectResponse
	{
		Auth::guard('web')->logout();
		$request->session()->invalidate();
		$request->session()->regenerateToken();
		return redirect('/');
	}

	/**
	 * Set the current company and role session data.
	 */
	protected function setCurrentCompany(Company $company, $user)
	{
		$companyUser = $user->companies()
		->where('company_id', $company->id)
		->first();

		session([
			'current_company' => $company,
			'current_company_id' => $company->id,
			'current_role' => $companyUser->pivot->role,
			'current_role_permissions' => $companyUser->pivot->role->permissions ?? [],
		]);
	}

}
