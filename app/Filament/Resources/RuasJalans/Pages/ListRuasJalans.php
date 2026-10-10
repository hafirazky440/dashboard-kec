<?php

namespace App\Filament\Resources\RuasJalans\Pages;

use App\Filament\Resources\RuasJalans\RuasJalanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRuasJalans extends ListRecords
{
    protected static string $resource = RuasJalanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
