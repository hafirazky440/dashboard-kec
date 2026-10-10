<?php

namespace App\Filament\Resources\DataPenduduks\Pages;

use App\Filament\Resources\DataPenduduks\DataPendudukResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDataPenduduk extends EditRecord
{
    protected static string $resource = DataPendudukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
