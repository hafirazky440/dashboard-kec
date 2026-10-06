<?php

namespace App\Filament\Resources\Mbgs\Pages;

use App\Filament\Resources\Mbgs\MbgResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMbg extends EditRecord
{
    protected static string $resource = MbgResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
