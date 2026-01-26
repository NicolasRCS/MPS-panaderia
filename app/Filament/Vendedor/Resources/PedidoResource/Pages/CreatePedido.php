<?php

namespace App\Filament\Vendedor\Resources\PedidoResource\Pages;

use App\Filament\Vendedor\Resources\PedidoResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePedido extends CreateRecord
{
    protected static string $resource = PedidoResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['estado'] = 'nuevo';
        return $data;
    }
}
