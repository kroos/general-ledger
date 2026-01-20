<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::middleware('guest')->group(function () {
	Route::get('/', function () {
		return view('welcome');
	});

});


require __DIR__.'/auth.php';
