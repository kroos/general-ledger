@extends('layouts.app')

@section('content')
<div class="col-sm-12 d-flex flex-column align-items-center justify-content-center">
	<h3 class="">Please select a company to continue.</h3>
	<form method="POST" action="{{ route('company.store') }}" accept-charset="UTF-8" id="form" autocomplete="off" class="needs-validation" enctype="multipart/form-data">
		@csrf

		<div class="form-group row m-2 @error('company_id') has-error @enderror">
			<label for="company_id" class="col-sm-4 col-form-label col-form-label-sm">Select Company : </label>
			<div class="col-sm-8">
				<select name="company_id" id="company_id" class="form-select form-select-sm col-sm-12 @error('company_id') is-invalid @enderror" required autofocus>
					<option value="">Please choose</option>
					@foreach($collection as $k1 => $v1)
						<option value="{{ $k1 }}" {{ (old('company_id', @$variable->company_id) == $k1)?'selected':NULL }}>{{ $v1 }}</option>
					@endforeach
				</select>
				@error('username') <div class="invalid-feedback fw-lighter">{{ $message }}</div> @enderror
			</div>
		</div>

		<div class="m-2">
			<button type="submit" class="btn btn-sm btn-outline-primary m-3">
				{{ __('Continue') }}
			</button>
		</div>
	</form>
</div>
@endsection

@section('js')

$('#company_id').select2({
	placeholder: 'Select Company',
	allowClear: true,
	closeOnSelect: true,
});

// Initialize BootstrapValidator
$('#form').bootstrapValidator({
	feedbackIcons: {
		valid: 'fas fa-light fa-check',
		invalid: 'fas fa-sharp fa-light fa-xmark',
		validating: 'fas fa-duotone fa-spinner-third'
	},
	fields: {
		company_id: {
			validators: {
				notEmpty: {
					message: 'Please select a company'
				}
			}
		}
	}
});

// Update validation when Select2 changes
// $('#company_id').on('change', function() {
// 	$('#form').bootstrapValidator('revalidateField', 'company_id');
//
// 	// Clear server-side errors
// 	var errorElement = $(this).closest('.form-group').find('.invalid-feedback');
// 	if (errorElement.length && !errorElement.hasClass('d-block')) {
// 		errorElement.remove();
// 	}
// });

@endsection
