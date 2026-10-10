<?php

namespace App\Filament\Resources\Pengairans;

use App\Filament\Resources\Concerns\HasGlobalSearchColumns;
use App\Filament\Resources\Pengairans\Pages\CreatePengairan;
use App\Filament\Resources\Pengairans\Pages\EditPengairan;
use App\Filament\Resources\Pengairans\Pages\ListPengairans;
use App\Filament\Resources\Pengairans\Schemas\PengairanForm;
use App\Filament\Resources\Pengairans\Tables\PengairansTable;
use App\Models\Pengairan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Resource admin untuk jaringan pengairan / irigasi.
 */
class PengairanResource extends Resource
{
    use HasGlobalSearchColumns;

    /**
     * @var array<int, string>
     */
    protected static array $searchableColumns = ['nama', 'kewenangan'];

    protected static ?string $model = Pengairan::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cloud';

    protected static ?string $navigationLabel = 'Pengairan';

    protected static ?string $modelLabel = 'pengairan';

    protected static ?string $pluralModelLabel = 'pengairan';

    protected static string|UnitEnum|null $navigationGroup = 'Infrastruktur';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return PengairanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PengairansTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPengairans::route('/'),
            'create' => CreatePengairan::route('/create'),
            'edit' => EditPengairan::route('/{record}/edit'),
        ];
    }
}
