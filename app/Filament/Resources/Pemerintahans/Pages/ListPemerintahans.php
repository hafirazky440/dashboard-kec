<?php

namespace App\Filament\Resources\Pemerintahans\Pages;

use App\Filament\Resources\Pemerintahans\PemerintahanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPemerintahans extends ListRecords
{
    protected static string $resource = PemerintahanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
