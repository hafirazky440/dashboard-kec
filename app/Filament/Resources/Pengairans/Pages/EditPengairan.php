<?php

namespace App\Filament\Resources\Pengairans\Pages;

use App\Filament\Resources\Pengairans\PengairanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPengairan extends EditRecord
{
    protected static string $resource = PengairanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
