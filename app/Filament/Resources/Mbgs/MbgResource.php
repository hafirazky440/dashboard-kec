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
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class MbgResource extends Resource
{
    use HasGlobalSearchColumns;

    /**
     * Kolom yang muncul di kotak pencarian global Cmd+K.
     *
     * @var array<int, string>
     */
    protected static array $searchableColumns = ['jenis', 'satuan'];

    protected static ?string $model = Mbg::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCake;

    protected static ?string $navigationLabel = 'MBG';

    protected static ?string $modelLabel = 'data MBG';

    protected static ?string $pluralModelLabel = 'data MBG';

    protected static string|UnitEnum|null $navigationGroup = 'Potensi dan MBG';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return MbgForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MbgsTable::configure($table);
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
            'index' => ListMbgs::route('/'),
            'create' => CreateMbg::route('/create'),
            'edit' => EditMbg::route('/{record}/edit'),
        ];
    }
}
