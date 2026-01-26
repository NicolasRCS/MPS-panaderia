<?php

namespace App\Filament\Admin\Resources\Recetas\Pages;

use App\Filament\Admin\Resources\Recetas\RecetaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditReceta extends EditRecord
{
    protected static string $resource = RecetaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
