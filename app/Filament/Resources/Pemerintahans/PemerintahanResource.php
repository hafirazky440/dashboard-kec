<?php

namespace App\Filament\Resources\Pemerintahans;

use App\Filament\Resources\Concerns\HasGlobalSearchColumns;
use App\Filament\Resources\Pemerintahans\Pages\CreatePemerintahan;
use App\Filament\Resources\Pemerintahans\Pages\EditPemerintahan;
use App\Filament\Resources\Pemerintahans\Pages\ListPemerintahans;
use App\Filament\Resources\Pemerintahans\Schemas\PemerintahanForm;
use App\Filament\Resources\Pemerintahans\Tables\PemerintahansTable;
use App\Models\Pemerintahan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Resource admin untuk data pegawai pemerintahan (PNS, PPPK, PPPK paruh waktu).
 */
class PemerintahanResource extends Resource
{
    use HasGlobalSearchColumns;

    /**
     * Kolom yang muncul di kotak pencarian global Cmd+K.
     *
     * @var array<int, string>
     */
    protected static array $searchableColumns = ['jenis'];

    protected static ?string $model = Pemerintahan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;

    protected static ?string $navigationLabel = 'Pegawai Pemerintahan';

    protected static ?string $modelLabel = 'data pemerintahan';

    protected static ?string $pluralModelLabel = 'data pemerintahan';

    protected static string|UnitEnum|null $navigationGroup = 'Pemerintahan';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return PemerintahanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PemerintahansTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPemerintahans::route('/'),
            'create' => CreatePemerintahan::route('/create'),
            'edit' => EditPemerintahan::route('/{record}/edit'),
        ];
    }
}
