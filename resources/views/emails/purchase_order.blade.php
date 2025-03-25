<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đơn đặt hàng</title>
</head>
<body>
    <h2>Đơn đặt hàng từ công ty</h2>

    <p>Chào {{ $order->supplier->name ?? 'Nhà cung cấp không xác định' }},</p>

    <p>Chúng tôi xin đặt hàng với thông tin sau:</p>
    <ul>
        <li><strong>Nguyên vật liệu:</strong> {{ $order->material_id ?? 'Không có thông tin' }}</li>
        <li><strong>Số lượng:</strong> {{ $order->quantity ?? 0 }} {{ $order->unit->name ?? '' }}</li>
        <li><strong>Giá:</strong> {{ $order->price_per_unit ?? 0 }} / {{ $order->unit->name ?? '' }}</li>
        <li><strong>Tổng tiền:</strong> {{ $order->total_price ?? 0 }}</li>
        <li><strong>Ngày đặt:</strong> {{ $order->order_date ?? 'Không có thông tin' }}</li>
        <li><strong>Ngày giao dự kiến:</strong> {{ $order->expected_delivery_date ?? 'Không có thông tin' }}</li>
    </ul>

    <p>Vui lòng xác nhận đơn hàng này.</p>
    <p>Trân trọng,<br> Công ty XYZ</p>
</body>
</html>
