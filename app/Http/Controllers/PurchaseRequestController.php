<?php

namespace App\Http\Controllers;
use App\Mail\PurchaseOrderMail;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;


class PurchaseRequestController extends Controller
{
    public function index(){
        $request = PurchaseRequests::where('status', 'pending')->with('supplier','material')->get();
        return response()->json($request);
    }
    // public function getNotifications()
    // {
    //     $count = PurchaseRequests::where('status', 'pending')->count();
    //     $requests = PurchaseRequests::where('status', 'pending')->with('supplier')->get();

    //     return response()->json([
    //         'count' => $count,
    //         'requests' => $requests
    //     ]);
    // }
    public function getNotifications()
    {
        // Lấy tất cả đề xuất mua hàng, kể cả đã duyệt hoặc từ chối
        $purchaseRequests = PurchaseRequests::with('supplier')->orderBy('created_at', 'desc')->get();

        return response()->json([
            'count' => $purchaseRequests->where('status', 'pending')->count(),
            'requests' => $purchaseRequests
        ]);
    }
    public function show($id)
    {
        $request = PurchaseRequests::where('id', $id)->with('supplier')->first();
        return response()->json($request);
    }

    public function approve($id, Request $request)
    {
        $purchaseRequest = PurchaseRequests::where('id', $id)->with('supplier','material')->first();
        $existingOrder = PurchaseOrder::where('supplier_id', $purchaseRequest->supplier_id)
        ->where('material_id', $purchaseRequest->material_id)
        ->first();

        if ($existingOrder) {
            return response()->json(['message' => 'Đơn hàng đã tồn tại!'], 400);
        }
        $order = PurchaseOrder::create([
            'supplier_id' => $purchaseRequest->supplier_id,
            'material_id' => $purchaseRequest->material_id,
            'type' => $purchaseRequest->type,
            'quantity' => $purchaseRequest->quantity,
            'unit_id' => $purchaseRequest->unit_id,
            'price_per_unit' => $purchaseRequest->price_per_unit,
            'total_price' =>$purchaseRequest->total_price,
            'order_date' => now(),
            'expected_delivery_date' => now()->addDays($purchaseRequest->delivery_time),
            'status' => 'ordered',
        ]);


        $purchaseRequest->update(['status' => 'approved']);
        if ($request->send_email) {
            Mail::to($purchaseRequest->supplier->contact_info)->send(new PurchaseOrderMail($order));
        }
        return response()->json($purchaseRequest->supplier->contact_info);
    }

    public function reject(Request $request, $id)
    {
        $purchaseRequest = PurchaseRequests::findOrFail($id);
        $purchaseRequest->update(['status' => 'rejected']);

        return response()->json(['message' => 'Đã từ chối đề xuất!']);
    }
    public function deleteNotification($id)
    {
        $request = PurchaseRequests::findOrFail($id);
        if ($request->status !== 'pending') {
            $request->delete();
            return response()->json(['message' => 'Thông báo đã được xóa!']);
        }
        return response()->json(['message' => 'Không thể xóa đơn hàng chưa xử lý!'], 400);
    }

}
