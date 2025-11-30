<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderStatus;
use App\Models\PurchaseRequest;
use App\Models\PurchaseUpdate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class WarehouseDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:Admin,Warehouse');
    }

    public function index(Request $request)
    {
        $baseStatusNames = ['Ordered', 'In process', 'In route', 'Delivered'];
        $statusNames = array_merge($baseStatusNames, ['No Delivered']);
        $statusFilter = $request->input('status', 'Ordered');
        if (! in_array($statusFilter, $statusNames, true)) {
            $statusFilter = 'Ordered';
        }

        $ordersQuery = Order::query()
            ->with(['client', 'product', 'status', 'routeOperator', 'purchaseRequest.updates.user'])
            ->where('is_deleted', false)
            ->when($request->filled('query'), function ($query) use ($request) {
                $term = $request->string('query');
                $query->where(function ($sub) use ($term) {
                    $sub->where('invoice_number', 'like', '%' . $term . '%')
                        ->orWhere('customer_name', 'like', '%' . $term . '%')
                        ->orWhereHas('client', function ($clientQuery) use ($term) {
                            $clientQuery->where('name', 'like', '%' . $term . '%');
                        });
                });
            })
            ->orderByRaw('delivery_address IS NULL')
            ->orderBy('order_date');

        if ($statusFilter === 'No Delivered') {
            $ordersQuery->whereHas('status', fn ($query) => $query->where('name', 'In route'))
                ->where('has_incident', true);
        } else {
            $ordersQuery->whereHas('status', fn ($query) => $query->where('name', $statusFilter));
        }

        $orders = $ordersQuery->limit(50)->get();

        $statusCollection = OrderStatus::whereIn('name', $baseStatusNames)
            ->withCount(['orders' => fn ($query) => $query->where('is_deleted', false)])
            ->get();

        $statusSummary = $statusCollection->mapWithKeys(fn ($status) => [$status->name => $status->orders_count])->toArray();

        $statusSummary['No Delivered'] = Order::where('is_deleted', false)
            ->whereHas('status', fn ($query) => $query->where('name', 'In route'))
            ->where('has_incident', true)
            ->count();

        $statusMap = $statusCollection->keyBy('name');
        $statusMap['No Delivered'] = (object) [
            'id' => null,
            'name' => 'No Delivered',
            'color' => '#dc3545',
        ];

        return view('warehouse.picks', [
            'orders' => $orders,
            'statusFilter' => $statusFilter,
            'statusNames' => $statusNames,
            'statusSummary' => $statusSummary,
            'statusMap' => $statusMap,
        ]);
    }

    public function updateLogistics(Request $request, Order $order)
    {
        $data = $request->validate([
            'missing_items' => 'nullable|string|max:500',
            'incident_notes' => 'nullable|string|max:500',
        ]);

        $order->missing_items = $data['missing_items'] ?? null;
        $order->incident_notes = $data['incident_notes'] ?? null;
        $order->save();

        $this->syncPurchaseRequest($order);

        OrderHistory::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'from_status_id' => $order->status_id,
            'to_status_id' => $order->status_id,
            'notes' => 'Actualización logística desde el panel de almacén',
        ]);

        return back()->with('success', 'Se actualizó la información logística de la orden ' . $order->invoice_number . '.');
    }


    private function syncPurchaseRequest(Order $order): void
    {
        $missingText = trim((string) $order->missing_items);

        if ($missingText === '') {
            return;
        }

        $materialName = $order->product?->name ?? 'Material faltante';

        $purchaseRequest = PurchaseRequest::firstOrCreate(
            ['order_id' => $order->id],
            [
                'product_id' => $order->product_id,
                'requested_by' => Auth::id(),
                'material_name' => $materialName,
                'quantity_needed' => $order->quantity,
                'details' => $missingText,
                'status' => 'pending',
            ]
        );

        $needsUpdate = $purchaseRequest->wasRecentlyCreated;

        if ($purchaseRequest->details !== $missingText) {
            $purchaseRequest->details = $missingText;
            $needsUpdate = true;
        }

        if ($purchaseRequest->quantity_needed !== $order->quantity) {
            $purchaseRequest->quantity_needed = $order->quantity;
            $needsUpdate = true;
        }

        if ($purchaseRequest->material_name !== $materialName) {
            $purchaseRequest->material_name = $materialName;
            $needsUpdate = true;
        }

        if ($purchaseRequest->status === 'received') {
            $purchaseRequest->status = 'pending';
            $needsUpdate = true;
        }

        if ($needsUpdate) {
            $purchaseRequest->save();

            PurchaseUpdate::create([
                'purchase_request_id' => $purchaseRequest->id,
                'user_id' => Auth::id(),
                'status' => 'pending',
                'notes' => 'Faltante registrado por Almacén: ' . $missingText,
            ]);
        }
    }
}
