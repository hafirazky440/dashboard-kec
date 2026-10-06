<?php

namespace App\Filament\Resources\GeografiDesas;

use App\Filament\Resources\Concerns\HasGlobalSearchColumns;
use App\Filament\Resources\GeografiDesas\Pages\CreateGeografiDesa;
use App\Filament\Resources\GeografiDesas\Pages\EditGeografiDesa;
use App\Filament\Resources\GeografiDesas\Pages\ListGeografiDesas;
use App\Filament\Resources\GeografiDesas\Schemas\GeografiDesaForm;
use App\Filament\Resources\GeografiDesas\Tables\GeografiDesasTable;
use App\Models\GeografiDesa;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Resource admin untuk luas wilayah setiap desa per tahun.
 */
class GeografiDesaResource extends Resource
{
    use HasGlobalSearchColumns;

    /**
     * Kolom yang muncul di kotak pencarian global Cmd+K.
     *
     * @var array<int, string>
     */
    protected static array $searchableColumns = ['desa.nama'];

    protected static ?string $model = GeografiDesa::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMap;

    protected static ?string $navigationLabel = 'Luas Wilayah Desa';

    protected static ?string $modelLabel = 'luas wilayah desa';

    protected static ?string $pluralModelLabel = 'luas wilayah desa';

    protected static string|UnitEnum|null $navigationGroup = 'Geografi';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return GeografiDesaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GeografiDesasTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGeografiDesas::route('/'),
            'create' => CreateGeografiDesa::route('/create'),
            'edit' => EditGeografiDesa::route('/{record}/edit'),
        ];
    }
}
