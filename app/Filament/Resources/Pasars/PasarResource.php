<?php

namespace App\Filament\Resources\Pasars;

use App\Filament\Resources\Concerns\HasGlobalSearchColumns;
use App\Filament\Resources\Pasars\Pages\CreatePasar;
use App\Filament\Resources\Pasars\Pages\EditPasar;
use App\Filament\Resources\Pasars\Pages\ListPasars;
use App\Filament\Resources\Pasars\Schemas\PasarForm;
use App\Filament\Resources\Pasars\Tables\PasarsTable;
use App\Models\Pasar;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Resource admin untuk pasar, bank, dan koperasi per tahun.
 *
 * Satu resource ini dipakai bersama karena ketiganya berasal dari tabel yang
 * sama di sumber, hanya berbeda jenis transaksi.
 */
class PasarResource extends Resource
{
    use HasGlobalSearchColumns;

    /**
     * Kolom yang muncul di kotak pencarian global Cmd+K.
     *
     * @var array<int, string>
     */
    protected static array $searchableColumns = ['nama', 'lokasi'];

    protected static ?string $model = Pasar::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingCart;

    protected static ?string $navigationLabel = 'Pasar';

    protected static ?string $modelLabel = 'pasar';

    protected static ?string $pluralModelLabel = 'pasar dan lembaga';

    protected static string|UnitEnum|null $navigationGroup = 'Infrastruktur';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return PasarForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PasarsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPasars::route('/'),
            'create' => CreatePasar::route('/create'),
            'edit' => EditPasar::route('/{record}/edit'),
        ];
    }
}
