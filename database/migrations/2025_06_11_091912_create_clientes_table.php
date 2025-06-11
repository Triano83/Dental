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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_clinica');
            $table->string('direccion');
            $table->string('codigo_postal', 10); // Longitud más específica
            $table->string('poblacion', 100);    // Longitud más específica
            $table->string('ciudad', 100);      // Longitud más específica
            $table->string('telefono', 20);     // Longitud más específica
            $table->string('nif', 20)->unique(); // Campo NIF, debe ser único
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};