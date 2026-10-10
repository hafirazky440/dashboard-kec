<?php

namespace App\Filament\Resources\Gurus;

use App\Filament\Resources\Concerns\HasGlobalSearchColumns;
use App\Filament\Resources\Gurus\Pages\CreateGuru;
use App\Filament\Resources\Gurus\Pages\EditGuru;
use App\Filament\Resources\Gurus\Pages\ListGurus;
use App\Filament\Resources\Gurus\Schemas\GuruForm;
use App\Filament\Resources\Gurus\Tables\GurusTable;
use App\Models\Guru;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Resource admin untuk data guru.
 *
 * Sumber cetakan hanya membedakan guru sekolah negeri dan swasta, tanpa
 * rincian per jenjang.
 */
class GuruResource extends Resource
{
    use HasGlobalSearchColumns;

    /**
     * @var array<int, string>
     */
    protected static array $searchableColumns = ['jenis'];

    protected static ?string $model = Guru::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user';

    protected static ?string $navigationLabel = 'Guru';

    protected static ?string $modelLabel = 'guru';

    protected static ?string $pluralModelLabel = 'guru';

    protected static string|UnitEnum|null $navigationGroup = 'Pendidikan';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return GuruForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GurusTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGurus::route('/'),
            'create' => CreateGuru::route('/create'),
            'edit' => EditGuru::route('/{record}/edit'),
        ];
    }
}
