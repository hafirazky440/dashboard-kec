<?php

namespace App\Filament\Resources\Sungais;

use App\Filament\Resources\Concerns\HasGlobalSearchColumns;
use App\Filament\Resources\Sungais\Pages\CreateSungai;
use App\Filament\Resources\Sungais\Pages\EditSungai;
use App\Filament\Resources\Sungais\Pages\ListSungais;
use App\Filament\Resources\Sungais\Schemas\SungaiForm;
use App\Filament\Resources\Sungais\Tables\SungaisTable;
use App\Models\Sungai;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Resource admin untuk daftar sungai desa per tahun.
 */
class SungaiResource extends Resource
{
    use HasGlobalSearchColumns;

    /**
     * Kolom yang muncul di kotak pencarian global Cmd+K.
     *
     * @var array<int, string>
     */
    protected static array $searchableColumns = ['nama', 'status'];

    protected static ?string $model = Sungai::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    protected static ?string $navigationLabel = 'Sungai';

    protected static ?string $modelLabel = 'sungai';

    protected static ?string $pluralModelLabel = 'sungai';

    protected static string|UnitEnum|null $navigationGroup = 'Infrastruktur';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return SungaiForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SungaisTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSungais::route('/'),
            'create' => CreateSungai::route('/create'),
            'edit' => EditSungai::route('/{record}/edit'),
        ];
    }
}
