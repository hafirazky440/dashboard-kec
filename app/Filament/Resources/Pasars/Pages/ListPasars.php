<?php

namespace App\Filament\Resources\Pasars\Pages;

use App\Filament\Resources\Pasars\PasarResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPasars extends ListRecords
{
    protected static string $resource = PasarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
