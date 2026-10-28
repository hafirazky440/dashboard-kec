<?php

namespace App\Filament\Resources\PegawaiKecamatans\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PegawaiDetailsRelationManager extends RelationManager
{
    private const ESELON_OPTIONS = [
        'Eselon I' => 'Eselon I',
        'Eselon II' => 'Eselon II',
        'Eselon III' => 'Eselon III',
        'Eselon IV' => 'Eselon IV',
        'Non-Eselon' => 'Non-Eselon',
    ];

    private const GOLONGAN_OPTIONS = [
        'I/a' => 'I/a',
        'I/b' => 'I/b',
        'I/c' => 'I/c',
        'I/d' => 'I/d',
        'II/a' => 'II/a',
        'II/b' => 'II/b',
        'II/c' => 'II/c',
        'II/d' => 'II/d',
        'III/a' => 'III/a',
        'III/b' => 'III/b',
        'III/c' => 'III/c',
        'III/d' => 'III/d',
        'IV/a' => 'IV/a',
        'IV/b' => 'IV/b',
        'IV/c' => 'IV/c',
        'IV/d' => 'IV/d',
        'IV/e' => 'IV/e',
        'Non-Golongan' => 'Non-Golongan',
    ];

    protected static string $relationship = 'details';

    protected static ?string $title = 'Rincian Pegawai';

    protected static ?string $modelLabel = 'rincian pegawai';

    protected static ?string $pluralModelLabel = 'rincian pegawai';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->label('Nama')
                    ->maxLength(255)
                    ->required(),

                TextInput::make('nip')
                    ->label('NIP')
                    ->required()
                    ->length(18)
                    ->regex('/^[0-9]{18}$/')
                    ->unique(table: 'pegawai_kecamatan_detail', column: 'nip', ignoreRecord: true)
                    ->inputMode('numeric')
                    ->placeholder('18 digit angka')
                    ->validationMessages([
                        'size' => 'NIP harus terdiri dari 18 angka.',
                        'regex' => 'NIP hanya boleh berisi angka.',
                        'unique' => 'NIP sudah digunakan.',
                    ]),

                TextInput::make('jabatan')
                    ->label('Jabatan')
                    ->maxLength(255)
                    ->required(),

                Select::make('eselon')
                    ->label('Eselon')
                    ->options(self::ESELON_OPTIONS)
                    ->required()
                    ->native(false),

                Select::make('golongan')
                    ->label('Golongan')
                    ->options(self::GOLONGAN_OPTIONS)
                    ->required()
                    ->native(false),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama')
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('nip')
                    ->label('NIP')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('jabatan')
                    ->label('Jabatan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('eselon')
                    ->label('Eselon')
                    ->sortable(),

                TextColumn::make('golongan')
                    ->label('Golongan')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Terakhir Diubah')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('nama')
            ->headerActions([
                CreateAction::make()->label('Tambah Rincian'),
            ])
            ->recordActions([
                EditAction::make()->label('Ubah'),
                DeleteAction::make()->label('Hapus'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Hapus yang dipilih'),
                ]),
            ]);
    }
}
