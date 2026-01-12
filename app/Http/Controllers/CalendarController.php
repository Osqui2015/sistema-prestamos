<?php

namespace App\Http\Controllers;

use App\Models\Installment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CalendarController extends Controller
{
    public function index()
    {
        // 1. Buscamos solo las cuotas que NO están pagadas
        $installments = Installment::with(['loan.client'])
            ->where('status', '!=', 'pagado')
            ->get();

        // 2. Transformamos los datos al formato que FullCalendar entiende
        $events = $installments->map(function ($installment) {
            return [
                'title' => '$' . intval($installment->amount) . ' - ' . $installment->loan->client->name,
                'start' => $installment->due_date,
                'url'   => route('loans.show', $installment->loan_id), // Al hacer clic, lleva al préstamo
                'color' => $installment->status === 'vencido' ? '#ef4444' : '#3b82f6', // Rojo si venció, Azul si no
                'allDay' => true
            ];
        });

        return Inertia::render('Calendar/Index', [
            'events' => $events
        ]);
    }
}
