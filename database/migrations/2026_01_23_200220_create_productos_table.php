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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('unidad')->default('kg'); // kg o unidad
            $table->decimal('stock_inicial', 10, 2)->default(0);
            $table->decimal('stock_minimo', 10, 2)->default(0);
            $table->decimal('tamano_lote', 10, 2)->default(0); // tamaño de lote
            $table->decimal('capacidad_por_turno', 10, 2)->default(0); // kg por turno
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
