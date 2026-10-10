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
| Halaman desa diakses lewat id, misalnya /desa/1, karena tabel desa tidak
| punya kolom slug lagi. Route model binding memastikan id yang tidak ada
| langsung melempar 404.
|
| Tidak ada query parameter tahun: skema tabel mengikuti kolom pada file CSV
| yang tidak punya dimensi tahun, jadi dashboard selalu menampilkan satu
| snapshot terbaru.
*/

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/desa/{desa}', [DashboardController::class, 'desa'])
    ->whereNumber('desa')
    ->name('desa');
