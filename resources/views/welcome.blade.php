@extends('layouts.app')

@section('content')
<div class="col-sm-12 d-flex flex-column align-items-center justify-content-center">
	<div class="card my-auto">
		<div class="card-header d-flex justify-content-between">
			<h4 class="my-auto">Home </h4>
		</div>
		<div class="card-body d-flex align-items-center justify-content-center">
			<h1 class="">{{ config('app.name', 'Laravel') }}</h1>
		</div>
	</div>

</div>
@endsection

@section('js')
	@endsection
