<?php

namespace App\Filament\Resources\AktaKematianDesas;

use App\Filament\Resources\AktaKematianDesas\Pages\CreateAktaKematianDesa;
use App\Filament\Resources\AktaKematianDesas\Pages\EditAktaKematianDesa;
use App\Filament\Resources\AktaKematianDesas\Pages\ListAktaKematianDesas;
use App\Filament\Resources\AktaKematianDesas\Schemas\AktaKematianDesaForm;
use App\Filament\Resources\AktaKematianDesas\Tables\AktaKematianDesasTable;
use App\Filament\Resources\Concerns\HasGlobalSearchColumns;
use App\Models\AktaKematianDesa;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Resource admin untuk jumlah akta kematian per desa per tahun.
 */
class AktaKematianDesaResource extends Resource
{
    use HasGlobalSearchColumns;

    /**
     * Kolom yang muncul di kotak pencarian global Cmd+K.
     *
     * @var array<int, string>
     */
    protected static array $searchableColumns = ['desa.nama'];

    protected static ?string $model = AktaKematianDesa::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static ?string $navigationLabel = 'Akta Kematian';

    protected static ?string $modelLabel = 'akta kematian desa';

    protected static ?string $pluralModelLabel = 'akta kematian desa';

    protected static string|UnitEnum|null $navigationGroup = 'Administrasi Kependudukan';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return AktaKematianDesaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AktaKematianDesasTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAktaKematianDesas::route('/'),
            'create' => CreateAktaKematianDesa::route('/create'),
            'edit' => EditAktaKematianDesa::route('/{record}/edit'),
        ];
    }
}
