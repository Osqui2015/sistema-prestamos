<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 14px; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total { font-weight: bold; text-align: right; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Cronograma de Pagos</h2>
        <p>Cliente: {{ $loan->client->name }} | DNI: {{ $loan->client->dni }}</p>
        <p>Préstamo #{{ $loan->id }} | Fecha Inicio: {{ $loan->start_date }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Cuota #</th>
                <th>Vencimiento</th>
                <th>Monto</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($loan->installments as $installment)
            <tr>
                <td>{{ $installment->installment_number }}</td>
                <td>{{ $installment->due_date }}</td>
                <td>${{ number_format($installment->amount, 2) }}</td>
                <td>{{ strtoupper($installment->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        Total a Pagar: ${{ number_format($loan->total_payable, 2) }}
    </div>
</body>
</html>
