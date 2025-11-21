@extends('layouts.app')

@section('content')
<div class="card">
  <div class="card-header">
    <h5 class="mb-0"><i class="fa fa-balance-scale"></i> Balance Sheet</h5>
  </div>

  <div class="card-body">
    <form method="get" class="row mb-3">
      <div class="col-md-3">
        <label>As of Date:</label>
        <input type="date" name="as_of" value="{{ $asOf }}" class="form-control form-control-sm">
      </div>
      <div class="col-md-2 d-flex align-items-end">
        <button class="btn btn-primary btn-sm"><i class="fa fa-filter"></i> View</button>
      </div>
    </form>

    <div class="row">
      <div class="col-md-4">
        <h6 class="fw-bold">Assets</h6>
        <table class="table table-sm table-bordered">
          @foreach($assets as $acc)
            <tr>
              <td>{{ $acc->account }}</td>
              <td class="text-end">{{ number_format($acc->hasmanyjournalentries->sum('debit') - $acc->hasmanyjournalentries->sum('credit'), 2) }}</td>
            </tr>
          @endforeach
          <tr class="fw-bold">
            <td>Total Assets</td>
            <td class="text-end">{{ number_format($totalAssets, 2) }}</td>
          </tr>
        </table>
      </div>

      <div class="col-md-4">
        <h6 class="fw-bold">Liabilities</h6>
        <table class="table table-sm table-bordered">
          @foreach($liabilities as $acc)
            <tr>
              <td>{{ $acc->account }}</td>
              <td class="text-end">{{ number_format($acc->hasmanyjournalentries->sum('credit') - $acc->hasmanyjournalentries->sum('debit'), 2) }}</td>
            </tr>
          @endforeach
          <tr class="fw-bold">
            <td>Total Liabilities</td>
            <td class="text-end">{{ number_format($totalLiabilities, 2) }}</td>
          </tr>
        </table>
      </div>

      <div class="col-md-4">
        <h6 class="fw-bold">Equity</h6>
        <table class="table table-sm table-bordered">
          @foreach($equity as $acc)
            <tr>
              <td>{{ $acc->account }}</td>
              <td class="text-end">{{ number_format($acc->hasmanyjournalentries->sum('credit') - $acc->hasmanyjournalentries->sum('debit'), 2) }}</td>
            </tr>
          @endforeach
          <tr class="fw-bold">
            <td>Total Equity</td>
            <td class="text-end">{{ number_format($totalEquity, 2) }}</td>
          </tr>
        </table>
      </div>
    </div>

    <hr>
    <h5 class="text-end">
      Difference:
      <span class="{{ $balance == 0 ? 'text-success' : 'text-danger' }}">
        {{ number_format($balance, 2) }}
      </span>
    </h5>
  </div>
</div>
@endsection
