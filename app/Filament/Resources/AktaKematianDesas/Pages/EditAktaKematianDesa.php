<?php

namespace App\Filament\Resources\AktaKematianDesas\Pages;

use App\Filament\Resources\AktaKematianDesas\AktaKematianDesaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAktaKematianDesa extends EditRecord
{
    protected static string $resource = AktaKematianDesaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
