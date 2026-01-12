<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{
    protected $fillable = [
        'loan_id',
        'installment_number',
        'due_date',
        'amount',
        'amount_paid',
        'payment_date',
        'status'
    ];

    // --- ESTO ES LO QUE FALTABA ---
    // Relación: Una Cuota "pertenece a" un Préstamo
    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
