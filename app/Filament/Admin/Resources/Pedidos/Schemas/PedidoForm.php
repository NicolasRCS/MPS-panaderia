<?php

namespace App\Filament\Admin\Resources\Pedidos\Schemas;

use App\Models\Producto;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PedidoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            DatePicker::make('fecha')
                ->label('Fecha')
                ->required(),

            Select::make('producto_id')
                ->label('Producto')
                ->options(Producto::query()->orderBy('nombre')->pluck('nombre', 'id'))
                ->searchable()
                ->required(),

            TextInput::make('cantidad')
                ->label('Cantidad')
                ->numeric()
                ->required(),
        ]);
    }
}
