<?php

namespace App\Filament\Resources\Sungais\Pages;

use App\Filament\Resources\Sungais\SungaiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSungais extends ListRecords
{
    protected static string $resource = SungaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
