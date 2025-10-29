<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Login;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CustomLoginController extends Controller
{
	public function create(): View
	{
		return view('auth.login');
	}

	public function store(Request $request): RedirectResponse
	{
		$credentials = $request->validate([
			'login' => ['required', 'string'],
			'password' => ['required', 'string'],
		]);

		// Find login by username, email, or phone
		$login = Login::findForAuthentication($credentials['login']);

		if (!$login) {
			return back()->withErrors([
				'login' => 'The provided credentials do not match our records.',
			])->onlyInput('login');
		}

		// Attempt authentication using the login's username
		if (Auth::attempt([
			'username' => $login->username,
			'password' => $credentials['password']
		], $request->boolean('remember'))) {

			$request->session()->regenerate();

			// Mark login activity
			$login->markLogin($request->ip());

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

		return back()->withErrors([
			'login' => 'The provided credentials do not match our records.',
		])->onlyInput('login');
	}

	public function destroy(Request $request): RedirectResponse
	{
		Auth::logout();

		$request->session()->invalidate();
		$request->session()->regenerateToken();

		return redirect('/');
	}

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
