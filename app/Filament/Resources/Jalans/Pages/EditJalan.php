<?php

namespace App\Filament\Resources\Jalans\Pages;

use App\Filament\Resources\Jalans\JalanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditJalan extends EditRecord
{
    protected static string $resource = JalanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
