<?php

namespace App\Filament\Resources\Mbgs;

use App\Filament\Resources\Concerns\HasGlobalSearchColumns;
use App\Filament\Resources\Mbgs\Pages\CreateMbg;
use App\Filament\Resources\Mbgs\Pages\EditMbg;
use App\Filament\Resources\Mbgs\Pages\ListMbgs;
use App\Filament\Resources\Mbgs\Schemas\MbgForm;
use App\Filament\Resources\Mbgs\Tables\MbgsTable;
use App\Models\Mbg;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Resource admin untuk program Makan Bergizi Gratis (MBG / SPPG).
 */
class MbgResource extends Resource
{
    use HasGlobalSearchColumns;

    /**
     * @var array<int, string>
     */
    protected static array $searchableColumns = ['nama'];

    protected static ?string $model = Mbg::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationLabel = 'MBG / SPPG';

    protected static ?string $modelLabel = 'program MBG';

    protected static ?string $pluralModelLabel = 'program MBG';

    protected static string|UnitEnum|null $navigationGroup = 'Potensi dan MBG';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return MbgForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MbgsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMbgs::route('/'),
            'create' => CreateMbg::route('/create'),
            'edit' => EditMbg::route('/{record}/edit'),
        ];
    }
}
