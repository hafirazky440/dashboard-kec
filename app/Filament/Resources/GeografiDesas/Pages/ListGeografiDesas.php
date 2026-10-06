<?php

namespace App\Filament\Resources\GeografiDesas\Pages;

use App\Filament\Resources\GeografiDesas\GeografiDesaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGeografiDesas extends ListRecords
{
    protected static string $resource = GeografiDesaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
