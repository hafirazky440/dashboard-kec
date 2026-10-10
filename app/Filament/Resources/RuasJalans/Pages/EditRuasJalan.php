<?php

namespace App\Filament\Resources\RuasJalans\Pages;

use App\Filament\Resources\RuasJalans\RuasJalanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRuasJalan extends EditRecord
{
    protected static string $resource = RuasJalanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
