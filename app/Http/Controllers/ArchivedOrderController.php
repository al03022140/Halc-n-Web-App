<?php

namespace App\Http\Controllers;

use App\Models\Order;

class ArchivedOrderController extends Controller
{
    public function index()
    {
        // Obtener solo las órdenes eliminadas (SoftDeletes)
        $orders = Order::onlyTrashed()->with(['status', 'user'])->get();

        return view('orders.archived', compact('orders'));
    }

    public function restore($id)
    {
        // Restaurar orden archivada
        $order = Order::onlyTrashed()->findOrFail($id);
        $order->is_deleted = false;
        $order->restore();

        return redirect()->route('orders.archived')->with('success', 'Orden restaurada correctamente.');
    }
}
