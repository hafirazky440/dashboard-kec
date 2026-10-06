<?php

namespace App\Filament\Resources\AktaKelahiranDesas;

use App\Filament\Resources\AktaKelahiranDesas\Pages\CreateAktaKelahiranDesa;
use App\Filament\Resources\AktaKelahiranDesas\Pages\EditAktaKelahiranDesa;
use App\Filament\Resources\AktaKelahiranDesas\Pages\ListAktaKelahiranDesas;
use App\Filament\Resources\AktaKelahiranDesas\Schemas\AktaKelahiranDesaForm;
use App\Filament\Resources\AktaKelahiranDesas\Tables\AktaKelahiranDesasTable;
use App\Filament\Resources\Concerns\HasGlobalSearchColumns;
use App\Models\AktaKelahiranDesa;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Resource admin untuk data akta kelahiran per desa per tahun.
 */
class AktaKelahiranDesaResource extends Resource
{
    use HasGlobalSearchColumns;

    /**
     * Kolom yang muncul di kotak pencarian global Cmd+K.
     *
     * @var array<int, string>
     */
    protected static array $searchableColumns = ['desa.nama'];

    protected static ?string $model = AktaKelahiranDesa::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static ?string $navigationLabel = 'Akta Kelahiran';

    protected static ?string $modelLabel = 'akta kelahiran desa';

    protected static ?string $pluralModelLabel = 'akta kelahiran desa';

    protected static string|UnitEnum|null $navigationGroup = 'Administrasi Kependudukan';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return AktaKelahiranDesaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AktaKelahiranDesasTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAktaKelahiranDesas::route('/'),
            'create' => CreateAktaKelahiranDesa::route('/create'),
            'edit' => EditAktaKelahiranDesa::route('/{record}/edit'),
        ];
    }
}
