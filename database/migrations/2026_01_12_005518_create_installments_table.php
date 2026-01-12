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
        Schema::create('installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->onDelete('cascade'); // Vincula con el préstamo

            $table->integer('installment_number'); // Número de cuota (1, 2, 3...)
            $table->date('due_date'); // Fecha de vencimiento (para el calendario)
            $table->decimal('amount', 10, 2); // Monto que DEBE pagar

            // Control de pagos
            $table->decimal('amount_paid', 10, 2)->default(0); // Cuánto pagó realmente
            $table->date('payment_date')->nullable(); // Fecha real en que pagó

            $table->enum('status', ['pendiente', 'pagado', 'parcial'])->default('pendiente');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installments');
    }
};
