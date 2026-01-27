<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Redirect;

// Redirect root to admin panel by default
Route::redirect('/', '/admin');

// Ruta para crear orden agrupada desde la Propuesta Maestra
Route::post('/admin/crear-orden-agrupada', \App\Http\Controllers\CrearOrdenAgrupadaController::class)
    ->name('admin.crear-orden-agrupada');

// Keep welcome route available if needed under /welcome
Route::get('/welcome', function () {
    return view('welcome');
});
