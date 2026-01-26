<?php

namespace App\Filament\Admin\Resources\Ingredientes\Pages;

use App\Filament\Admin\Resources\Ingredientes\IngredienteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListIngredientes extends ListRecords
{
    protected static string $resource = IngredienteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
