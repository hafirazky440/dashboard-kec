<?php

namespace App\Filament\Resources\Jalans;

use App\Filament\Resources\Concerns\HasGlobalSearchColumns;
use App\Filament\Resources\Jalans\Pages\CreateJalan;
use App\Filament\Resources\Jalans\Pages\EditJalan;
use App\Filament\Resources\Jalans\Pages\ListJalans;
use App\Filament\Resources\Jalans\Schemas\JalanForm;
use App\Filament\Resources\Jalans\Tables\JalansTable;
use App\Models\Jalan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Resource admin untuk daftar jalan desa per tahun.
 */
class JalanResource extends Resource
{
    use HasGlobalSearchColumns;

    /**
     * Kolom yang muncul di kotak pencarian global Cmd+K.
     *
     * @var array<int, string>
     */
    protected static array $searchableColumns = ['nama', 'tingkat'];

    protected static ?string $model = Jalan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static ?string $navigationLabel = 'Jalan Desa';

    protected static ?string $modelLabel = 'jalan';

    protected static ?string $pluralModelLabel = 'jalan';

    protected static string|UnitEnum|null $navigationGroup = 'Infrastruktur';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return JalanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JalansTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJalans::route('/'),
            'create' => CreateJalan::route('/create'),
            'edit' => EditJalan::route('/{record}/edit'),
        ];
    }
}
