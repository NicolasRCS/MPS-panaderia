<?php

namespace App\Filament\Resources\Recetas\Pages;

use App\Filament\Resources\Recetas\RecetaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRecetas extends ListRecords
{
    protected static string $resource = RecetaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
