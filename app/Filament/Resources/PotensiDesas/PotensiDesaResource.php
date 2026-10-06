<?php

namespace App\Filament\Resources\PotensiDesas;

use App\Filament\Resources\Concerns\HasGlobalSearchColumns;
use App\Filament\Resources\PotensiDesas\Pages\CreatePotensiDesa;
use App\Filament\Resources\PotensiDesas\Pages\EditPotensiDesa;
use App\Filament\Resources\PotensiDesas\Pages\ListPotensiDesas;
use App\Filament\Resources\PotensiDesas\Schemas\PotensiDesaForm;
use App\Filament\Resources\PotensiDesas\Tables\PotensiDesasTable;
use App\Models\PotensiDesa;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PotensiDesaResource extends Resource
{
    use HasGlobalSearchColumns;

    /**
     * Kolom yang muncul di kotak pencarian global Cmd+K.
     *
     * @var array<int, string>
     */
    protected static array $searchableColumns = ['desa.nama', 'kategori'];

    protected static ?string $model = PotensiDesa::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static ?string $navigationLabel = 'Potensi Desa';

    protected static ?string $modelLabel = 'potensi desa';

    protected static ?string $pluralModelLabel = 'potensi desa';

    protected static string|UnitEnum|null $navigationGroup = 'Potensi dan MBG';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return PotensiDesaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PotensiDesasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPotensiDesas::route('/'),
            'create' => CreatePotensiDesa::route('/create'),
            'edit' => EditPotensiDesa::route('/{record}/edit'),
        ];
    }
}
