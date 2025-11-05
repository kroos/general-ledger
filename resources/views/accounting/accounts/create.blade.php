@extends('layouts.app')

@section('content')
<div class="card col-md-8 mx-auto">
  <div class="card-header"><h5><i class="fa fa-plus"></i> New Account</h5></div>
  <div class="card-body">
    <form method="POST" action="{{ route('accounts.store') }}">
      @csrf
      <div class="mb-3">
        <label>Code</label>
        <input name="code" class="form-control form-control-sm" required>
      </div>
      <div class="mb-3">
        <label>Name</label>
        <input name="name" class="form-control form-control-sm" required>
      </div>
      <div class="mb-3">
        <label>Type</label>
        <select name="type" class="form-select form-select-sm select2" required>
          <option value="">Select Type</option>
          @foreach(['asset','liability','equity','income','expense'] as $type)
            <option value="{{ $type }}">{{ ucfirst($type) }}</option>
          @endforeach
        </select>
      </div>
      <div class="mb-3">
        <label>Parent Account</label>
        <select name="parent_id" class="form-select form-select-sm select2">
          <option value="">None</option>
          @foreach($parents as $id => $name)
            <option value="{{ $id }}">{{ $name }}</option>
          @endforeach
        </select>
      </div>
      <div class="mb-3">
        <label>Description</label>
        <textarea name="description" rows="2" class="form-control form-control-sm"></textarea>
      </div>
      <button class="btn btn-success btn-sm"><i class="fa fa-save"></i> Save</button>
      <a href="{{ route('accounts.index') }}" class="btn btn-secondary btn-sm">Cancel</a>
    </form>
  </div>
</div>
@endsection

@section('js')
$('.select2').select2({theme:'bootstrap-5'});
@endsection
