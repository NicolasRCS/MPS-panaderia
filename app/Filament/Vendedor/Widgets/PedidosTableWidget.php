<?php

namespace App\Filament\Vendedor\Widgets;

use App\Models\Pedido;
use App\Filament\Vendedor\Resources\PedidoResource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class PedidosTableWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = 'full';
    
    protected string $view = 'filament.vendedor.widgets.pedidos-table-widget';
    
    public function getHeading(): ?string
    {
        return null;
    }
    
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Pedido::query()->with(['cliente', 'producto'])->latest('fecha')
            )
            ->heading(null)
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('N° Pedido')
                    ->prefix('PED')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary')
                    ->copyable()
                    ->copyMessage('ID copiado')
                    ->tooltip('Click para copiar')
                    ->url(fn ($record) => PedidoResource::getUrl('edit', ['record' => $record]))
                    ->openUrlInNewTab(false),
                
                Tables\Columns\TextColumn::make('cliente.nombre')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable()
                    ->default('Sin cliente')
                    ->icon('heroicon-o-user')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->cliente?->nombre),
                
                Tables\Columns\TextColumn::make('producto_info')
                    ->label('Cantidad de Productos')
                    ->formatStateUsing(function ($record) {
                        $cantidad = $record->cantidad ?? 0;
                        return "{$cantidad} productos";
                    })
                    ->icon('heroicon-o-cube')
                    ->searchable(['producto.nombre'])
                    ->sortable(['cantidad']),
                
                Tables\Columns\BadgeColumn::make('estado')
                    ->label('Estado')
                    ->colors([
                        'secondary' => 'nuevo',
                        'warning' => 'en_produccion',
                        'info' => 'listo',
                        'success' => fn ($state) => in_array($state, ['finalizado', 'entregado_al_cliente']),
                        'danger' => 'cancelado',
                    ])
                    ->icons([
                        'heroicon-o-sparkles' => 'nuevo',
                        'heroicon-o-cog-6-tooth' => 'en_produccion',
                        'heroicon-o-check-circle' => 'listo',
                        'heroicon-o-check-badge' => fn ($state) => in_array($state, ['finalizado', 'entregado_al_cliente']),
                        'heroicon-o-x-circle' => 'cancelado',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'nuevo' => 'Nuevo',
                        'en_produccion' => 'En Producción',
                        'listo' => 'Listo',
                        'finalizado' => 'Finalizado',
                        'entregado_al_cliente' => 'Entregado',
                        'cancelado' => 'Cancelado',
                        default => ucfirst($state),
                    }),
                
                Tables\Columns\TextColumn::make('fecha_carga')
                    ->label('Fecha Carga')
                    ->date('d/m/Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar')
                    ->color('gray')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('fecha')
                    ->label('Fecha Realización')
                    ->date('d/m/Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar-days')
                    ->color('info')
                    ->description(fn ($record) => \Carbon\Carbon::parse($record->fecha)->diffForHumans()),                
                Tables\Columns\TextColumn::make('acciones')
                    ->label('Acciones')
                    ->formatStateUsing(function ($record) {
                        $editUrl = PedidoResource::getUrl('edit', ['record' => $record]);
                        return view('filament.vendedor.components.pedido-actions', [
                            'editUrl' => $editUrl,
                            'record' => $record,
                        ])->render();
                    })
                    ->html(),            ])
            ->filters([
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'nuevo' => 'Nuevo',
                        'en_produccion' => 'En producción',
                        'listo' => 'Listo',
                        'finalizado' => 'Finalizado',
                        'entregado_al_cliente' => 'Entregado al cliente',
                        'cancelado' => 'Cancelado',
                    ])
                    ->placeholder('Todos los estados'),

                SelectFilter::make('rango_fecha')
                    ->label('Fechas')
                    ->options([
                        'hoy' => 'Hoy',
                        'semana' => 'Esta semana',
                        'mes' => 'Este mes',
                    ])
                    ->placeholder('Todas las fechas')
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                ($data['value'] ?? null) === 'hoy',
                                fn (Builder $query): Builder => $query->whereDate('fecha', now()->toDateString()),
                            )
                            ->when(
                                ($data['value'] ?? null) === 'semana',
                                fn (Builder $query): Builder => $query->whereBetween('fecha', [now()->startOfWeek(), now()->endOfWeek()]),
                            )
                            ->when(
                                ($data['value'] ?? null) === 'mes',
                                fn (Builder $query): Builder => $query->whereMonth('fecha', now()->month)->whereYear('fecha', now()->year),
                            );
                    }),
            ])
            ->defaultSort('fecha', 'desc')
            ->striped()
            ->paginated([10, 25, 50])
            ->poll('30s');
    }
}
