<?php

namespace App\Filament\Resources\Ingredientes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class IngredienteForm
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
            ]);
    }
}
