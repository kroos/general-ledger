<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CompanySelectionController extends Controller
{
    public function index(): View
    {
        $companies = Auth::user()->accessibleCompanies();

        return view('company-selection', compact('companies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id' => ['required', 'exists:companies,id']
        ]);

        $company = Company::findOrFail($request->company_id);
        $user = Auth::user();

        // Check if user has access to this company
        if (!$user->companies()->where('company_id', $company->id)->exists()) {
            return redirect()->route('company.select')
                ->with('error', 'You do not have access to this company.');
        }

        $this->setCurrentCompany($company, $user);

        return redirect()->intended(route('dashboard', absolute: false));
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
