<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Installment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class LoanController extends Controller
{
    // Función para Guardar el Préstamo (Con la corrección de fecha)
    public function store(Request $request)
    {
        // 1. Validar datos que vienen del formulario
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'amount' => 'required|numeric|min:1',
            'interest' => 'required|numeric|min:0',
            'installments' => 'required|integer|min:1',
            'frequency' => 'required|in:semanal,mensual',
            'start_date' => 'required|date',
        ]);

        // Usamos una Transacción para asegurar que se guarde todo o nada
        DB::transaction(function () use ($request) {

            // 2. Calcular los montos
            // Fórmula: Capital * (1 + (Interés / 100))
            $totalAmount = $request->amount * (1 + ($request->interest / 100));

            // Monto exacto de cada cuota
            $installmentAmount = $totalAmount / $request->installments;

            // --- CORRECCIÓN IMPORTANTE ---
            // Limpiamos la fecha para que MySQL no se queje por la hora (T02:15:12...)
            $cleanDate = Carbon::parse($request->start_date)->format('Y-m-d');

            // 3. Crear el Préstamo en la base de datos
            $loan = Loan::create([
                'client_id' => $request->client_id,
                'amount' => $request->amount,
                'interest_rate' => $request->interest,
                'installments_count' => $request->installments,
                'frequency' => $request->frequency,
                'total_payable' => $totalAmount,
                'start_date' => $cleanDate, // Usamos la fecha limpia
                'status' => 'activo'
            ]);

            // 4. Generar las Cuotas automáticamente
            $date = Carbon::parse($cleanDate);

            for ($i = 1; $i <= $request->installments; $i++) {
                // Sumar semanas o meses según corresponda
                if ($request->frequency === 'semanal') {
                    $date->addWeek();
                } else {
                    $date->addMonth();
                }

                Installment::create([
                    'loan_id' => $loan->id,
                    'installment_number' => $i,
                    'due_date' => $date->copy(), // Usamos copy() para no afectar la vuelta del bucle
                    'amount' => $installmentAmount,
                    'status' => 'pendiente'
                ]);
            }
        });

        // Volver a la página anterior (el perfil del cliente)
        return redirect()->back();
    }

    // Función para ver el detalle de un préstamo (Tabla de cobros)
    public function show($id)
    {
        // Buscamos el préstamo, incluyendo los datos del cliente y las cuotas
        $loan = Loan::with(['client', 'installments'])->findOrFail($id);

        return Inertia::render('Loans/Show', [
            'loan' => $loan
        ]);
    }

    // Función para registrar que una cuota fue pagada
    public function registerPayment(Request $request, $installment_id)
    {
        $installment = Installment::findOrFail($installment_id);

        $installment->update([
            'amount_paid' => $request->amount,
            'payment_date' => now(), // Fecha y hora actual
            'status' => 'pagado'
        ]);

        return redirect()->back();
    }

    public function downloadPdf($id)
    {
        $loan = Loan::with(['client', 'installments'])->findOrFail($id);

        // Cargamos la vista que creamos recién y le pasamos los datos
        $pdf = Pdf::loadView('pdf.loan_schedule', compact('loan'));

        // Descargamos el archivo
        return $pdf->download('prestamo_' . $loan->id . '_' . $loan->client->name . '.pdf');
    }
}
