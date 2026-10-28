<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserRole;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Akun')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama')
                            ->maxLength(255)
                            ->required(),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255)
                            ->required()
                            ->unique(ignoreRecord: true),
                    ])
                    ->columns(2),

                Section::make('Akses')
                    ->schema([
                        Select::make('role')
                            ->label('Peran')
                            ->options(UserRole::options())
                            ->required(),

                        TextInput::make('password')
                            ->label('Kata Sandi')
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->saved(fn (?string $state): bool => filled($state))
                            ->helperText('Kosongkan saat mengubah pengguna jika kata sandi tidak diganti.')
                            ->confirmed(),

                        TextInput::make('password_confirmation')
                            ->label('Ulangi Kata Sandi')
                            ->password()
                            ->revealable()
                            ->dehydrated(false)
                            ->required(fn (string $operation): bool => $operation === 'create'),
                    ])
                    ->columns(2),
            ]);
    }
}
