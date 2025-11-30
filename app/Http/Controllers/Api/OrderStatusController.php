<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderStatusController extends Controller
{
    /**
     * Devuelve el estado de una orden validando la pertenencia al cliente.
     */
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'invoice_number' => ['required', 'string', 'max:100'],
            'customer_custom_id' => ['required', 'string', 'max:100'],
        ]);

        $order = Order::with(['status', 'client'])
            ->where('invoice_number', $data['invoice_number'])
            ->where(function ($query) use ($data) {
                $query->where('customer_custom_id', $data['customer_custom_id'])
                    ->orWhereHas('client', function ($q) use ($data) {
                        $q->where('custom_id', $data['customer_custom_id']);
                    });
            })
            ->first();

        if (! $order) {
            return response()->json([
                'message' => 'No se encontró ningún pedido con los datos proporcionados.',
            ], 404);
        }

        $orderDate = $order->order_date
            ? Carbon::parse($order->order_date)
            : $order->created_at;

        $statusName = optional($order->status)->name ?? 'Sin estado';

        $steps = [
            ['key' => 'Ordered', 'label' => 'Ordenado', 'icon' => 'fa-clipboard-list'],
            ['key' => 'In process', 'label' => 'En proceso', 'icon' => 'fa-cogs'],
            ['key' => 'In route', 'label' => 'En ruta', 'icon' => 'fa-truck'],
            ['key' => 'Delivered', 'label' => 'Entregado', 'icon' => 'fa-check-circle'],
        ];

        $activeIndex = collect($steps)->search(function ($step) use ($statusName) {
            return strcasecmp($step['key'], $statusName) === 0;
        });

        $customerCustomId = $order->customer_custom_id ?? optional($order->client)->custom_id;

        $payload = [
            'invoice_number' => $order->invoice_number,
            'customer_name' => $order->customer_name,
            'customer_number' => $order->customer_number,
            'customer_custom_id' => $customerCustomId,
            'status' => [
                'name' => $statusName,
                'color' => optional($order->status)->color,
            ],
            'order_date' => $orderDate->toIso8601String(),
            'order_date_label' => $orderDate->format('d/m/Y'),
            'updated_at' => $order->updated_at->toIso8601String(),
            'updated_at_label' => $order->updated_at->format('d/m/Y H:i'),
            'steps' => $steps,
            'active_step_index' => $activeIndex === false ? 0 : $activeIndex,
            'evidence_url' => $order->end_image ? asset('storage/' . $order->end_image) : null,
        ];

        return response()->json([
            'data' => $payload,
        ]);
    }
}
