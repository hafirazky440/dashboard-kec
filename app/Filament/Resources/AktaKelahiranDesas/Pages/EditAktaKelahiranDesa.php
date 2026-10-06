<?php

namespace App\Filament\Resources\AktaKelahiranDesas\Pages;

use App\Filament\Resources\AktaKelahiranDesas\AktaKelahiranDesaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAktaKelahiranDesa extends EditRecord
{
    protected static string $resource = AktaKelahiranDesaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
