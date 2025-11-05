@extends('layouts.app')

@section('content')
<div class="col-sm-12">
  <form method="POST" action="{{ route('accounting.payments.store') }}" id="form" autocomplete="off">
    @csrf
    @include('accounting.payments._form')
  </form>
</div>
@endsection

@section('js')
$(function () {
  $('.select2').select2({ theme:'bootstrap-5', allowClear:true });

  $('#type').on('change', function() {
    const val = $(this).val();
    $('.source-select').addClass('d-none');
    if (val === 'receive') $('#invoice_select_wrap').removeClass('d-none');
    if (val === 'make') $('#bill_select_wrap').removeClass('d-none');
  });

  $('#form').on('submit', function(e) {
    e.preventDefault();
    swal.fire({
      title:'Record this payment?',
      icon:'question',
      showCancelButton:true,
      confirmButtonText:'Yes, save it'
    }).then(r=>{
      if(r.isConfirmed) this.submit();
    });
  });
});
@endsection
