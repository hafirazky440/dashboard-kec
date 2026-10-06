<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute dashboard publik
|--------------------------------------------------------------------------
|
| Halaman ini terbuka untuk umum, tanpa perlu login. Panel admin yang
| butuh autentikasi berada di /admin dan ditangani Filament.
|
| Tahun dipilih lewat query parameter ?tahun=, contoh:
|   /?tahun=2026
|   /desa/nagrog?tahun=2026
|
| Query parameter dipakai supaya halaman tetap punya satu URL yang bisa
| ditandai dan dibagikan, berapa pun tahun yang sedang dilihat.
*/

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/desa/{slug}', [DashboardController::class, 'desa'])
    ->where('slug', '[a-z0-9\-]+')
    ->name('desa');