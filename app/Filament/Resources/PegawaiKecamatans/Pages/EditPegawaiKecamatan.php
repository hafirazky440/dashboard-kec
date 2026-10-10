<?php

namespace App\Filament\Resources\PegawaiKecamatans\Pages;

use App\Filament\Resources\PegawaiKecamatans\PegawaiKecamatanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPegawaiKecamatan extends EditRecord
{
    protected static string $resource = PegawaiKecamatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
