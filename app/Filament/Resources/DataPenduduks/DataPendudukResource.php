<?php

namespace App\Filament\Resources\DataPenduduks;

use App\Filament\Resources\Concerns\HasGlobalSearchColumns;
use App\Filament\Resources\DataPenduduks\Pages\CreateDataPenduduk;
use App\Filament\Resources\DataPenduduks\Pages\EditDataPenduduk;
use App\Filament\Resources\DataPenduduks\Pages\ListDataPenduduks;
use App\Filament\Resources\DataPenduduks\Schemas\DataPendudukForm;
use App\Filament\Resources\DataPenduduks\Tables\DataPenduduksTable;
use App\Models\DataPenduduk;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Resource admin untuk data penduduk tingkat kecamatan.
 *
 * Satu baris untuk satu indikator (misal Total Penduduk, Laki-laki,
 * Perempuan, RT, RW, Dusun), dengan jumlah pada kolom jumlah.
 */
class DataPendudukResource extends Resource
{
    use HasGlobalSearchColumns;

    /**
     * @var array<int, string>
     */
    protected static array $searchableColumns = ['nama_data'];

    protected static ?string $model = DataPenduduk::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Data Penduduk';

    protected static ?string $modelLabel = 'data penduduk';

    protected static ?string $pluralModelLabel = 'data penduduk';

    protected static string|UnitEnum|null $navigationGroup = 'Data Dasar';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return DataPendudukForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DataPenduduksTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDataPenduduks::route('/'),
            'create' => CreateDataPenduduk::route('/create'),
            'edit' => EditDataPenduduk::route('/{record}/edit'),
        ];
    }
}
