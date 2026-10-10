<?php

namespace App\Filament\Resources\AktaKematians;

use App\Filament\Resources\AktaKematians\Pages\CreateAktaKematian;
use App\Filament\Resources\AktaKematians\Pages\EditAktaKematian;
use App\Filament\Resources\AktaKematians\Pages\ListAktaKematians;
use App\Filament\Resources\AktaKematians\Schemas\AktaKematianForm;
use App\Filament\Resources\AktaKematians\Tables\AktaKematiansTable;
use App\Filament\Resources\Concerns\HasGlobalSearchColumns;
use App\Models\AktaKematian;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Resource admin untuk data akta kematian per desa.
 */
class AktaKematianResource extends Resource
{
    use HasGlobalSearchColumns;

    /**
     * @var array<int, string>
     */
    protected static array $searchableColumns = ['desa'];

    protected static ?string $model = AktaKematian::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-heart';

    protected static ?string $navigationLabel = 'Akta Kematian';

    protected static ?string $modelLabel = 'akta kematian';

    protected static ?string $pluralModelLabel = 'akta kematian';

    protected static string|UnitEnum|null $navigationGroup = 'Administrasi Kependudukan';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return AktaKematianForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AktaKematiansTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAktaKematians::route('/'),
            'create' => CreateAktaKematian::route('/create'),
            'edit' => EditAktaKematian::route('/{record}/edit'),
        ];
    }
}
