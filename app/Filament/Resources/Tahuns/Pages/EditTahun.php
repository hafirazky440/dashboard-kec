<?php

namespace App\Filament\Resources\Tahuns\Pages;

use App\Filament\Resources\Tahuns\TahunResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTahun extends EditRecord
{
    protected static string $resource = TahunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
