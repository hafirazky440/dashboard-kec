<?php

namespace App\Filament\Resources\SaranaPerdagangans;

use App\Filament\Resources\Concerns\HasGlobalSearchColumns;
use App\Filament\Resources\SaranaPerdagangans\Pages\CreateSaranaPerdagangan;
use App\Filament\Resources\SaranaPerdagangans\Pages\EditSaranaPerdagangan;
use App\Filament\Resources\SaranaPerdagangans\Pages\ListSaranaPerdagangans;
use App\Filament\Resources\SaranaPerdagangans\Schemas\SaranaPerdaganganForm;
use App\Filament\Resources\SaranaPerdagangans\Tables\SaranaPerdagangansTable;
use App\Models\SaranaPerdagangan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Resource admin untuk sarana perdagangan (pasar dan pusat perdagangan).
 */
class SaranaPerdaganganResource extends Resource
{
    use HasGlobalSearchColumns;

    /**
     * @var array<int, string>
     */
    protected static array $searchableColumns = ['nama', 'jenis', 'lokasi'];

    protected static ?string $model = SaranaPerdagangan::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationLabel = 'Sarana Perdagangan';

    protected static ?string $modelLabel = 'sarana perdagangan';

    protected static ?string $pluralModelLabel = 'sarana perdagangan';

    protected static string|UnitEnum|null $navigationGroup = 'Infrastruktur';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return SaranaPerdaganganForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SaranaPerdagangansTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSaranaPerdagangans::route('/'),
            'create' => CreateSaranaPerdagangan::route('/create'),
            'edit' => EditSaranaPerdagangan::route('/{record}/edit'),
        ];
    }
}
