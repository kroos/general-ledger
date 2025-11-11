<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::middleware('guest')->group(function () {
	Route::get('/', function () {
		return view('welcome');
	});

	Route::post('/', function (Request $request) {
		// dd($request->all());
		$request->validate([
			'skills' => 'required|array|min:1',
			'experiences' => 'required|array|min:1',
			'countries' => 'required|array|min:1',
			'skills.*.name' => 'required',
			'skills.*.skill' => 'required',
			'skills.*.subskills.*.subskill' => 'required',
			'skills.*.subskills.*.years' => 'required',
			'experiences.*.name' => 'required',
			'experiences.*.id' => 'required',
			'countries.*.country_id' => 'required',
			'countries.*.state_id' => 'required',
		], [
			'skills.required' => 'Please add at least one skill.',
			'experiences.required' => 'Please add at least one experience.',
			'countries.required' => 'Please add at least one country',
				'skills.*.name' => 'Please insert :attribute at #:position',   //:index
				'skills.*.skill' => 'Please insert :attribute at #:position',   //:index
				'skills.*.subskills.*.subskill' => 'Please insert :attribute at #:position',   //:index
				'skills.*.subskills.*.years' => 'Please insert :attribute at #:position',   //:index
				'experiences.*.name' => 'Please insert :attribute at #:position',   //:index
				'experiences.*.id' => 'Please insert :attribute at #:position',   //:index
				'countries.*.country_id' => 'Please insert :attribute at #:position',
				'countries.*.state_id' => 'Please insert :attribute at #:position',
			], [
				'countries.*.country_id' => 'Country',
				'countries.*.state_id' => 'State',
				'skills.*.name' => 'Name',
				'skills.*.skill' => 'Skill',
				'skills.*.subskills.*.subskill' => 'Sub-Skill',
				'skills.*.subskills.*.years' => 'Years',
				'experiences.*.name' => 'Name',
				'experiences.*.id' => 'ID',
			]);
		return redirect()->back()->with('success', 'Successfully submitted form');
	})->name('welcome');




});


require __DIR__.'/auth.php';
