<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class HomeController extends Controller
{
    // Página pública de búsqueda
    public function index()
    {
        // Siempre mostrar la página pública; el contenido adapta CTA según sesión
        return view('public.home');
    }

    // Procesar búsqueda pública (requiere coincidencia exacta)
    public function search(Request $request)
    {
        $data = $request->validate([
            'customer_number' => 'required|string|max:100',
            'invoice_number' => 'required|string|max:100',
        ]);

        $order = Order::where('customer_number', $data['customer_number'])
            ->where('invoice_number', $data['invoice_number'])
            ->first();

        if (!$order) {
            return back()->withInput()->with('error', 'No se encontró ningún pedido con los datos proporcionados.');
        }

        return view('public.order-status', compact('order'));
    }

    // Dashboard para usuarios autenticados
    public function dashboard()
    {
        return view('dashboard');
    }
}
