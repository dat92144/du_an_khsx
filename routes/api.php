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
    ProductionPlanningController,
    GanttController
};

// Auth Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/send-verification-code', [AuthController::class, 'sendVerificationCode']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);

    Route::middleware('permission:Admin')->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index']);

        // Suppliers
        Route::get('/supplier', [SupplierController::class, 'index']);
        Route::get('/supplierprice/{id}', [SupplierPriceController::class, 'index']);

        // Orders
        Route::apiResource('orders', OrderController::class);
        Route::post('/orders/{id}/start-production', [OrderController::class, 'produce']);
        Route::post('orders/estimate-delivery', [OrderController::class, 'estimateDelivery']);

        // Purchase Requests
        Route::apiResource('purchase-requests', PurchaseRequestController::class);
        Route::get('/notifications', [PurchaseRequestController::class, 'getNotifications']);
        Route::post('/purchase-requests/{id}/approve', [PurchaseRequestController::class, 'approve']);
        Route::post('/purchase-requests/{id}/reject', [PurchaseRequestController::class, 'reject']);
        Route::delete('/purchase-requests/{id}/delete', [PurchaseRequestController::class, 'deleteNotification']);

        // Machines
        Route::apiResources([
            'machines' => MachineController::class,
            'machine-capacities' => MachineCapacityController::class,
            'machine-schedules' => MachineScheduleController::class,
        ]);

        // Inventory
        Route::apiResources([
            'inventory-materials' => InventoryMaterialController::class,
            'inventory-products' => InventoryProductController::class,
            'inventory-semi-products' => InventorySemiProductController::class,
        ]);

        // Materials & Products
        Route::apiResources([
            'materials' => MaterialController::class,
            'units' => UnitController::class,
            'processes' => ProcessController::class,
            'products' => ProductController::class,
            'semi-finished-products' => SemiFinishedProductController::class,
        ]);

        // BOM & Routing BOM
        Route::get('/products/{product}/boms', [BomController::class, 'index']);
        Route::apiResource('boms', BomController::class);
        Route::get('/boms/{bom}/items', [BomItemController::class, 'index']);
        Route::post('/boms/{bom}/items', [BomItemController::class, 'store']);
        Route::put('/bom-items/{id}', [BomItemController::class, 'update']);
        Route::delete('/bom-items/{id}', [BomItemController::class, 'destroy']);
        Route::apiResource('routing-boms', RoutingBomController::class);

        // Specs
        Route::apiResources([
            'specs' => SpecController::class,
            'spec-attributes' => SpecAttributeController::class,
            'spec-attribute-values' => SpecAttributeValueController::class,
        ]);

        // Customers
        Route::apiResource('customers', CustomerController::class);

        // Production
        Route::apiResources([
            'production-orders' => ProductionOrderController::class,
            'production-histories' => ProductionHistoryController::class,
        ]);
        Route::post('/production-planning', [ProductionPlanningController::class, 'autoPlan']);
        Route::get('/production-plans', [ProductionPlanningController::class, 'getPlans']);
        Route::get('/production-orders/{orderId}/plans', [ProductionPlanningController::class, 'getPlansByOrder']);

        // MRP (nếu cần)
        // Route::get('/mrp', [PurchaseController::class, 'caculateMRP']);

        Route::get('/gantt/orders', [GanttController::class, 'forOrders']);
        Route::get('/gantt/product-lot', [GanttController::class, 'productWithLots']);
        Route::get('/gantt/lot-detail', [GanttController::class, 'getLotDetail']);
        Route::get('/gantt/machine', [GanttController::class, 'getMachineGantt']);

    });
});
