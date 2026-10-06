<?php

namespace App\Filament\Resources\Murids;

use App\Filament\Resources\Concerns\HasGlobalSearchColumns;
use App\Filament\Resources\Murids\Pages\CreateMurid;
use App\Filament\Resources\Murids\Pages\EditMurid;
use App\Filament\Resources\Murids\Pages\ListMurids;
use App\Filament\Resources\Murids\Schemas\MuridForm;
use App\Filament\Resources\Murids\Tables\MuridsTable;
use App\Models\Murid;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class MuridResource extends Resource
{
    use HasGlobalSearchColumns;

    /**
     * Kolom yang muncul di kotak pencarian global Cmd+K.
     *
     * @var array<int, string>
     */
    protected static array $searchableColumns = ['jenjang'];

    protected static ?string $model = Murid::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?string $navigationLabel = 'Murid';

    protected static ?string $modelLabel = 'murid';

    protected static ?string $pluralModelLabel = 'murid';

    protected static string|UnitEnum|null $navigationGroup = 'Pendidikan';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return MuridForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MuridsTable::configure($table);
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
            'index' => ListMurids::route('/'),
            'create' => CreateMurid::route('/create'),
            'edit' => EditMurid::route('/{record}/edit'),
        ];
    }
}
