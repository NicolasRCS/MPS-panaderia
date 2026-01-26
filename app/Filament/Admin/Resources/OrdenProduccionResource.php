<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\OrdenProduccionResource\Pages;
use App\Models\OrdenProduccion;
use App\Models\Producto;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Support\Icons\Heroicon;

class OrdenProduccionResource extends Resource
{
    protected static ?string $model = OrdenProduccion::class;

    protected static ?string $navigationLabel = 'Órdenes Producción';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Select::make('pedido_id')
                ->label('Pedido')
                ->options(\App\Models\Pedido::query()->pluck('id', 'id'))
                ->searchable(),
            Forms\Components\Select::make('producto_id')
                ->label('Producto')
                ->options(Producto::query()->orderBy('nombre')->pluck('nombre', 'id'))
                ->required(),
            Forms\Components\TextInput::make('cantidad')->numeric()->required(),
            Forms\Components\Select::make('estado')
                ->options([
                    'pendiente' => 'Pendiente',
                    'en_cola' => 'En cola',
                    'produciendo' => 'Produciendo',
                    'completada' => 'Completada',
                ])->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')->label('#')->sortable(),
            TextColumn::make('pedido_id')->label('Pedido')->sortable(),
            TextColumn::make('producto.nombre')->label('Producto')->sortable()->wrap(),
            TextColumn::make('cantidad')->label('Cantidad')->alignRight(),
            BadgeColumn::make('estado')->colors([
                'primary' => 'pendiente',
                'warning' => 'en_cola',
                'success' => 'completada',
            ])->label('Estado'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrdenes::route('/'),
            'create' => Pages\CreateOrden::route('/create'),
            'edit' => Pages\EditOrden::route('/{record}/edit'),
        ];
    }
}
