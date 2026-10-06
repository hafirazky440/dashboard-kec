<?php

namespace App\Filament\Resources\Kesehatans\Pages;

use App\Filament\Resources\Kesehatans\KesehatanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditKesehatan extends EditRecord
{
    protected static string $resource = KesehatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
