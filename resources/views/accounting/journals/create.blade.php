@extends('layouts.app')

@section('content')
<form method="POST" action="{{ route('journals.store') }}" id="journalForm">
  @csrf
  @include('accounting.journals._form')
</form>
@endsection

@section('js')
$('#ledger_type_id').select2({theme:'bootstrap-5'});
$('.account-select').select2({theme:'bootstrap-5'});

$('#addRow').on('click',function(){
  const i = $('#entries tr').length;
  $('#entries').append(`
    <tr>
      <td><select name="entries[${i}][account_id]" class="form-select form-select-sm account-select">@foreach($accounts as $id=>$name)<option value="{{ $id }}">{{ $name }}</option>@endforeach</select></td>
      <td><input type="number" step="0.01" name="entries[${i}][debit]" class="form-control form-control-sm text-end"></td>
      <td><input type="number" step="0.01" name="entries[${i}][credit]" class="form-control form-control-sm text-end"></td>
      <td><button type="button" class="btn btn-sm btn-danger removeRow"><i class="fa fa-times"></i></button></td>
    </tr>`);
  $('.account-select').select2({theme:'bootstrap-5'});
});

$(document).on('click','.removeRow',function(){
  $(this).closest('tr').remove();
});
@endsection
