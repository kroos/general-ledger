<li class="nav-item">
	<a class="nav-link" href="{{ route('account-types.index') }}">Account Type</a>
</li>
<li class="nav-item">
	<a class="nav-link" href="{{ route('accounts.index') }}">Account</a>
</li>
<li class="nav-item">
	<a class="nav-link" href="{{ route('ledgers.index') }}">Ledger</a>
</li>
<li class="nav-item">
	<a class="nav-link" href="{{ route('journals.index') }}">Journal</a>
</li>

<li class="nav-item dropdown">
	<a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Reports</a>
	<div class="dropdown-menu">
		<a class="dropdown-item" href="{{ route('reports.general-ledger.index') }}">GL Reports</a>
		<a class="dropdown-item" href="{{ route('reports.trial-balance.index') }}">Trial Balance Reports</a>
		<a class="dropdown-item" href="{{ route('reports.profit-loss.index') }}">Profit Loss Reports</a>
		<a class="dropdown-item" href="{{ route('reports.balance-sheet.index') }}">Balance Sheet Reports</a>
	</div>
</li>
<li class="nav-item">
	<a class="nav-link" href="{{ route('activity-logs.index') }}">Activity Logs</a>
</li>
