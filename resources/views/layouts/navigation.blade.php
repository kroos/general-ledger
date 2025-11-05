<li class="nav-item">
	<a class="nav-link" href="{{ route('journals.index') }}">Journals</a>
</li>
<li class="nav-item">
	<a class="nav-link" href="{{ route('accounts.index') }}">Accounts</a>
</li>
<li class="nav-item">
	<a class="nav-link" href="{{ route('activity-logs.index') }}">Activity Logs</a>
</li>
<li class="nav-item dropdown">
	<a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Reports</a>
	<div class="dropdown-menu">
		<a class="dropdown-item" href="{{ route('reports.general-ledger.index') }}">GL Reports</a>
		<a class="dropdown-item" href="{{ route('reports.trial-balance.index') }}">Trial Balance Reports</a>
		<a class="dropdown-item" href="{{ route('reports.profit-loss.index') }}">Profit Loss Reports</a>
		<a class="dropdown-item" href="{{ route('reports.balance-sheet.index') }}">Balance Sheet Reports</a>
<!-- 		<div class="dropdown-divider"></div>
		<a class="dropdown-item" href="#">Separated link</a> -->
	</div>
</li>
<li class="nav-item dropdown">
	<a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Accounting</a>
	<div class="dropdown-menu">
		<a class="dropdown-item" href="{{ route('accounting.sales-invoices.index') }}">Sales Invoices</a>
		<!-- <a class="dropdown-item" href="{{ route('accounting.sales-invoices.post', 1) }}">Sales Invoices Post</a> -->
		<a class="dropdown-item" href="{{ route('accounting.purchase-bills.index') }}">Purchase Bills</a>
		<!-- <a class="dropdown-item" href="{{ route('accounting.purchase-bills.post', 1) }}">Purchase Bills Post</a> -->
		<!-- <div class="dropdown-divider"></div> -->
	</div>
</li>
<li class="nav-item">
	<a class="nav-link" href="{{ route('accounting.payments.index') }}">Payments</a>
</li>


@include('accounting.journals._draftNotifications')
