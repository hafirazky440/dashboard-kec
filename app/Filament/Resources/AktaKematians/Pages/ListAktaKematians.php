<?php

namespace App\Filament\Resources\AktaKematians\Pages;

use App\Filament\Resources\AktaKematians\AktaKematianResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAktaKematians extends ListRecords
{
    protected static string $resource = AktaKematianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
