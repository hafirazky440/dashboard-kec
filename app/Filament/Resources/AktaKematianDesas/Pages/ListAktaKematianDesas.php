<?php

namespace App\Filament\Resources\AktaKematianDesas\Pages;

use App\Filament\Resources\AktaKematianDesas\AktaKematianDesaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAktaKematianDesas extends ListRecords
{
    protected static string $resource = AktaKematianDesaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
