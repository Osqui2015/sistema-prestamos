<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Loan;
use App\Models\Installment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Total Clientes
        $totalClients = Client::count();

        // 2. Préstamos Activos
        $activeLoans = Loan::where('status', 'activo')->count();

        // 3. Dinero Prestado (Total histórico)
        $totalLent = Loan::sum('amount');

        // 4. Dinero por Cobrar (Suma de cuotas pendientes)
        $pendingCollection = Installment::where('status', 'pendiente')->sum('amount');

        // 5. Cuotas Vencidas (Fecha menor a hoy y no pagadas)
        $overdueInstallments = Installment::where('status', 'pendiente')
            ->where('due_date', '<', now())
            ->count();

        return Inertia::render('Dashboard', [
            'stats' => [
                'clients' => $totalClients,
                'active_loans' => $activeLoans,
                'total_lent' => $totalLent,
                'pending' => $pendingCollection,
                'overdue' => $overdueInstallments
            ]
        ]);
    }
}
