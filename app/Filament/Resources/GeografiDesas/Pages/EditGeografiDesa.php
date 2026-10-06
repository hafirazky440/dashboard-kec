<?php

namespace App\Filament\Resources\GeografiDesas\Pages;

use App\Filament\Resources\GeografiDesas\GeografiDesaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGeografiDesa extends EditRecord
{
    protected static string $resource = GeografiDesaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
