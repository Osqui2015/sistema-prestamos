<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'client_id',
        'amount',
        'interest_rate',
        'installments_count',
        'frequency',
        'total_payable',
        'start_date',
        'status'
    ];

    // --- ESTO ES LO QUE FALTABA ---
    // Relación: Un préstamo "pertenece a" un Cliente
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Relación: Un préstamo "tiene muchas" Cuotas
    public function installments()
    {
        return $this->hasMany(Installment::class);
    }
}
