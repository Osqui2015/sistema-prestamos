<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientController extends Controller
{
    public function index()
    {
        // Traemos clientes CON su préstamo activo (si tienen) y contamos las cuotas pendientes
        $clients = Client::with(['loans' => function ($query) {
            $query->where('status', 'activo') // Solo nos interesa el préstamo activo
                ->withCount(['installments as pending_installments' => function ($q) {
                    $q->where('status', '!=', 'pagado'); // Contamos las que faltan pagar
                }]);
        }])->orderBy('created_at', 'desc')->get();

        return Inertia::render('Clients/Index', [
            'clients' => $clients
        ]);
    }

    // NUEVO: Función para guardar el cliente
    public function store(Request $request)
    {
        // 1. Validamos que los datos sean correctos
        $request->validate([
            'name' => 'required|string|max:255',
            'dni' => 'required|string|unique:clients,dni', // DNI único
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        // 2. Creamos el cliente en la Base de Datos
        Client::create($request->all());

        // 3. Redirigimos (Inertia actualizará la tabla automáticamente)
        return redirect()->route('clients.index');
    }

    public function show($id)
    {
        // Buscamos al cliente por su ID y cargamos sus préstamos también
        $client = Client::with('loans')->findOrFail($id);

        return Inertia::render('Clients/Show', [
            'client' => $client
        ]);
    }

    // Actualizar datos de un cliente
    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            // El DNI debe ser único, pero ignorando al cliente actual (para que no de error si no cambia el DNI)
            'dni' => 'required|string|unique:clients,dni,' . $client->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $client->update($request->all());

        return redirect()->back();
    }

    // Eliminar cliente
    public function destroy($id)
    {
        $client = Client::findOrFail($id);

        // Opcional: Podrías validar si tiene préstamos activos antes de borrar
        $client->delete();

        return redirect()->route('clients.index');
    }

}
