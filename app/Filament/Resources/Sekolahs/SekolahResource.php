<?php

namespace App\Filament\Resources\Sekolahs;

use App\Filament\Resources\Concerns\HasGlobalSearchColumns;
use App\Filament\Resources\Sekolahs\Pages\CreateSekolah;
use App\Filament\Resources\Sekolahs\Pages\EditSekolah;
use App\Filament\Resources\Sekolahs\Pages\ListSekolahs;
use App\Filament\Resources\Sekolahs\Schemas\SekolahForm;
use App\Filament\Resources\Sekolahs\Tables\SekolahsTable;
use App\Models\Sekolah;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Resource admin untuk data sekolah.
 *
 * Tabel sekolah pada sumber cetakan hanya membedakan sekolah negeri dan
 * swasta, tanpa rincian jenjang seperti SD atau SMP.
 */
class SekolahResource extends Resource
{
    use HasGlobalSearchColumns;

    /**
     * @var array<int, string>
     */
    protected static array $searchableColumns = ['jenis'];

    protected static ?string $model = Sekolah::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Sekolah';

    protected static ?string $modelLabel = 'sekolah';

    protected static ?string $pluralModelLabel = 'sekolah';

    protected static string|UnitEnum|null $navigationGroup = 'Pendidikan';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return SekolahForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SekolahsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSekolahs::route('/'),
            'create' => CreateSekolah::route('/create'),
            'edit' => EditSekolah::route('/{record}/edit'),
        ];
    }
}
