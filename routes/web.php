<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ArchivedOrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PurchasingDashboardController;
use App\Http\Controllers\PurchaseRequestController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\SystemSettingController;
use App\Http\Controllers\WarehouseDashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;

// Página pública (inicio: búsqueda de pedidos)
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [HomeController::class, 'search'])->name('search');

// Rutas públicas para seguimiento de pedidos
use App\Http\Controllers\TrackOrderController;
Route::get('/track', [TrackOrderController::class, 'index'])->name('track.index');
Route::post('/track', [TrackOrderController::class, 'track'])->name('track.order');
Route::get('/track/result', [TrackOrderController::class, 'view'])->name('track.view');

// Rutas de autenticación (login, sin registro público)
Auth::routes(['register' => false]);

// Rutas protegidas por autenticación
Route::middleware('auth')->group(function () {
    
    // Dashboard (solo logueados)
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

    // Perfil del usuario autenticado
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Gestión de usuarios (solo Admin)
    Route::resource('users', UserController::class)->middleware('role:Admin');

    // Gestión de clientes
    Route::resource('clients', ClientController::class)->middleware('role:Admin');

    // Gestión de productos
    Route::resource('products', ProductController::class)->middleware('role:Admin');

    // Gestión de roles, departamentos y estados (solo Admin)
    Route::resource('roles', RoleController::class)->middleware('role:Admin');
    Route::resource('departments', DepartmentController::class)->middleware('role:Admin');
    Route::resource('statuses', StatusController::class)->middleware('role:Admin');

    // Búsqueda avanzada de órdenes (solo personal autorizado)
    Route::get('/orders/search', [OrderController::class, 'search'])->name('orders.search');

    // Gestión de pedidos (CRUD completo)
    Route::resource('orders', OrderController::class);

    // Marcar como entregada
    Route::patch('/orders/{order}/delivered', [OrderController::class, 'markAsDelivered'])->name('orders.delivered');

    // Cambio de estado
    Route::post('/orders/{order}/status', [OrderController::class, 'changeStatus'])->name('orders.changeStatus');

    // Subir fotografía de evidencia
    Route::post('/orders/{order}/photo', [OrderController::class, 'uploadPhoto'])->name('orders.uploadPhoto');

    Route::middleware('role:Admin')->group(function () {
        Route::get('/settings/system', [SystemSettingController::class, 'index'])->name('settings.system');
        Route::put('/settings/system', [SystemSettingController::class, 'update'])->name('settings.system.update');

        Route::get('/reports/orders', [ReportController::class, 'orders'])->name('reports.orders');

        Route::get('/archived-orders', [ArchivedOrderController::class, 'index'])->name('orders.archived');
        Route::post('/archived-orders/{id}/restore', [ArchivedOrderController::class, 'restore'])->name('orders.restore');
    });

    Route::middleware('role:Admin,Purchasing')->group(function () {
        Route::get('/purchasing/dashboard', [PurchasingDashboardController::class, 'index'])->name('purchasing.dashboard');
        Route::post('/purchasing/products/{product}/alert', [PurchasingDashboardController::class, 'sendAlert'])->name('purchasing.alert');
        Route::post('/purchasing/requests/{purchaseRequest}/updates', [PurchaseRequestController::class, 'addUpdate'])->name('purchasing.requests.updates.store');
    });

    Route::middleware('role:Admin,Warehouse')->group(function () {
        Route::get('/warehouse/picks', [WarehouseDashboardController::class, 'index'])->name('warehouse.picks');
        Route::post('/warehouse/orders/{order}/logistics', [WarehouseDashboardController::class, 'updateLogistics'])->name('warehouse.orders.logistics');
    });
});
