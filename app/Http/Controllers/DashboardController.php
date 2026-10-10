<?php

namespace App\Http\Controllers;

use App\Models\Desa;
use App\Services\StatistikService;
use Illuminate\Contracts\View\View;

/**
 * Halaman publik dashboard statistik.
 *
 * Tanpa autentikasi. Semua angka yang ditampilkan berasal dari database,
 * bukan ditulis di dalam view, sehingga perbaikan data langsung terlihat di
 * halaman tanpa perlu menyentuh kode.
 *
 * Skema tabel mengikuti kolom pada file CSV, yang tidak punya dimensi tahun,
 * jadi halaman ini menyajikan satu snapshot terbaru tanpa pemilih tahun.
 */
class DashboardController extends Controller
{
    public function __construct(
        private readonly StatistikService $statistik,
    ) {}

    /**
     * Halaman utama dashboard.
     */
    public function index(): View
    {
        return view('dashboard', $this->statistik->ringkasan());
    }

    /**
     * Halaman detail satu desa.
     *
     * Desa dicari lewat route model binding berdasarkan id, karena tabel desa
     * tidak lagi menyimpan kolom slug.
     */
    public function desa(Desa $desa): View
    {
        return view('desa', $this->statistik->untukDesa($desa));
    }
}
