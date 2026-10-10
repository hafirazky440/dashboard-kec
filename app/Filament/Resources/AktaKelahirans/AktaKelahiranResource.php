<?php

namespace App\Filament\Resources\AktaKelahirans;

use App\Filament\Resources\AktaKelahirans\Pages\CreateAktaKelahiran;
use App\Filament\Resources\AktaKelahirans\Pages\EditAktaKelahiran;
use App\Filament\Resources\AktaKelahirans\Pages\ListAktaKelahirans;
use App\Filament\Resources\AktaKelahirans\Schemas\AktaKelahiranForm;
use App\Filament\Resources\AktaKelahirans\Tables\AktaKelahiransTable;
use App\Filament\Resources\Concerns\HasGlobalSearchColumns;
use App\Models\AktaKelahiran;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Resource admin untuk data akta kelahiran per desa.
 */
class AktaKelahiranResource extends Resource
{
    use HasGlobalSearchColumns;

    /**
     * @var array<int, string>
     */
    protected static array $searchableColumns = ['desa'];

    protected static ?string $model = AktaKelahiran::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Akta Kelahiran';

    protected static ?string $modelLabel = 'akta kelahiran';

    protected static ?string $pluralModelLabel = 'akta kelahiran';

    protected static string|UnitEnum|null $navigationGroup = 'Administrasi Kependudukan';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return AktaKelahiranForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AktaKelahiransTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAktaKelahirans::route('/'),
            'create' => CreateAktaKelahiran::route('/create'),
            'edit' => EditAktaKelahiran::route('/{record}/edit'),
        ];
    }
}
