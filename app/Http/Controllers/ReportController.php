<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Order;
use App\Models\OrderStatus;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function orders(Request $request)
    {
        $filters = $request->only(['status_id', 'client_id', 'date_from', 'date_to']);

        $orders = Order::with(['status', 'client'])
            ->when($request->filled('status_id'), fn ($query) => $query->where('status_id', $request->status_id))
            ->when($request->filled('client_id'), fn ($query) => $query->where('client_id', $request->client_id))
            ->when($request->filled('date_from'), fn ($query) => $query->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($query) => $query->whereDate('created_at', '<=', $request->date_to))
            ->where('is_deleted', false)
            ->latest()
            ->take(200)
            ->get();

        $totalsByStatus = $orders->groupBy(fn ($order) => optional($order->status)->name ?? 'Sin estado')
            ->map->count();

        $statuses = OrderStatus::all();
        $clients = Client::orderBy('name')->get();

        return view('reports.orders', compact('orders', 'totalsByStatus', 'statuses', 'clients', 'filters'));
    }
}
