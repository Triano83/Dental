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
        Schema::create('albaranes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade'); // Clave foránea a la tabla clientes
            $table->string('codigo_albaran', 20)->unique(); // Formato AAAAMMDD + ID
            $table->date('fecha_envio');
            $table->string('nombre_paciente');
            $table->decimal('descuento', 10, 2)->default(0.00); // Descuento, por defecto 0
            $table->decimal('total_productos', 10, 2); // Suma de productos antes del descuento
            $table->decimal('total_albaran', 10, 2);   // Total final del albarán

            // La clave foránea a facturas puede ser nula inicialmente
            // Se añadirá una vez el albarán sea incluido en una factura.
            $table->foreignId('factura_id')->nullable()->constrained('facturas')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('albaranes');
    }
};