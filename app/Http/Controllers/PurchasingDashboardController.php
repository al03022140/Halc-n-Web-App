<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\PurchaseRequest;
use App\Models\PurchaseUpdate;
use App\Services\InventoryAlertService;
use Illuminate\Http\Request;

class PurchasingDashboardController extends Controller
{
    public function __construct(private InventoryAlertService $inventoryAlertService)
    {
        $this->middleware('role:Admin,Purchasing');
    }

    public function index(Request $request)
    {
        $statusFilters = ['Ordered', 'In process'];
        $showAll = $request->has('show_all') ? $request->boolean('show_all') : true;

        $baseQuery = Product::query()->with(['orders' => function ($query) use ($statusFilters) {
            $query->whereHas('status', function ($q) use ($statusFilters) {
                $q->whereIn('name', $statusFilters);
            })->with(['status', 'client']);
        }]);

        $products = (clone $baseQuery)
            ->when(! $showAll, function ($query) {
                $query->whereColumn('stock', '<=', 'reorder_level');
            })
            ->orderByRaw('CASE WHEN stock <= reorder_level THEN 0 ELSE 1 END')
            ->orderBy('stock')
            ->get();

        $allProducts = $baseQuery->get();
        $summary = [
            'low_stock' => $allProducts->where(fn ($product) => $product->stock <= $product->reorder_level)->count(),
            'total_products' => $allProducts->count(),
            'pending_units' => $allProducts->sum(fn ($product) => $product->orders->sum('quantity')),
        ];

        $inProcessOrders = Order::with(['client', 'product'])
            ->whereHas('status', fn ($query) => $query->where('name', 'In process'))
            ->orderBy('created_at', 'desc')
            ->limit(25)
            ->get();

        $openRequests = PurchaseRequest::with([
                'order.client',
                'product',
                'updates.user',
                'assignedTo',
                'requestedBy',
            ])
            ->whereIn('status', ['pending', 'ordered'])
            ->latest()
            ->get();

        $recentUpdates = PurchaseUpdate::with(['purchaseRequest.order', 'user'])
            ->latest()
            ->limit(15)
            ->get();

        return view('purchasing.dashboard', [
            'products' => $products,
            'summary' => $summary,
            'statusFilters' => $statusFilters,
            'showAll' => $showAll,
            'inProcessOrders' => $inProcessOrders,
            'openRequests' => $openRequests,
            'recentUpdates' => $recentUpdates,
        ]);
    }

    public function sendAlert(Product $product)
    {
        $this->inventoryAlertService->notifyIfBelowThreshold($product->refresh());

        return redirect()
            ->route('purchasing.dashboard')
            ->with('success', 'Se reenvió la alerta de inventario para ' . $product->name . '.');
    }
}
