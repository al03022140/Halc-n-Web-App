<?php
namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderStatus;
use App\Models\Photo;
use App\Models\Product;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\InventoryAlertService;
use App\Services\InvoiceNumberGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class OrderController extends Controller
{
    public function __construct(
        private InvoiceNumberGenerator $invoiceNumberGenerator,
        private InventoryAlertService $inventoryAlertService
    )
    {
        $this->middleware('role:Admin,Sales')->only(['create', 'store', 'edit', 'update']);
        $this->middleware('role:Admin')->only(['destroy']);
        $this->middleware('role:Admin,Warehouse,Route')->only(['changeStatus', 'markAsDelivered', 'uploadPhoto']);
    }

    // Lista de órdenes
    public function index()
    {
        $ordersQuery = Order::with(['status', 'user', 'client'])
            ->where('is_deleted', false);

        $currentRole = $this->currentUserRole();

        if ($currentRole === 'Route') {
            $allowedStatusIds = OrderStatus::whereIn('name', ['In route', 'Delivered'])->pluck('id')->toArray();
            $ordersQuery->where('route_user_id', Auth::id())
                        ->whereIn('status_id', $allowedStatusIds);
        } elseif ($currentRole === 'Purchasing') {
            $inProcessStatusId = OrderStatus::where('name', 'In process')->value('id');
            if ($inProcessStatusId) {
                $ordersQuery->where('status_id', $inProcessStatusId);
            } else {
                $ordersQuery->whereRaw('1 = 0');
            }
        }

        $orders = $ordersQuery->get();

        return view('orders.index', compact('orders'));
    }

    // Buscar orden por ID o cliente
    public function search(Request $request)
    {
        $filters = $request->only([
            'invoice_number',
            'customer_number',
            'customer_custom_id',
            'query',
            'status_id',
            'date_from',
            'date_to',
        ]);

        // Si no se envían criterios, mostrar el formulario de búsqueda con filtros
        $hasFilters = collect($filters)->filter(fn ($value) => filled($value))->isNotEmpty();

        if (!$hasFilters) {
            return view('orders.search', ['filters' => $filters]);
        }

        $filledFilters = collect($filters)->filter(fn ($value) => filled($value));

        if ($filledFilters->count() < 2) {
            return view('orders.search', [
                'filters' => $filters,
                'minFiltersError' => 'Ingresa al menos dos criterios para ejecutar la búsqueda de órdenes.',
            ]);
        }

        $ordersQuery = Order::with(['status', 'user'])
            ->when($request->filled('invoice_number'), function($q) use ($request) {
                $q->where('invoice_number', 'like', '%' . $request->invoice_number . '%');
            })
            ->when($request->filled('customer_number'), function($q) use ($request) {
                $q->where('customer_number', 'like', '%' . $request->customer_number . '%');
            })
            ->when($request->filled('customer_custom_id'), function($q) use ($request) {
                $q->where(function($qq) use ($request) {
                    $qq->where('customer_custom_id', 'like', '%' . $request->customer_custom_id . '%')
                       ->orWhereHas('client', function($q2) use ($request) {
                           $q2->where('custom_id', 'like', '%' . $request->customer_custom_id . '%');
                       });
                });
            })
            ->when($request->filled('query'), function($q) use ($request) {
                $q->where(function($qq) use ($request) {
                    $qq->where('customer_name', 'like', '%' . $request->query . '%')
                       ->orWhere('invoice_number', 'like', '%' . $request->query . '%')
                       ->orWhere('customer_number', 'like', '%' . $request->query . '%');
                });
            })
            ->when($request->filled('status_id'), function($q) use ($request) {
                $q->where('status_id', $request->status_id);
            })
            ->when($request->filled('date_from'), function($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->date_to);
            })
            ->where('is_deleted', false);

        $currentRole = $this->currentUserRole();
        if ($currentRole === 'Route') {
            $allowedStatusIds = OrderStatus::whereIn('name', ['In route', 'Delivered'])->pluck('id')->toArray();
            $ordersQuery->where('route_user_id', Auth::id())
                        ->whereIn('status_id', $allowedStatusIds);
        } elseif ($currentRole === 'Purchasing') {
            $inProcessStatusId = OrderStatus::where('name', 'In process')->value('id');
            if ($inProcessStatusId) {
                $ordersQuery->where('status_id', $inProcessStatusId);
            } else {
                $ordersQuery->whereRaw('1 = 0');
            }
        }

        $orders = $ordersQuery->get();

        return view('orders.search_results', compact('orders'));
    }

    // Mostrar formulario de creación
    public function create()
    {
        $statuses = OrderStatus::all();
        $users = User::all();
        $clients = Client::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();
        return view('orders.create', compact('statuses', 'users', 'clients', 'products'));
    }


public function store(Request $request)
    {
        // Validar los datos del formulario
        $data = $request->validate([
            'invoice_number' => 'nullable|string|max:255',
            'client_id' => 'required|exists:clients,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'status_id' => 'nullable|exists:order_statuses,id',
            'user_id' => 'required|exists:users,id',
            'route_user_id' => 'nullable|exists:users,id',
            'order_date' => 'nullable|date',
            'delivery_address' => 'nullable|string',
            'notes' => 'nullable|string',
            'fiscal_data' => 'nullable|string|max:255',
            'has_incident' => 'nullable|boolean',
            'incident_notes' => 'nullable|string',
            'missing_items' => 'nullable|string',
            // note: file fields validated conditionally below to avoid MIME guesser errors when no file is sent
        ]);

            if ($this->currentUserRole() === 'Sales' && ! empty($data['route_user_id'])) {
                throw ValidationException::withMessages([
                    'route_user_id' => 'Ventas no puede asignar operadores de ruta.',
                ]);
            }

        $this->ensureRouteOperator($data['route_user_id'] ?? null);

        // Si no envían invoice_number, generamos uno
        if (empty($data['invoice_number'])) {
            $data['invoice_number'] = $this->invoiceNumberGenerator->generate();
        }

        // Estado por defecto 'Ordered' si no se envía
        $defaultStatusId = OrderStatus::where('name', 'Ordered')->value('id');
        if (empty($data['status_id'])) {
            if (!$defaultStatusId) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['status_id' => 'No existe un estado predeterminado para nuevas órdenes. Configura uno antes de continuar.']);
            }
            $data['status_id'] = $defaultStatusId;
        }

        if ($this->currentUserRole() === 'Sales' && $defaultStatusId) {
            $data['status_id'] = $defaultStatusId;
        }

        // Derivar datos del cliente
        $client = Client::find($data['client_id']);
        if ($client) {
            $data['customer_name'] = $client->name;
            $data['customer_number'] = $client->phone;
            $data['customer_custom_id'] = $client->custom_id;
            if (empty($data['delivery_address'])) {
                $data['delivery_address'] = $client->address;
            }
            if (empty($data['fiscal_data']) && !empty($client->fiscal_data)) {
                $data['fiscal_data'] = $client->fiscal_data;
            }
        }

        $requireFiscal = SystemSetting::bool('require_fiscal_data', true);
        if ($requireFiscal && empty($data['fiscal_data'])) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['fiscal_data' => 'Debes capturar los datos fiscales del cliente.']);
        }

        $requireAddress = SystemSetting::bool('require_delivery_address', true);
        if ($requireAddress && empty($data['delivery_address'])) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['delivery_address' => 'Debes proporcionar una dirección de entrega.']);
        }

        $data['has_incident'] = $request->boolean('has_incident');

        // Procesar subida de imágenes (validar solo si se envían archivos)
        if ($request->hasFile('start_image')) {
            $request->validate([
                'start_image' => 'image|mimes:jpg,jpeg,png|max:4096',
            ]);
            try {
                $data['start_image'] = $request->file('start_image')->store('orders', 'public');
            } catch (\Exception $e) {
                // Log del error
            }
        }

        if ($request->hasFile('end_image')) {
            $request->validate([
                'end_image' => 'image|mimes:jpg,jpeg,png|max:4096',
            ]);
            try {
                $data['end_image'] = $request->file('end_image')->store('orders', 'public');
            } catch (\Exception $e) {
                // Log del error
            }
        }

        try {
            DB::beginTransaction();

            $order = Order::create($data);
            $order->load('product');
            $this->applyStockChange($order->product, -1 * (int) $order->quantity);
            $this->logHistory($order, null, $order->status_id, 'Creación de la orden');

            DB::commit();

            return redirect()->route('orders.index')
                ->with('success', 'Orden #' . $order->invoice_number . ' creada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            // Log del error
            //\Log::error('Error al crear orden: ' . $e->getMessage());
            
            // Devolver al formulario con mensaje de error
             return redirect()->back()
                 ->withInput()
                 ->with('error', 'Error al crear la orden: ' . $e->getMessage());
        }
    }

    // Mostrar una orden
    public function show(Order $order)
    {
        $this->ensureRouteOwnership($order);
        $this->ensurePurchasingVisibility($order);

        // Eager-load relaciones relevantes y calcular costo total
        $order->load([
            'product',
            'client',
            'status',
            'user',
            'routeOperator',
            'photos',
            'histories.user',
            'histories.fromStatus',
            'histories.toStatus',
        ]);

        $total = 0;
        if ($order->product && $order->quantity) {
            $total = (float) $order->product->price * (int) $order->quantity;
        }

        $deliveredStatusId = OrderStatus::where('name', 'Delivered')->value('id');
        $isRouteOwner = $this->isOrderAssignedToCurrentRoute($order);
        $routeOperators = [];
        if ($this->userHasRole(['Admin', 'Warehouse'])) {
            $routeOperators = User::with('role')
                ->whereHas('role', fn ($q) => $q->where('name', 'Route'))
                ->where('active', true)
                ->orderBy('name')
                ->get();
        }

        return view('orders.show', compact('order', 'total', 'deliveredStatusId', 'isRouteOwner', 'routeOperators'));
    }

public function edit(Order $order)
    {
        if ($this->currentUserRole() === 'Sales' && optional($order->status)->name !== 'Ordered') {
            throw new HttpResponseException(redirect()->route('dashboard')->with('error', 'Ventas solo puede modificar pedidos en estado Ordered.'));
        }

        $statuses = OrderStatus::all();
        $users = User::all();
        $clients = Client::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();

        return view('orders.edit', compact('order', 'statuses', 'users', 'clients', 'products'));
    }


    // Actualizar orden
    public function update(Request $request, Order $order)
    {
        if ($this->currentUserRole() === 'Sales' && optional($order->status)->name !== 'Ordered') {
            throw new HttpResponseException(redirect()->route('dashboard')->with('error', 'Ventas solo puede modificar pedidos en estado Ordered.'));
        }

        $originalStatusId = $order->status_id;
        $originalProductId = $order->product_id;
        $originalQuantity = $order->quantity;
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'status_id' => 'required|exists:order_statuses,id',
            'user_id' => 'required|exists:users,id',
            'route_user_id' => 'nullable|exists:users,id',
            'order_date' => 'nullable|date',
            'delivery_address' => 'nullable|string',
            'notes' => 'nullable|string',
            'fiscal_data' => 'nullable|string|max:255',
            'has_incident' => 'nullable|boolean',
            'incident_notes' => 'nullable|string',
            'missing_items' => 'nullable|string',
            // note: file fields validated conditionally below to avoid MIME guesser errors when no file is sent
        ]);
        
            if ($this->currentUserRole() === 'Sales' && ! empty($data['route_user_id'])) {
                throw ValidationException::withMessages([
                    'route_user_id' => 'Ventas no puede asignar operadores de ruta.',
                ]);
            }

        // Si order_date está vacío, establecerlo como null
        if (empty($data['order_date'])) {
            $data['order_date'] = null;
        }
        
        // Procesar subida de imágenes (validar solo si se envían archivos)
        if ($request->hasFile('start_image')) {
            $request->validate([
                'start_image' => 'image|mimes:jpg,jpeg,png|max:4096',
            ]);
            try {
                // Eliminar imagen anterior si existe
                if ($order->start_image && file_exists(storage_path('app/public/' . $order->start_image))) {
                    unlink(storage_path('app/public/' . $order->start_image));
                }
                $data['start_image'] = $request->file('start_image')->store('orders', 'public');
            } catch (\Exception $e) {
                // Log del error
            }
        } else {
            // Si no se sube una nueva imagen, mantener la existente
            unset($data['start_image']);
        }

        if ($request->hasFile('end_image')) {
            $request->validate([
                'end_image' => 'image|mimes:jpg,jpeg,png|max:4096',
            ]);
            try {
                // Eliminar imagen anterior si existe
                if ($order->end_image && file_exists(storage_path('app/public/' . $order->end_image))) {
                    unlink(storage_path('app/public/' . $order->end_image));
                }
                $data['end_image'] = $request->file('end_image')->store('orders', 'public');
            } catch (\Exception $e) {
                // Log del error
            }
        } else {
            // Si no se sube una nueva imagen, mantener la existente
            unset($data['end_image']);
        }

        $this->ensureRouteOperator($data['route_user_id'] ?? null);
        $data['has_incident'] = $request->boolean('has_incident');

        try {
            // Derivar datos del cliente si se actualiza
            $client = Client::find($data['client_id']);
            if ($client) {
                $data['customer_name'] = $client->name;
                $data['customer_number'] = $client->phone;
                $data['customer_custom_id'] = $client->custom_id;
                if (empty($data['delivery_address']) && !empty($client->address)) {
                    $data['delivery_address'] = $client->address;
                }
                if (empty($data['fiscal_data']) && !empty($client->fiscal_data)) {
                    $data['fiscal_data'] = $client->fiscal_data;
                }
            }

            if (empty($data['delivery_address'])) {
                $data['delivery_address'] = $order->delivery_address;
            }

            if (empty($data['delivery_address'])) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['delivery_address' => 'Debes proporcionar una dirección de entrega.']);
            }

            if (empty($data['fiscal_data'])) {
                $data['fiscal_data'] = $order->fiscal_data;
            }

            if (empty($data['fiscal_data'])) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['fiscal_data' => 'Debes capturar los datos fiscales del cliente.']);
            }

            $updatedStatusId = $data['status_id'];
            unset($data['status_id']);

            DB::beginTransaction();

            $order->update($data);
            $order->refresh();
            $this->reconcileInventory($order, $originalProductId, $originalQuantity);

            if ($updatedStatusId !== $originalStatusId) {
                $newStatus = OrderStatus::findOrFail($updatedStatusId);
                $this->ensureTransitionAllowed($order, $newStatus);
                $this->applyStatusChange($order, $newStatus, 'Actualización manual desde el formulario');
            }

            DB::commit();

            return redirect()->route('orders.index')
                ->with('success', 'Orden #' . $order->invoice_number . ' actualizada correctamente.');
        } catch (HttpResponseException $e) {
            DB::rollBack();
            throw $e;
        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log del error
            //\Log::error('Error al actualizar orden: ' . $e->getMessage());
            
            // Devolver al formulario con mensaje de error
             return redirect()->back()
                 ->withInput()
                 ->with('error', 'Error al actualizar la orden: ' . $e->getMessage());
         }
      }

    // Eliminar (archivar) orden
    public function destroy(Order $order)
    {
        $order->is_deleted = true;
        $order->save();
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Orden eliminada.');
    }

    // Marcar como entregada
    public function markAsDelivered(Order $order)
    {
        $this->ensureRouteOwnership($order);
        if (! $this->userHasRole(['Route', 'Admin'])) {
            throw new HttpResponseException(redirect()->route('dashboard')->with('error', 'Solo Ruta o Admin pueden completar la entrega.'));
        }

        if (optional($order->status)->name === 'Delivered') {
            return redirect()->back()->with('success', 'La orden ya estaba marcada como entregada.');
        }

        if (! $order->end_image && ! $order->has_incident) {
            return redirect()->back()->with('error', 'Debes subir evidencia de entrega o documentar una incidencia antes de marcar como entregado.');
        }

        $deliveredStatus = OrderStatus::where('name', 'Delivered')->first();
        if (! $deliveredStatus) {
            return redirect()->route('orders.index')->with('error', 'Estado Delivered no encontrado.');
        }

        $this->ensureTransitionAllowed($order, $deliveredStatus);
        $this->applyStatusChange($order, $deliveredStatus, 'Entrega confirmada desde el panel.');

        return redirect()->route('orders.index')->with('success', 'Orden marcada como entregada.');
    }

    // Cambiar estado de la orden
    public function changeStatus(Request $request, Order $order)
    {
        $this->ensureRouteOwnership($order);
        $data = $request->validate([
            'status_id' => 'required|exists:order_statuses,id',
            'missing_items' => 'nullable|string',
            'incident_notes' => 'nullable|string',
            'has_incident' => 'nullable|boolean',
            'route_user_id' => 'nullable|exists:users,id',
            'status_notes' => 'nullable|string|max:500',
        ]);

        if ($request->has('route_user_id') && ! $this->userHasRole(['Warehouse', 'Admin'])) {
            throw new HttpResponseException(redirect()->route('dashboard')->with('error', 'Solo Almacén o Admin pueden reasignar rutas.'));
        }

        $this->ensureRouteOperator($data['route_user_id'] ?? null);
        $newStatus = OrderStatus::findOrFail($data['status_id']);
        // Refuerzo explícito: si el usuario es Route sólo puede mover a Delivered desde In route
        if ($this->currentUserRole() === 'Route') {
            if ($newStatus->name !== 'Delivered') {
                throw new HttpResponseException(redirect()->route('dashboard')->with('error', 'Route solo puede marcar pedidos como Delivered.'));
            }
            if (optional($order->status)->name !== 'In route') {
                throw new HttpResponseException(redirect()->route('dashboard')->with('error', 'Route solo puede marcar como Delivered órdenes que estén en In route.'));
            }
        }

        $statusChanged = $newStatus->id !== $order->status_id;
        if ($statusChanged) {
            $this->ensureTransitionAllowed($order, $newStatus);
        }

        $newHasIncident = $request->boolean('has_incident', $order->has_incident);
        $incidentBlocksDelivery = $newStatus->name === 'Delivered' && $newHasIncident;
        if ($incidentBlocksDelivery) {
            $statusChanged = false;
        }

        $order->missing_items = $data['missing_items'] ?? $order->missing_items;
        $order->incident_notes = $data['incident_notes'] ?? $order->incident_notes;
        $order->has_incident = $newHasIncident;
        if (! empty($data['route_user_id'])) {
            $order->route_user_id = $data['route_user_id'];
        } elseif ($request->has('route_user_id')) {
            $order->route_user_id = null;
        }
        $order->save();

        if ($statusChanged) {
            if ($newStatus->name === 'Delivered' && ! $order->end_image && ! $order->has_incident) {
                throw new HttpResponseException(
                    redirect()->back()->with('error', 'Debes subir evidencia de entrega o documentar una incidencia antes de marcar como entregado.')
                );
            }
            $this->applyStatusChange($order, $newStatus, $data['status_notes'] ?? null);
        } else {
            $shouldLogUpdate = ! empty($data['status_notes'])
                || $request->has('route_user_id')
                || $request->filled('missing_items')
                || $request->filled('incident_notes')
                || $request->has('has_incident');

            if ($shouldLogUpdate) {
                $historyNote = $data['status_notes']
                    ?? ($incidentBlocksDelivery ? 'Incidencia registrada; pedido permanece como No Delivered.' : 'Actualización logística sin cambio de estado');
                $this->logHistory($order, $order->status_id, $order->status_id, $historyNote);
            }
        }

        $flashType = $incidentBlocksDelivery ? 'info' : 'success';
        $flashMessage = $incidentBlocksDelivery
            ? 'Se registró la incidencia. El pedido permanece como No Delivered.'
            : 'Estado de la orden actualizado.';

        return redirect()->route('orders.show', $order)->with($flashType, $flashMessage);
    }

    // Subir fotografía de evidencia
    public function uploadPhoto(Request $request, Order $order)
    {
        $this->ensureRouteOwnership($order);
        if (! $this->userHasRole(['Route', 'Admin'])) {
            throw new HttpResponseException(redirect()->route('dashboard')->with('error', 'Solo el rol Route puede subir evidencia.'));
        }

        $data = $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png|max:4096',
            'photo_type' => 'required|string|in:start,end',
            'notes' => 'nullable|string',
        ]);

        $path = $request->file('photo')->store('orders', 'public');

        $autoDelivered = false;

        // Asignar evidencia al pedido
        if ($data['photo_type'] === 'start') {
            $order->start_image = $path;
        } else {
            $order->end_image = $path;
            $deliveredStatus = OrderStatus::where('name', 'Delivered')->first();
            if ($deliveredStatus && ! $order->has_incident) {
                $this->ensureTransitionAllowed($order, $deliveredStatus);
                $this->applyStatusChange($order, $deliveredStatus, 'Evidencia de entrega cargada');
                $order->refresh();
                $autoDelivered = true;
            }
        }
        $order->save();

        // Registrar en tabla photos para historial
        Photo::create([
            'order_id' => $order->id,
            'path' => $path,
            'type' => $data['photo_type'] === 'end' ? 'entrega' : 'en_ruta',
            'notes' => $data['notes'] ?? null,
        ]);

        if ($data['photo_type'] === 'end' && $order->has_incident && ! $autoDelivered) {
            return redirect()->route('orders.show', $order)->with('info', 'La evidencia se guardó, pero la orden queda como No Delivered hasta resolver la incidencia.');
        }

        $message = $autoDelivered ? 'Evidencia final guardada y pedido marcado como entregado.' : 'Fotografía subida correctamente.';

        return redirect()->route('orders.show', $order)->with('success', $message);
    }

    private function ensureRouteOwnership(Order $order): void
    {
        if ($this->currentUserRole() !== 'Route') {
            return;
        }

        if ($order->route_user_id !== Auth::id()) {
            throw new HttpResponseException(redirect()->route('dashboard')->with('error', 'Esta orden no está asignada a tu ruta.'));
        }
    }

    private function ensurePurchasingVisibility(Order $order): void
    {
        if ($this->currentUserRole() !== 'Purchasing') {
            return;
        }

        if (optional($order->status)->name !== 'In process') {
            throw new HttpResponseException(redirect()->route('dashboard')->with('error', 'Compras solo puede consultar pedidos en estado In process.'));
        }
    }

    private function isOrderAssignedToCurrentRoute(Order $order): bool
    {
        return $this->currentUserRole() === 'Route'
            && $order->route_user_id === Auth::id();
    }

    private function currentUserRole(): ?string
    {
        return optional(Auth::user()?->role)->name;
    }

    private function userHasRole(array $roles): bool
    {
        $role = $this->currentUserRole();

        return $role && in_array($role, $roles, true);
    }

    private function ensureTransitionAllowed(Order $order, OrderStatus $targetStatus): void
    {
        $currentName = optional($order->status)->name ?? 'Ordered';
        $targetName = $targetStatus->name;
        $role = $this->currentUserRole();

        $allowedSequential = [
            'Ordered' => ['In process'],
            'In process' => ['In route'],
            'In route' => ['Delivered'],
        ];

        if (! isset($allowedSequential[$currentName]) || ! in_array($targetName, $allowedSequential[$currentName], true)) {
            throw new HttpResponseException(redirect()->route('dashboard')->with('error', 'Transición de estado no permitida.'));
        }

        $rolePermissions = [
            'Warehouse' => [
                'Ordered' => ['In process'],
                'In process' => ['In route'],
            ],
            'Route' => [
                'In route' => ['Delivered'],
            ],
            'Admin' => $allowedSequential,
        ];

        if (! $role || ! isset($rolePermissions[$role])) {
            throw new HttpResponseException(redirect()->route('dashboard')->with('error', 'Tu rol no puede realizar esta acción.'));
        }

        $roleAllowed = $rolePermissions[$role][$currentName] ?? [];
        if (! in_array($targetName, $roleAllowed, true)) {
            throw new HttpResponseException(redirect()->route('dashboard')->with('error', 'Tu rol no puede mover el pedido a este estado.'));
        }
    }

    private function applyStatusChange(Order $order, OrderStatus $newStatus, ?string $notes = null): void
    {
        $fromStatusId = $order->status_id;
        if ($fromStatusId === $newStatus->id) {
            return;
        }

        $order->status_id = $newStatus->id;
        $order->save();

        $this->logHistory($order, $fromStatusId, $newStatus->id, $notes);
    }

    private function logHistory(Order $order, ?int $fromStatusId, ?int $toStatusId, ?string $notes = null): void
    {
        OrderHistory::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'from_status_id' => $fromStatusId,
            'to_status_id' => $toStatusId,
            'notes' => $notes,
        ]);
    }

    private function ensureRouteOperator($userId): void
    {
        if (! $userId) {
            return;
        }

        $candidate = User::with('role')->find($userId);
        if (! $candidate || optional($candidate->role)->name !== 'Route') {
            throw ValidationException::withMessages([
                'route_user_id' => 'Selecciona un usuario con rol Route para asignarlo a la entrega.',
            ]);
        }
    }

    private function applyStockChange(?Product $product, int $delta): void
    {
        if (! $product || $delta === 0) {
            return;
        }

        $product->stock = (int) $product->stock + $delta;
        $product->save();

        if ($delta < 0) {
            $product->refresh();
            $this->inventoryAlertService->notifyIfBelowThreshold($product);
        }
    }

    private function reconcileInventory(Order $order, ?int $originalProductId, ?int $originalQuantity): void
    {
        if (! $originalProductId || $originalQuantity === null) {
            return;
        }

        if ($originalProductId === $order->product_id) {
            $difference = (int) $order->quantity - (int) $originalQuantity;
            if ($difference !== 0) {
                $order->loadMissing('product');
                $this->applyStockChange($order->product, -1 * $difference);
            }
            return;
        }

        $originalProduct = Product::find($originalProductId);
        $this->applyStockChange($originalProduct, (int) $originalQuantity);

        $order->load('product');
        $this->applyStockChange($order->product, -1 * (int) $order->quantity);
    }
}
