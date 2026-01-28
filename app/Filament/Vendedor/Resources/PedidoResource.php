<?php

namespace App\Filament\Vendedor\Resources;

use App\Filament\Vendedor\Resources\PedidoResource\Pages;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Cliente;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use BackedEnum;
use UnitEnum;

class PedidoResource extends Resource
{
    protected static ?string $model = Pedido::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Pedidos';

    protected static ?string $pluralModelLabel = 'Pedidos';

    protected static ?string $modelLabel = 'Pedido';
    
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('cliente_id')
                ->label('Cliente')
                ->options(Cliente::query()->orderBy('nombre')->pluck('nombre', 'id'))
                ->searchable()
                ->preload()
                ->createOptionForm([
                    TextInput::make('nombre')
                        ->label('Nombre')
                        ->required(),
                    TextInput::make('numero_pedido')
                        ->label('Número de Pedido')
                        ->required()
                        ->unique('clientes', 'numero_pedido'),
                    TextInput::make('telefono')
                        ->label('Teléfono')
                        ->tel(),
                    TextInput::make('direccion')
                        ->label('Dirección'),
                ])
                ->createOptionUsing(function (array $data): int {
                    return Cliente::create($data)->getKey();
                }),

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
                ->default(1)
                ->suffix('kg'),
            
            DatePicker::make('fecha')
                ->label('Fecha de Realización')
                ->required()
                ->default(now())
                ->native(false),
            
            DatePicker::make('fecha_carga')
                ->label('Fecha de Carga')
                ->default(now())
                ->native(false)
                ->disabled()
                ->dehydrated(),
                
            Select::make('estado')
                ->label('Estado')
                ->options([
                    'nuevo' => 'Nuevo',
                    'en_produccion' => 'En producción',
                    'finalizado' => 'Finalizado',
                    'listo' => 'Listo',
                    'entregado_al_cliente' => 'Entregado al cliente',
                    'cancelado' => 'Cancelado',
                ])
                ->default('nuevo')
                ->required(),

            Textarea::make('observaciones')
                ->label('Observaciones')
                ->rows(3)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Número de Pedido')
                    ->prefix('PED')
                    ->sortable()
                    ->searchable(),
                    
                TextColumn::make('cliente.nombre')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable()
                    ->default('Sin cliente'),
                    
                TextColumn::make('producto.nombre')
                    ->label('Producto')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('cantidad')
                    ->label('Cantidad')
                    ->suffix(' kg')
                    ->sortable(),
                    
                Tables\Columns\BadgeColumn::make('estado')
                    ->label('Estado')
                    ->colors([
                        'secondary' => 'nuevo',
                        'info' => 'listo',
                        'warning' => 'en_produccion',
                        'success' => fn ($state) => in_array($state, ['finalizado', 'entregado_al_cliente']),
                        'danger' => 'cancelado',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'nuevo' => 'Nuevo',
                        'en_produccion' => 'En producción',
                        'listo' => 'Listo',
                        'finalizado' => 'Finalizado',
                        'entregado_al_cliente' => 'Entregado al cliente',
                        'cancelado' => 'Cancelado',
                        default => ucfirst($state),
                    }),
                    
                TextColumn::make('fecha_carga')
                    ->label('Fecha de Carga')
                    ->date('Y-m-d')
                    ->sortable(),
                    
                TextColumn::make('fecha')
                    ->label('Fecha de Realización')
                    ->date('Y-m-d')
                    ->sortable(),
            ])
            ->defaultSort('fecha', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
