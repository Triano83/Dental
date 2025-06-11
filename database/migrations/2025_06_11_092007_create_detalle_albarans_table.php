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
        Schema::create('detalle_albaranes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('albaran_id')->constrained('albaranes')->onDelete('cascade'); // Clave foránea al albarán
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade'); // Clave foránea al producto
            $table->string('nombre_producto'); // Copia del nombre del producto para mantenerlo inmutable si el original cambia
            $table->integer('unidades');
            $table->decimal('precio_unitario', 10, 2); // Copia del precio unitario al momento de la venta
            $table->decimal('importe', 10, 2); // Unidades x Precio Unitario
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_albaranes');
    }
};