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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nombre completo
            $table->string('dni')->unique(); // DNI único para no repetir clientes
            $table->string('phone')->nullable(); // Teléfono (opcional)
            $table->string('address')->nullable(); // Dirección
            $table->timestamps(); // Guarda fecha de creación automáticamente
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
