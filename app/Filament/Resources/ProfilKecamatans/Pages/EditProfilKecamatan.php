<?php

namespace App\Filament\Resources\ProfilKecamatans\Pages;

use App\Filament\Resources\ProfilKecamatans\ProfilKecamatanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProfilKecamatan extends EditRecord
{
    protected static string $resource = ProfilKecamatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
