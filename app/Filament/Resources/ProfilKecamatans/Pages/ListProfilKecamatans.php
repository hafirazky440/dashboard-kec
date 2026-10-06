<?php

namespace App\Filament\Resources\ProfilKecamatans\Pages;

use App\Filament\Resources\ProfilKecamatans\ProfilKecamatanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProfilKecamatans extends ListRecords
{
    protected static string $resource = ProfilKecamatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
