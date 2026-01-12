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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade'); // Vincula con el cliente

            $table->decimal('amount', 10, 2); // Monto prestado (ej: 10000.00)
            $table->decimal('interest_rate', 5, 2); // Porcentaje de interés (ej: 10.50)
            $table->integer('installments_count'); // Cantidad de cuotas (ej: 12)
            $table->enum('frequency', ['semanal', 'mensual']); // Frecuencia de pago
            $table->decimal('total_payable', 10, 2); // Total final a pagar (Capital + Interés)

            $table->date('start_date'); // Fecha de inicio
            $table->enum('status', ['pendiente', 'activo', 'pagado', 'vencido'])->default('activo');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
