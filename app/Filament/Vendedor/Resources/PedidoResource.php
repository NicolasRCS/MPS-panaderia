<?php

namespace App\Filament\Vendedor\Resources;

use App\Filament\Vendedor\Resources\PedidoResource\Pages;
use App\Models\Pedido;
use App\Models\Producto;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use BackedEnum;

class PedidoResource extends Resource
{
    protected static ?string $model = Pedido::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Pedidos';

    protected static ?string $pluralModelLabel = 'Pedidos';

    protected static ?string $modelLabel = 'Pedido';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            DatePicker::make('fecha')
                ->label('Fecha de Entrega')
                ->required()
                ->default(now()),

            Select::make('producto_id')
                ->label('Producto')
                ->options(Producto::query()->orderBy('nombre')->pluck('nombre', 'id'))
                ->searchable()
                ->required()
                ->preload(),

            TextInput::make('cantidad')
                ->label('Cantidad')
                ->numeric()
                ->required()
                ->minValue(1)
                ->default(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('producto.nombre')
                    ->label('Producto')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('cantidad')
                    ->label('Cantidad')
                    ->sortable(),
                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'nuevo' => 'gray',
                        'pendiente' => 'warning',
                        'en_produccion' => 'info',
                        'completado' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'nuevo' => 'Nuevo',
                        'pendiente' => 'Pendiente',
                        'en_produccion' => 'En Producción',
                        'completado' => 'Completado',
                        default => $state,
                    }),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('fecha', 'desc')
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPedidos::route('/'),
            'create' => Pages\CreatePedido::route('/create'),
            'edit' => Pages\EditPedido::route('/{record}/edit'),
        ];
    }
}
