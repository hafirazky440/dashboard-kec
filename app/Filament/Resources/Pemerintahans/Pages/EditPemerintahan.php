<?php

namespace App\Filament\Resources\Pemerintahans\Pages;

use App\Filament\Resources\Pemerintahans\PemerintahanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPemerintahan extends EditRecord
{
    protected static string $resource = PemerintahanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
