<?php

namespace App\Filament\Resources\Productos\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProductoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->required(),
                TextInput::make('unidad')
                    ->required()
                    ->default('kg'),
                TextInput::make('stock_inicial')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('stock_minimo')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('tamano_lote')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('capacidad_por_turno')
                    ->required()
                    ->numeric()
                    ->default(0.0),
            ]);
    }
}
