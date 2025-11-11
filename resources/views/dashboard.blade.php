@extends('layouts.app')

@section('content')
<div class="col-sm-12 d-flex flex-column align-items-center justify-content-center">
<?php
// if (request()->session()->missing('users')) {
// 	request()->session()->put('users', \Auth::user());
// }
// dd(request()->session()->all(), \Auth::user())
// request()->session()->flush();
?>
	<h3>Dashboard</h3>
	<p class="text-gray text-center">You're logged in!</p>

	<div class="card col-sm-6">
		<div class="card-header">Test API</div>
		<div class="card-body">
			<form action="" method="POST" id="form" class="" enctype="multipart/form-data">
				@csrf

				<div class="row col-sm-6 @error('option') has-error @enderror">
					<label for="opt" class="col-form-label col-sm-4">Select 2:</label>
					<div class="col-sm-8 my-auto">
						<select name="option" id="opt" value="{{ old('option')}}" class="form-select form-select-sm col-sm-8 @error('option') is-invalid @enderror" placeholder="Please choose"></select>
					</div>
					@error('option')
						<div class="invalid-feedback">{{ $message }}</div>
					@enderror
				</div>


				<div class="mt-3">
					<button type="submit" class="btn btn-sm btn-success">Save</button>
				</div>
			</form>
		</div>
	</div>


</div>
@endsection

@section('js')

	$('#opt').select2({
		theme: 'bootstrap-5',
		placeholder: 'Please choose',
		allowClear: true,
		closeOnSelect: true,
		width: '100%',
		ajax: {
			url: '{{ route('getYesNoOptions') }}',
			type: 'GET',
			dataType: 'json',
			delay: 250,											// Delay to reduce server requests
			data: function (params) {
				return {
					_token: '{!! csrf_token() !!}',
					search: params.term,				// Search query
				}
			},
			processResults: function (data) {
				return {
					results: data.map(function(item) {
						return {
							id: item.id,
							text: item.option,
							raw: item
						}
					})
				};
			}
		},
	});
	@if(null !== old('option'))
		var newOptionType = new Option('{!! \App\Models\YesNoOption::find(old('option'))->value !!}', '{{ old('option') }}', true, true);
		$('#opt').append(newOptionType).trigger('change');
	@endif

@endsection
