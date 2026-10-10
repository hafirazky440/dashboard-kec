<?php

namespace App\Filament\Resources\RuasJalans;

use App\Filament\Resources\Concerns\HasGlobalSearchColumns;
use App\Filament\Resources\RuasJalans\Pages\CreateRuasJalan;
use App\Filament\Resources\RuasJalans\Pages\EditRuasJalan;
use App\Filament\Resources\RuasJalans\Pages\ListRuasJalans;
use App\Filament\Resources\RuasJalans\Schemas\RuasJalanForm;
use App\Filament\Resources\RuasJalans\Tables\RuasJalansTable;
use App\Models\RuasJalan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Resource admin untuk ruas jalan.
 */
class RuasJalanResource extends Resource
{
    use HasGlobalSearchColumns;

    /**
     * @var array<int, string>
     */
    protected static array $searchableColumns = ['nama', 'status'];

    protected static ?string $model = RuasJalan::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationLabel = 'Ruas Jalan';

    protected static ?string $modelLabel = 'ruas jalan';

    protected static ?string $pluralModelLabel = 'ruas jalan';

    protected static string|UnitEnum|null $navigationGroup = 'Infrastruktur';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return RuasJalanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RuasJalansTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRuasJalans::route('/'),
            'create' => CreateRuasJalan::route('/create'),
            'edit' => EditRuasJalan::route('/{record}/edit'),
        ];
    }
}
