<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\DataSummaryWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            // Urutan grup mengikuti urutan bab pada buku Cicalengka Dalam
            // Angka, dari identitas kecamatan sampai potensi dan MBG. Tanpa
            // daftar ini, Filament menyusun grup berdasarkan abjad sehingga
            // sidebar tidak lagi sama dengan urutan sumbernya.
            ->navigationGroups([
                'Data Dasar',
                'Geografi',
                'Administrasi Kependudukan',
                'Pemerintahan',
                'Pendidikan',
                'Kesehatan',
                'Infrastruktur',
                'Potensi dan MBG',
            ])
            ->brandName('Cicalengka Dalam Angka')
            // Mode ketat: kalau suatu model belum punya policy, Filament
            // berhenti dengan pesan jelas, bukan dengan diam-diam mengizinkan
            // semua orang. Semua model domain sudah didaftarkan di
            // AppServiceProvider, jadi tidak akan ada yang gagal diam-diam.
            ->strictAuthorization()
            // Kotak pencarian global Cmd+K. Resource yang punya daftar kolom
            // pencarian akan otomatis muncul di sini.
            ->globalSearch()
            ->globalSearchDebounce('300ms')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                DataSummaryWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
