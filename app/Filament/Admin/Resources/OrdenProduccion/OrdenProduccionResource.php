<?php

namespace App\Filament\Admin\Resources\OrdenProduccion;

use App\Filament\Admin\Resources\OrdenProduccion\Pages;
use App\Models\OrdenProduccion;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use BackedEnum;

class OrdenProduccionResource extends Resource
{
    protected static ?string $model = OrdenProduccion::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Órdenes de Producción';

    protected static ?string $pluralModelLabel = 'Órdenes de Producción';

    protected static ?string $modelLabel = 'Orden de Producción';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            //
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('fecha')->label('Fecha')->date('d/m/Y')->sortable(),
                TextColumn::make('created_at')->label('Creada')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->defaultSort('fecha', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrdenes::route('/'),
        ];
    }
}
