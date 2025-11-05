@extends('layouts.app')

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0"><i class="fa fa-balance-scale"></i> Trial Balance</h5>
  </div>

  <div class="card-body">
    <form method="get" class="row mb-3">
      <div class="col-md-3">
        <label>From:</label>
        <input type="date" name="from" class="form-control form-control-sm" value="{{ $from }}">
      </div>
      <div class="col-md-3">
        <label>To:</label>
        <input type="date" name="to" class="form-control form-control-sm" value="{{ $to }}">
      </div>
      <div class="col-md-3 d-flex align-items-end">
        <button type="submit" class="btn btn-primary btn-sm">
          <i class="fa fa-filter"></i> Filter
        </button>
      </div>
    </form>

    <table class="table table-sm table-striped table-bordered">
      <thead class="table-light">
        <tr>
          <th>Account</th>
          <th class="text-end">Debit</th>
          <th class="text-end">Credit</th>
          <th class="text-end">Balance</th>
          <th class="text-center">Type</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($rows as $r)
          <tr>
            <td>{{ $r['account'] }}</td>
            <td class="text-end">{{ $r['debit'] }}</td>
            <td class="text-end">{{ $r['credit'] }}</td>
            <td class="text-end">{{ $r['balance'] }}</td>
            <td class="text-center">{{ $r['type'] }}</td>
          </tr>
        @endforeach
      </tbody>
      <tfoot class="fw-bold">
        <tr>
          <td class="text-end">Total</td>
          <td class="text-end">{{ number_format($totalDebit, 2) }}</td>
          <td class="text-end">{{ number_format($totalCredit, 2) }}</td>
          <td colspan="2"></td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>
@endsection
