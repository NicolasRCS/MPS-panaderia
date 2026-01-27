<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            // Agregar columna cliente_id (relación con la tabla clientes)
            $table->foreignId('cliente_id')->nullable()->after('id')->constrained('clientes')->nullOnDelete();
            
            // Agregar columnas adicionales para mayor detalle del pedido
            $table->date('fecha_carga')->nullable()->after('fecha'); // Fecha de carga del pedido
            $table->date('fecha_realizacion')->nullable()->after('fecha_carga'); // Fecha en que se realizó el pedido
            $table->text('observaciones')->nullable()->after('estado'); // Observaciones del pedido
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropForeign(['cliente_id']);
            $table->dropColumn(['cliente_id', 'fecha_carga', 'fecha_realizacion', 'observaciones']);
        });
    }
};
