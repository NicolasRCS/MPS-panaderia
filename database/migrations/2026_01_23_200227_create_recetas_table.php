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
        Schema::create('recetas', function (Blueprint $table) {
    $table->id();
    $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
    $table->foreignId('ingrediente_id')->constrained('ingredientes')->cascadeOnDelete();
    $table->decimal('cantidad', 10, 4); 
    // cantidad de ingrediente por unidad base del producto
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recetas');
    }
};
