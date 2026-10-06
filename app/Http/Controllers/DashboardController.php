<?php

namespace App\Http\Controllers;

use App\Models\Desa;
use App\Services\StatistikService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Halaman publik dashboard statistik.
 *
 * Tanpa autentikasi. Semua angka yang ditampilkan berasal dari database,
 * bukan ditulis di dalam view, sehingga perbaikan data langsung terlihat di
 * halaman tanpa perlu menyentuh kode.
 *
 * Tahun dipilih lewat query parameter ?tahun=, bukan lewat path. Alasannya,
 * tahun bukan bagian dari identitas halaman ini, dan cara ini membuat
 * halaman tetap bisa ditandai/dibagikan dengan satu URL yang sama.
 */
class DashboardController extends Controller
{
    public function __construct(
        private readonly StatistikService $statistik,
    ) {}

    /**
     * Halaman utama dashboard.
     */
    public function index(Request $request): View
    {
        $tahun = $this->statistik->cariTahun($request->query('tahun'));

        return view('dashboard', $this->statistik->untukTahun($tahun));
    }

    /**
     * Halaman detail satu desa.
     *
     * Desa dicari lewat slug supaya URL-nya enak dibaca dan tetap valid
     * meskipun nama desa diubah, selama slug-nya ikut diperbarui.
     */
    public function desa(Request $request, string $slug): View
    {
        $desa = Desa::where('slug', $slug)->firstOrFail();
        $tahun = $this->statistik->cariTahun($request->query('tahun'));

        return view('desa', $this->statistik->untukDesa($desa, $tahun));
    }
}
