<?php

namespace App\Filament\Resources\Jalans\Pages;

use App\Filament\Resources\Jalans\JalanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListJalans extends ListRecords
{
    protected static string $resource = JalanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
