<?php

namespace App\Filament\Resources\Kesehatans;

use App\Filament\Resources\Concerns\HasGlobalSearchColumns;
use App\Filament\Resources\Kesehatans\Pages\CreateKesehatan;
use App\Filament\Resources\Kesehatans\Pages\EditKesehatan;
use App\Filament\Resources\Kesehatans\Pages\ListKesehatans;
use App\Filament\Resources\Kesehatans\Schemas\KesehatanForm;
use App\Filament\Resources\Kesehatans\Tables\KesehatansTable;
use App\Models\Kesehatan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class KesehatanResource extends Resource
{
    use HasGlobalSearchColumns;

    /**
     * Kolom yang muncul di kotak pencarian global Cmd+K.
     *
     * @var array<int, string>
     */
    protected static array $searchableColumns = ['jenis'];

    protected static ?string $model = Kesehatan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHeart;

    protected static ?string $navigationLabel = 'Kesehatan';

    protected static ?string $modelLabel = 'sarana kesehatan';

    protected static ?string $pluralModelLabel = 'sarana kesehatan';

    protected static string|UnitEnum|null $navigationGroup = 'Kesehatan';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return KesehatanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KesehatansTable::configure($table);
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
            'index' => ListKesehatans::route('/'),
            'create' => CreateKesehatan::route('/create'),
            'edit' => EditKesehatan::route('/{record}/edit'),
        ];
    }
}
