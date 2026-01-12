<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = ['name', 'dni', 'phone', 'address'];

    // AGREGAR ESTO: Un cliente tiene muchos préstamos
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}
