<?php

namespace App\Filament\Resources\AktaKelahirans\Pages;

use App\Filament\Resources\AktaKelahirans\AktaKelahiranResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAktaKelahirans extends ListRecords
{
    protected static string $resource = AktaKelahiranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
