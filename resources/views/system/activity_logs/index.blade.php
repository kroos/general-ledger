@extends('layouts.app')

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0"><i class="fa fa-history"></i> Activity Logs</h5>
  </div>
  <div class="card-body">
    <table id="logs-table" class="table table-striped table-bordered w-100"></table>
  </div>
</div>
@endsection

@section('js')
$(function(){
  $('#logs-table').DataTable({
    ajax: '{{ route("activity-logs.index") }}',
    serverSide: true,
    processing: true,
    columns: [
      {data:'id', title:'#', width:'3%'},
      {data:'event', title:'Event', width:'10%'},
      {data:'model', title:'Model', width:'20%'},
      {data:'user', title:'User', width:'15%'},
      {data:'ip', title:'IP Address', width:'10%'},
      {data:'created_at', title:'Timestamp', width:'15%'},
      {data:'action', title:'Action', orderable:false, searchable:false, width:'5%'},
    ],
    order:[[0,'desc']],
  });
});
@endsection
