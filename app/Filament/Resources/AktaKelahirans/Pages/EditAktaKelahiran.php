<?php

namespace App\Filament\Resources\AktaKelahirans\Pages;

use App\Filament\Resources\AktaKelahirans\AktaKelahiranResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAktaKelahiran extends EditRecord
{
    protected static string $resource = AktaKelahiranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
