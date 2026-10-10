<?php

namespace App\Filament\Resources\PegawaiKecamatans\Pages;

use App\Filament\Resources\PegawaiKecamatans\PegawaiKecamatanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPegawaiKecamatans extends ListRecords
{
    protected static string $resource = PegawaiKecamatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
