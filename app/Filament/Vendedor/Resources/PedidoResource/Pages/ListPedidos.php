<?php

namespace App\Filament\Vendedor\Resources\PedidoResource\Pages;

use App\Filament\Vendedor\Resources\PedidoResource;
use Filament\Resources\Pages\ListRecords;

class ListPedidos extends ListRecords
{
    protected static string $resource = PedidoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
