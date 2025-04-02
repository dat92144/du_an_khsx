<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    DashboardController,
    SupplierController,
    SupplierPriceController,
    PurchaseRequestController,
    PurchaseController,
    MachineController,
    MachineCapacityController,
    MachineScheduleController,
    MaterialController,
    ProcessController,
    ProductController,
    BomController,
    BomItemController,
    OrderController,
    OrderDetailsController,
    InventoryMaterialController,
    InventoryProductController,
    InventorySemiProductController,
    SemiFinishedProductController,
    SpecController,
    SpecAttributeController,
    SpecAttributeValueController,
    RoutingBomController,
    UnitController,
    CustomerController,
    ProductionOrderController,
    ProductionHistoryController,
    ProductionPlanningController
};

// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('permission:Admin');

    // Supplier & Prices
    Route::get('/supplier', [SupplierController::class, 'index'])->middleware('permission:Admin');
    Route::get('/supplierprice/{id}', [SupplierPriceController::class, 'index'])->middleware('permission:Admin');

    // Purchase Requests & Notifications
    Route::get('/purchase-requests', [PurchaseRequestController::class, 'index'])->middleware('permission:Admin');
    Route::get('/notifications', [PurchaseRequestController::class, 'getNotifications'])->middleware('permission:Admin');
    Route::get('/purchase-requests/{id}', [PurchaseRequestController::class, 'show'])->middleware('permission:Admin');
    Route::post('/purchase-requests/{id}/approve', [PurchaseRequestController::class, 'approve'])->middleware('permission:Admin');
    Route::post('/purchase-requests/{id}/reject', [PurchaseRequestController::class, 'reject'])->middleware('permission:Admin');
    Route::delete('/purchase-requests/{id}/delete', [PurchaseRequestController::class, 'deleteNotification'])->middleware('permission:Admin');

    // Machines
    Route::apiResource('machines', MachineController::class)->middleware('permission:Admin');
    Route::apiResource('machine-capacities', MachineCapacityController::class)->middleware('permission:Admin');
    Route::apiResource('machine-schedules', MachineScheduleController::class)->middleware('permission:Admin');

    // Materials & Units
    Route::apiResource('materials', MaterialController::class)->middleware('permission:Admin');
    Route::apiResource('units', UnitController::class)->middleware('permission:Admin');

    // Process & Product
    Route::apiResource('processes', ProcessController::class)->middleware('permission:Admin');
    Route::apiResource('products', ProductController::class)->middleware('permission:Admin');

    // BOM & BOM Items
    Route::get('/products/{product}/boms', [BomController::class, 'index'])->middleware('permission:Admin');
    Route::post('/boms', [BomController::class, 'store'])->middleware('permission:Admin');
    Route::put('/boms/{id}', [BomController::class, 'update'])->middleware('permission:Admin');
    Route::delete('/boms/{id}', [BomController::class, 'destroy'])->middleware('permission:Admin');

    Route::get('/boms/{bom}/items', [BomItemController::class, 'index'])->middleware('permission:Admin');
    Route::post('/boms/{bom}/items', [BomItemController::class, 'store'])->middleware('permission:Admin');
    Route::put('/bom-items/{id}', [BomItemController::class, 'update'])->middleware('permission:Admin');
    Route::delete('/bom-items/{id}', [BomItemController::class, 'destroy'])->middleware('permission:Admin');

    // Orders & Order Details
    Route::apiResource('orders', OrderController::class)->middleware('permission:Admin');
    Route::get('/orders/{id}/items', [OrderController::class, 'getOrderItems'])->middleware('permission:Admin');
    Route::apiResource('order-details', OrderDetailsController::class)->middleware('permission:Admin');

    // Inventory
    Route::apiResource('inventory-materials', InventoryMaterialController::class)->middleware('permission:Admin');
    Route::apiResource('inventory-products', InventoryProductController::class)->middleware('permission:Admin');
    Route::apiResource('inventory-semi-products', InventorySemiProductController::class)->middleware('permission:Admin');

    // Semi-Finished Products
    Route::apiResource('semi-finished-products', SemiFinishedProductController::class)->middleware('permission:Admin');

    // Specs & Attributes
    Route::apiResource('specs', SpecController::class)->middleware('permission:Admin');
    Route::apiResource('spec-attributes', SpecAttributeController::class)->middleware('permission:Admin');
    Route::apiResource('spec-attribute-values', SpecAttributeValueController::class)->middleware('permission:Admin');

    // Routing BOM
    Route::apiResource('routing-boms', RoutingBomController::class)->middleware('permission:Admin');

    // Customers
    Route::apiResource('customers', CustomerController::class)->middleware('permission:Admin');

    // Production Orders & Planning & History
    Route::apiResource('production-orders', ProductionOrderController::class)->middleware('permission:Admin');
    Route::apiResource('production-histories', ProductionHistoryController::class)->middleware('permission:Admin');

    // Production Planning
    Route::post('/production-planning', [ProductionPlanningController::class, 'autoPlan'])->middleware('permission:Admin');
    Route::get('/production-plans', [ProductionPlanningController::class, 'getPlans'])->middleware('permission:Admin');
    Route::get('/production-orders/{orderId}/plans', [ProductionPlanningController::class, 'getPlansByOrder'])->middleware('permission:Admin');

    // MRP Logic
    Route::get('/mrp', [PurchaseController::class, 'caculateMRP'])->middleware('permission:Admin');
});
