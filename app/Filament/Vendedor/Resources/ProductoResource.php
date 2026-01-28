<?php

namespace App\Filament\Vendedor\Resources;

use App\Filament\Vendedor\Resources\ProductoResource\Pages;
use App\Models\Producto;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use BackedEnum;
use UnitEnum;

/**
 * Resource: Productos (Vista del Vendedor)
 * 
 * Este recurso muestra los productos que están registrados en el sistema.
 * Los productos son gestionados por el Admin, pero el vendedor necesita
 * verlos para poder crear pedidos.
 * 
 * IMPORTANTE: Este recurso es de SOLO LECTURA para el vendedor.
 * Los productos se crean y editan desde el panel de Admin.
 * 
 * PENDIENTE DE IMPLEMENTAR:
 * - Definir columnas de la tabla en table()
 * - Configurar filtros para búsqueda rápida
 * - Mostrar información relevante para el vendedor (nombre, unidad, stock disponible)
 * - Deshabilitar acciones de creación/edición/eliminación
 */
class ProductoResource extends Resource
{
    protected static ?string $model = Producto::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cube';
    
    protected static ?string $navigationLabel = 'Catálogo';
    
    protected static ?string $modelLabel = 'Producto';
    
    protected static ?string $pluralModelLabel = 'Productos';
    
    protected static string|UnitEnum|null $navigationGroup = null;
    
    protected static ?int $navigationSort = 3;

    /**
     * El formulario no se utilizará en este recurso
     * ya que los productos son de solo lectura para el vendedor
     */
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
                // Formulario vacío - solo lectura
            ]);
    }

    /**
     * Tabla para visualizar productos
     * 
     * TODO: Implementar:
     * - Columnas: nombre, unidad, stock_inicial, stock_minimo
     * - Filtros: por tipo, por disponibilidad
     * - Búsqueda: por nombre
     * - NO incluir acciones de edición/eliminación (solo lectura)
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // TODO: Agregar columnas de la tabla
            ])
            ->filters([
                // TODO: Agregar filtros
            ])
            ->actions([
                // Sin acciones - solo lectura
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // Sin acciones masivas - solo lectura
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Opcionalmente se puede agregar relación para ver recetas
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductos::route('/'),
            'view' => Pages\ViewProducto::route('/{record}'),
        ];
    }
    
    /**
     * Deshabilitar la creación de productos desde este panel
     */
    public static function canCreate(): bool
    {
        return false;
    }
}
