<?php

namespace App\Http\Controllers;

use App\Models\Installment;
use App\Models\Loan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // 1. Definir fechas (Por defecto: Este mes)
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now()->endOfMonth();

        // 2. Calcular Dinero Prestado en este periodo (Salidas)
        $moneyLent = Loan::whereBetween('created_at', [$startDate, $endDate])->sum('amount');

        // 3. Calcular Dinero Cobrado en este periodo (Entradas)
        $moneyCollected = Installment::where('status', 'pagado')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->sum('amount_paid');

        // 4. Calcular Ganancia Estimada (Interés)
        // Lógica: Buscamos las cuotas pagadas y calculamos cuánto de eso fue ganancia según el interés del préstamo
        $installments = Installment::with('loan')
            ->where('status', 'pagado')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->get();

        $estimatedProfit = 0;
        foreach ($installments as $installment) {
            // Fórmula: Cuota / (1 + Interés/100) = Capital Puro. El resto es Ganancia.
            // Ejemplo: Cuota $110, Interés 10%. Capital = 110/1.10 = 100. Ganancia = 10.
            $interestRate = $installment->loan->interest_rate;
            $pureCapital = $installment->amount_paid / (1 + ($interestRate / 100));
            $profit = $installment->amount_paid - $pureCapital;
            $estimatedProfit += $profit;
        }

        // 5. Preparar datos para el Gráfico (Cobros por día)
        $chartData = Installment::selectRaw('DATE(payment_date) as date, SUM(amount_paid) as total')
            ->where('status', 'pagado')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return Inertia::render('Reports/Index', [
            'filters' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            'stats' => [
                'lent' => $moneyLent,
                'collected' => $moneyCollected,
                'profit' => round($estimatedProfit, 2),
                'balance' => $moneyCollected - $moneyLent // Flujo de caja neto
            ],
            'chart' => [
                'labels' => $chartData->pluck('date'),
                'data' => $chartData->pluck('total')
            ]
        ]);
    }
}
