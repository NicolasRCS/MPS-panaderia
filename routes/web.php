<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Redirect;

// Redirect root to admin panel by default
Route::redirect('/', '/admin');

// Keep welcome route available if needed under /welcome
Route::get('/welcome', function () {
    return view('welcome');
});
