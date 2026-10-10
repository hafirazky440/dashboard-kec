<?php

namespace App\Filament\Resources\PegawaiKecamatans;

use App\Filament\Resources\Concerns\HasGlobalSearchColumns;
use App\Filament\Resources\PegawaiKecamatans\Pages\CreatePegawaiKecamatan;
use App\Filament\Resources\PegawaiKecamatans\Pages\EditPegawaiKecamatan;
use App\Filament\Resources\PegawaiKecamatans\Pages\ListPegawaiKecamatans;
use App\Filament\Resources\PegawaiKecamatans\Schemas\PegawaiKecamatanForm;
use App\Filament\Resources\PegawaiKecamatans\Tables\PegawaiKecamatansTable;
use App\Models\PegawaiKecamatan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Resource admin untuk pegawai kecamatan.
 */
class PegawaiKecamatanResource extends Resource
{
    use HasGlobalSearchColumns;

    /**
     * @var array<int, string>
     */
    protected static array $searchableColumns = ['status'];

    protected static ?string $model = PegawaiKecamatan::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Pegawai Kecamatan';

    protected static ?string $modelLabel = 'pegawai kecamatan';

    protected static ?string $pluralModelLabel = 'pegawai kecamatan';

    protected static string|UnitEnum|null $navigationGroup = 'Data Dasar';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return PegawaiKecamatanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PegawaiKecamatansTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPegawaiKecamatans::route('/'),
            'create' => CreatePegawaiKecamatan::route('/create'),
            'edit' => EditPegawaiKecamatan::route('/{record}/edit'),
        ];
    }
}
