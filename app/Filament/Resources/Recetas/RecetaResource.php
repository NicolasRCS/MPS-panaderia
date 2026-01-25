<?php

namespace App\Filament\Resources\Recetas;

use App\Filament\Resources\Recetas\Pages\CreateReceta;
use App\Filament\Resources\Recetas\Pages\EditReceta;
use App\Filament\Resources\Recetas\Pages\ListRecetas;
use App\Filament\Resources\Recetas\Schemas\RecetaForm;
use App\Filament\Resources\Recetas\Tables\RecetasTable;
use App\Models\Receta;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RecetaResource extends Resource
{
    protected static ?string $model = Receta::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return RecetaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RecetasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRecetas::route('/'),
            'create' => CreateReceta::route('/create'),
            'edit' => EditReceta::route('/{record}/edit'),
        ];
    }
}
