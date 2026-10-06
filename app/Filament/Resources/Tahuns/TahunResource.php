<?php

namespace App\Filament\Resources\Tahuns;

use App\Filament\Resources\Concerns\HasGlobalSearchColumns;
use App\Filament\Resources\Tahuns\Pages\CreateTahun;
use App\Filament\Resources\Tahuns\Pages\EditTahun;
use App\Filament\Resources\Tahuns\Pages\ListTahuns;
use App\Filament\Resources\Tahuns\Schemas\TahunForm;
use App\Filament\Resources\Tahuns\Tables\TahunsTable;
use App\Models\Tahun;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Resource admin untuk mengelola Tahun statistik.
 *
 * Resource adalah titik masuk CRUD di Filament: model, form, tabel, dan rute
 * dikumpulkan di sini, lalu Filament menyusun halaman admin secara otomatis.
 */
class TahunResource extends Resource
{
    use HasGlobalSearchColumns;

    /**
     * Kolom yang muncul di kotak pencarian global Cmd+K.
     *
     * @var array<int, string>
     */
    protected static array $searchableColumns = ['judul', 'tahun'];

    protected static ?string $model = Tahun::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFlag;

    protected static ?string $navigationLabel = 'Tahun';

    protected static ?string $modelLabel = 'tahun';

    protected static ?string $pluralModelLabel = 'tahun';

    // Mengelompokkan menu di sidebar. Urutan grup diatur di AdminPanelProvider.
    protected static string|UnitEnum|null $navigationGroup = 'Data Dasar';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return TahunForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TahunsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTahuns::route('/'),
            'create' => CreateTahun::route('/create'),
            'edit' => EditTahun::route('/{record}/edit'),
        ];
    }
}
