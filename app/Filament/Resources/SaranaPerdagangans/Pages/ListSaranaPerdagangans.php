<?php

namespace App\Filament\Resources\SaranaPerdagangans\Pages;

use App\Filament\Resources\SaranaPerdagangans\SaranaPerdaganganResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSaranaPerdagangans extends ListRecords
{
    protected static string $resource = SaranaPerdaganganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
