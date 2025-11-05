@extends('layouts.app')

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0"><i class="fa fa-book"></i> Journals</h5>
    <a href="{{ route('journals.create') }}" class="btn btn-sm btn-success">
      <i class="fa fa-plus"></i> New Journal
    </a>
  </div>
  <div class="card-body">
    <table id="journals-table" class="table table-striped table-bordered w-100"></table>
  </div>
</div>
@endsection

@section('js')
$(function(){
  $('#journals-table').DataTable({
    ajax: '{{ route("journals.index") }}',
    serverSide: true,
    processing: true,
    columns: [
      {data:'id', title:'ID'},
      {data:'date', title:'Date'},
      {data:'reference_no', title:'Reference'},
      {data:'ledger', title:'Ledger'},
      {data:'status', title:'Status'},
      {data:'description', title:'Description'},
      {data:'action', title:'Actions', orderable:false, searchable:false}
    ]
  });

$('.btn-delete').off('click').on('click', function(){
  const id = $(this).data('id');
  Swal.fire({
    title: 'Delete Journal?',
    text: 'This will soft-delete the record.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, delete it'
  }).then(res=>{
    if(res.isConfirmed){
      $.ajax({
        url: '{{ url("journals.destroy") }}/'+id,
        type: 'DELETE',
        data: {_token:'{{ csrf_token() }}'},
        success: ()=> location.reload()
      });
    }
  });
});




});
@endsection
