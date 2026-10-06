<?php

namespace App\Filament\Resources\Kesehatans\Pages;

use App\Filament\Resources\Kesehatans\KesehatanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKesehatans extends ListRecords
{
    protected static string $resource = KesehatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
