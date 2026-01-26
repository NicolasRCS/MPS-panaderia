<?php

namespace App\Filament\Admin\Resources\Ingredientes\Pages;

use App\Filament\Admin\Resources\Ingredientes\IngredienteResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditIngrediente extends EditRecord
{
    protected static string $resource = IngredienteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
