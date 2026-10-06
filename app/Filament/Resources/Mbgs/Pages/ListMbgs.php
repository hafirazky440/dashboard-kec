<?php

namespace App\Filament\Resources\Mbgs\Pages;

use App\Filament\Resources\Mbgs\MbgResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMbgs extends ListRecords
{
    protected static string $resource = MbgResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
