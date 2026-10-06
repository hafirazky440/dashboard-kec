<?php

namespace App\Filament\Resources\Pasars\Pages;

use App\Filament\Resources\Pasars\PasarResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPasar extends EditRecord
{
    protected static string $resource = PasarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
