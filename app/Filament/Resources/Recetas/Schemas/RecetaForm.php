<?php

namespace App\Filament\Resources\Recetas\Schemas;

use App\Models\Ingrediente;
use App\Models\Producto;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RecetaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('producto_id')
                ->label('Producto')
                ->options(Producto::query()->orderBy('nombre')->pluck('nombre', 'id'))
                ->searchable()
                ->required(),

            Select::make('ingrediente_id')
                ->label('Ingrediente')
                ->options(Ingrediente::query()->orderBy('nombre')->pluck('nombre', 'id'))
                ->searchable()
                ->required(),

            TextInput::make('cantidad')
                ->label('Cantidad (por 1 kg de producto)')
                ->numeric()
                ->required(),
        ]);
    }
}
