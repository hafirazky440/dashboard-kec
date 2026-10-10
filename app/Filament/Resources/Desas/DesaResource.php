<?php

namespace App\Filament\Resources\Desas;

use App\Filament\Resources\Concerns\HasGlobalSearchColumns;
use App\Filament\Resources\Desas\Pages\CreateDesa;
use App\Filament\Resources\Desas\Pages\EditDesa;
use App\Filament\Resources\Desas\Pages\ListDesas;
use App\Filament\Resources\Desas\Schemas\DesaForm;
use App\Filament\Resources\Desas\Tables\DesasTable;
use App\Models\Desa;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Resource admin untuk mengelola master data Desa.
 *
 * Setiap desa menyimpan luas wilayahnya sendiri (kolom luas_km2) dan
 * potensi/kategori tentang desa tersebut (kolom potensi), sehingga tabel
 * geografi dan potensi yang terpisah tidak diperlukan lagi.
 */
class DesaResource extends Resource
{
    use HasGlobalSearchColumns;

    /**
     * Kolom yang muncul di kotak pencarian global Cmd+K.
     *
     * @var array<int, string>
     */
    protected static array $searchableColumns = ['nama', 'potensi'];

    protected static ?string $model = Desa::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationLabel = 'Desa';

    protected static ?string $modelLabel = 'desa';

    protected static ?string $pluralModelLabel = 'desa';

    protected static string|UnitEnum|null $navigationGroup = 'Data Dasar';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return DesaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DesasTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDesas::route('/'),
            'create' => CreateDesa::route('/create'),
            'edit' => EditDesa::route('/{record}/edit'),
        ];
    }
}
