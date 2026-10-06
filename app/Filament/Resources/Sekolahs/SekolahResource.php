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
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Resource admin untuk jumlah sekolah per jenjang dan jenis per tahun.
 *
 * Kombinasi jenjang dan jenis unik dalam satu tahun, jadi data disimpan sebagai
 * baris terpisah kecil, bukan satu baris panjang dengan banyak kolom.
 */
class SekolahResource extends Resource
{
    use HasGlobalSearchColumns;

    /**
     * Kolom yang muncul di kotak pencarian global Cmd+K.
     *
     * @var array<int, string>
     */
    protected static array $searchableColumns = ['jenis', 'jenjang'];

    protected static ?string $model = Sekolah::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

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

    public static function getRelations(): array
    {
        return [
            //
        ];
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
