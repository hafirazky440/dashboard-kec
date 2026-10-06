<?php

namespace App\Filament\Resources\ProfilKecamatans;

use App\Filament\Resources\Concerns\HasGlobalSearchColumns;
use App\Filament\Resources\ProfilKecamatans\Pages\CreateProfilKecamatan;
use App\Filament\Resources\ProfilKecamatans\Pages\EditProfilKecamatan;
use App\Filament\Resources\ProfilKecamatans\Pages\ListProfilKecamatans;
use App\Filament\Resources\ProfilKecamatans\Schemas\ProfilKecamatanForm;
use App\Filament\Resources\ProfilKecamatans\Tables\ProfilKecamatansTable;
use App\Models\ProfilKecamatan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Resource admin untuk Profil Kecamatan per tahun.
 *
 * Tabel ini hanya punya satu baris per tahun (dijamin unique constraint
 * tahun_id), jadi menjadi parameter-parameter scalars untuk seluruh dashboard.
 */
class ProfilKecamatanResource extends Resource
{
    use HasGlobalSearchColumns;

    /**
     * Kolom yang muncul di kotak pencarian global Cmd+K.
     *
     * @var array<int, string>
     */
    protected static array $searchableColumns = ['catatan'];

    protected static ?string $model = ProfilKecamatan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?string $navigationLabel = 'Profil Kecamatan';

    protected static ?string $modelLabel = 'profil kecamatan';

    protected static ?string $pluralModelLabel = 'profil kecamatan';

    protected static string|UnitEnum|null $navigationGroup = 'Data Dasar';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return ProfilKecamatanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProfilKecamatansTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProfilKecamatans::route('/'),
            'create' => CreateProfilKecamatan::route('/create'),
            'edit' => EditProfilKecamatan::route('/{record}/edit'),
        ];
    }
}
