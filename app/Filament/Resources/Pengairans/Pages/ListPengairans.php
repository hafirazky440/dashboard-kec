<?php

namespace App\Filament\Resources\Pengairans\Pages;

use App\Filament\Resources\Pengairans\PengairanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPengairans extends ListRecords
{
    protected static string $resource = PengairanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
