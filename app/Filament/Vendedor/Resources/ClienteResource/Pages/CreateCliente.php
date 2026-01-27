<?php

namespace App\Filament\Vendedor\Resources\ClienteResource\Pages;

use App\Filament\Vendedor\Resources\ClienteResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCliente extends CreateRecord
{
    protected static string $resource = ClienteResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
