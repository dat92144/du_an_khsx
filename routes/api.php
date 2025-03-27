<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseRequestController;
use App\Http\Controllers\SupplierController;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupplierPriceController;
use App\Http\Controllers\MachineController;
use App\Http\Controllers\ProcessController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BomController;
use App\Http\Controllers\BomItemController;
// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::middleware('permission:Admin')->get('/notifications', [PurchaseRequestController::class, 'getNotifications']);
    Route::middleware('permission:Admin')->get('/purchase-requests/{id}', [PurchaseRequestController::class, 'show']);
    Route::middleware('permission:Admin')->post('/purchase-requests/{id}/approve', [PurchaseRequestController::class, 'approve']);
    Route::middleware('permission:Admin')->post('/purchase-requests/{id}/reject', [PurchaseRequestController::class, 'reject']);
    Route::middleware('permission:Admin')->get('/supplier', [SupplierController::class, 'index']);
    Route::middleware('permission:Admin')->get('/supplierprice/{id}', [SupplierPriceController::class, 'index']);
    Route::middleware('permission:Admin')->get('/purchase-requests', [PurchaseRequestController::class, 'index']);
    Route::middleware('permission:Admin')->delete('/purchase-requests/{id}/delete', [PurchaseRequestController::class, 'deleteNotification']);
    Route::middleware('permission:Admin')->get('/dashboard', [DashboardController::class, 'index']);
    Route::middleware('permission:Admin')->get('/machines', [MachineController::class, 'index']);
    Route::middleware('permission:Admin')->post('/machines', [MachineController::class, 'store']);
    Route::middleware('permission:Admin')->put('/machines/{id}', [MachineController::class, 'update']);
    Route::middleware('permission:Admin')->delete('/machines/{id}', [MachineController::class, 'destroy']);
    Route::middleware('permission:Admin')->apiResource('processes', ProcessController::class);
    Route::middleware('permission:Admin')->apiResource('products', ProductController::class);
    Route::middleware('permission:Admin')->get('/products/{product}/boms', [BomController::class, 'index']);
    Route::middleware('permission:Admin')->post('/boms', [BomController::class, 'store']);
    Route::middleware('permission:Admin')->put('/boms/{id}', [BomController::class, 'update']);
    Route::middleware('permission:Admin')->delete('/boms/{id}', [BomController::class, 'destroy']);
    Route::middleware('permission:Admin')->get('/boms/{bom}/items', [BomItemController::class, 'index']);
    Route::middleware('permission:Admin')->post('/boms/{bom}/items', [BomItemController::class, 'store']);
    Route::middleware('permission:Admin')->delete('/bom-items/{id}', [BomItemController::class, 'destroy']);


});