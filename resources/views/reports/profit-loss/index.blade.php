@extends('layouts.app')

@section('content')
<div class="card">
  <div class="card-header">
    <h5 class="mb-0"><i class="fa fa-chart-line"></i> Profit & Loss Statement</h5>
  </div>
  <div class="card-body">
    <form method="get" class="row mb-3">
      <div class="col-md-3">
        <label>From:</label>
        <input type="date" name="from" value="{{ $from }}" class="form-control form-control-sm">
      </div>
      <div class="col-md-3">
        <label>To:</label>
        <input type="date" name="to" value="{{ $to }}" class="form-control form-control-sm">
      </div>
      <div class="col-md-2 d-flex align-items-end">
        <button class="btn btn-primary btn-sm"><i class="fa fa-filter"></i> Filter</button>
      </div>
    </form>

    <div class="row">
      <div class="col-md-6">
        <h6 class="fw-bold">Income</h6>
        <table class="table table-sm table-bordered">
          @foreach($incomeAccounts as $acc)
            <tr>
              <td>{{ $acc->name }}</td>
              <td class="text-end">
                {{ number_format($acc->entries->sum('credit') - $acc->entries->sum('debit'), 2) }}
              </td>
            </tr>
          @endforeach
          <tr class="fw-bold">
            <td>Total Income</td>
            <td class="text-end">{{ number_format($totalIncome, 2) }}</td>
          </tr>
        </table>
      </div>

      <div class="col-md-6">
        <h6 class="fw-bold">Expenses</h6>
        <table class="table table-sm table-bordered">
          @foreach($expenseAccounts as $acc)
            <tr>
              <td>{{ $acc->name }}</td>
              <td class="text-end">
                {{ number_format($acc->entries->sum('debit') - $acc->entries->sum('credit'), 2) }}
              </td>
            </tr>
          @endforeach
          <tr class="fw-bold">
            <td>Total Expenses</td>
            <td class="text-end">{{ number_format($totalExpense, 2) }}</td>
          </tr>
        </table>
      </div>
    </div>

    <hr>
    <h5 class="text-end">
      Net Profit:
      <span class="{{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
        {{ number_format($netProfit, 2) }}
      </span>
    </h5>
  </div>
</div>
@endsection
