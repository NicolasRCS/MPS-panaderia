<?php

namespace App\Filament\Vendedor\Resources\ProductoResource\Pages;

use App\Filament\Vendedor\Resources\ProductoResource;
use Filament\Resources\Pages\ListRecords;

class ListProductos extends ListRecords
{
    protected static string $resource = ProductoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Sin acciones de creación - solo lectura
        ];
    }
}
