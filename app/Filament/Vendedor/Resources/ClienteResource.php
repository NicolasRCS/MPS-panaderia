<?php

namespace App\Filament\Vendedor\Resources;

use App\Filament\Vendedor\Resources\ClienteResource\Pages;
use App\Models\Cliente;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use BackedEnum;
use UnitEnum;

/**
 * Resource: Clientes
 * 
 * Gestiona los clientes del vendedor con las siguientes funcionalidades:
 * - Listar todos los clientes
 * - Crear nuevos clientes
 * - Editar clientes existentes
 * - Eliminar clientes
 * - Buscar clientes por nombre o número de pedido
 * - Filtrar clientes
 * 
 * PENDIENTE DE IMPLEMENTAR:
 * - Definir campos del formulario en form()
 * - Definir columnas de la tabla en table()
 * - Configurar filtros y acciones
 * - Agregar validaciones en los campos
 */
class ClienteResource extends Resource
{
    protected static ?string $model = Cliente::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';
    
    protected static ?string $navigationLabel = 'Clientes';
    
    protected static ?string $modelLabel = 'Cliente';
    
    protected static ?string $pluralModelLabel = 'Clientes';
    
    protected static string|UnitEnum|null $navigationGroup = null;
    
    protected static ?int $navigationSort = 2;

    /**
     * Formulario para crear/editar clientes
     * 
     * TODO: Implementar los campos:
     * - numero_pedido (generado automáticamente o manual)
     * - nombre (texto, requerido)
     * - observaciones (textarea, opcional)
     */
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
                // TODO: Agregar campos del formulario
                Forms\Components\Section::make('Información del Cliente')
                    ->schema([
                        // Campos pendientes de implementar
                    ]),
            ]);
    }

    /**
     * Tabla para listar clientes
     * 
     * TODO: Implementar:
     * - Columnas: numero_pedido, nombre, cantidad de pedidos, observaciones
     * - Filtros: por fecha de creación, por actividad
     * - Búsqueda: por nombre y número de pedido
     * - Acciones: editar, eliminar, ver pedidos del cliente
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // TODO: Agregar RelationManager para ver pedidos del cliente
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClientes::route('/'),
            'create' => Pages\CreateCliente::route('/create'),
            'edit' => Pages\EditCliente::route('/{record}/edit'),
        ];
    }
}
