<?php

namespace App\Filament\Resources\Sungais\Pages;

use App\Filament\Resources\Sungais\SungaiResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSungai extends EditRecord
{
    protected static string $resource = SungaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
