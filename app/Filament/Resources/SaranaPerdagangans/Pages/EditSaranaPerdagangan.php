<?php

namespace App\Filament\Resources\SaranaPerdagangans\Pages;

use App\Filament\Resources\SaranaPerdagangans\SaranaPerdaganganResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSaranaPerdagangan extends EditRecord
{
    protected static string $resource = SaranaPerdaganganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
