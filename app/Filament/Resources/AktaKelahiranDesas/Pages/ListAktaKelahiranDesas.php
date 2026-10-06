<?php

namespace App\Filament\Resources\AktaKelahiranDesas\Pages;

use App\Filament\Resources\AktaKelahiranDesas\AktaKelahiranDesaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAktaKelahiranDesas extends ListRecords
{
    protected static string $resource = AktaKelahiranDesaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
