<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PedidoResource\Pages;
use App\Models\Pedido;
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
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class PedidoResource extends Resource
{
    protected static ?string $model = Pedido::class;

    protected static ?string $navigationLabel = 'Pedidos';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\DatePicker::make('fecha')->required(),
            Forms\Components\Select::make('producto_id')
                ->label('Producto')
                ->options(Producto::query()->orderBy('nombre')->pluck('nombre', 'id'))
                ->searchable()
                ->required(),
            Forms\Components\TextInput::make('cantidad')->numeric()->required(),
            Forms\Components\Select::make('estado')
                ->options([
                    'nuevo' => 'Nuevo',
                    'en_produccion' => 'En producción',
                    'finalizado' => 'Finalizado',
                    'cancelado' => 'Cancelado',
                ])->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')->label('#')->sortable(),
            TextColumn::make('fecha')->date()->sortable(),
            TextColumn::make('producto.nombre')->label('Producto')->sortable()->wrap(),
            TextColumn::make('cantidad')->label('Cantidad')->alignRight(),
            BadgeColumn::make('estado')->colors([
                'primary' => 'nuevo',
                'warning' => 'en_produccion',
                'success' => 'finalizado',
                'danger' => 'cancelado',
            ])->label('Estado'),
        ])->filters([])->actions([
            Action::make('convertir')
                ->label('Convertir a Orden')
                ->icon('heroicon-o-arrow-right')
                ->action(function (Pedido $record, array $data = []) {
                    // crear orden de produccion
                    \App\Models\OrdenProduccion::create([
                        'pedido_id' => $record->id,
                        'producto_id' => $record->producto_id,
                        'cantidad' => $record->cantidad,
                        'estado' => 'pendiente',
                    ]);

                    $record->update(['estado' => 'en_produccion']);

                    Notification::make()
                        ->title('Orden creada')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->hidden(fn (?Pedido $record) => $record->estado !== 'nuevo'),
        ])->bulkActions([
            BulkAction::make('convertir_multiples')
                ->label('Convertir seleccionados')
                ->action(function (Builder $query) {
                    $query->each(function (Pedido $p) {
                        \App\Models\OrdenProduccion::create([
                            'pedido_id' => $p->id,
                            'producto_id' => $p->producto_id,
                            'cantidad' => $p->cantidad,
                            'estado' => 'pendiente',
                        ]);
                        $p->update(['estado' => 'en_produccion']);
                    });
                })
                ->requiresConfirmation(),
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
