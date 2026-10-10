<?php

namespace App\Filament\Resources\AktaKematians\Pages;

use App\Filament\Resources\AktaKematians\AktaKematianResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAktaKematian extends EditRecord
{
    protected static string $resource = AktaKematianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
