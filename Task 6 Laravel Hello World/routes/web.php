<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HelloController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/page2', function () {
    return view('page2');
});

// Hello World controller
Route::get('/hello', [HelloController::class, 'index']);
