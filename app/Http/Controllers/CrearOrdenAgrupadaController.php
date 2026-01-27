<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\OrdenProduccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class CrearOrdenAgrupadaController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|integer|exists:productos,id',
            'fecha' => 'required|date',
        ]);

        $pedidos = Pedido::where('producto_id', $request->producto_id)
            ->whereDate('fecha', $request->fecha)
            ->get();

        if ($pedidos->isEmpty()) {
            return Redirect::back()->with('danger', 'No hay pedidos para agrupar.');
        }

        $orden = OrdenProduccion::crearAgrupada($request->producto_id, $request->fecha, $pedidos);

        return Redirect::back()->with('success', 'Orden de producción agrupada creada (#' . $orden->id . ').');
    }
}
