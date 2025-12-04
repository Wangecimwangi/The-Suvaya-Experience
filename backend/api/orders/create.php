<?php
require_once '../../config/database.php';
require_once '../../utils/cors.php';
require_once '../../utils/helpers.php';
require_once '../../utils/email.php';

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError(405, 'Method not allowed');
}

$data = json_decode(file_get_contents("php://input"));

// Validate required fields
if (empty($data->customer_name) || empty($data->customer_email) || empty($data->customer_phone) ||
    empty($data->items) || empty($data->subtotal)) {
    sendError(400, 'Missing required fields');
}

try {
    $db->beginTransaction();

    // Generate order number
    $order_number = 'ORD-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

    // Get deposit and balance amounts from request
    $deposit_amount = $data->deposit_amount ?? ($data->subtotal * 0.5);
    $balance_amount = $data->balance_amount ?? ($data->subtotal - $deposit_amount);

    // Insert order
    $query = "INSERT INTO orders
              (order_number, name, email, phone, total_amount, deposit_amount,
               balance_due, status, delivery_date, delivery_time, delivery_address,
               delivery_method, payment_method, notes)
              VALUES
              (:order_number, :name, :email, :phone, :total_amount, :deposit_amount,
               :balance_due, :status, :delivery_date, :delivery_time, :delivery_address,
               :delivery_method, :payment_method, :notes)";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':order_number', $order_number);
    $stmt->bindParam(':name', $data->customer_name);
    $stmt->bindParam(':email', $data->customer_email);
    $stmt->bindParam(':phone', $data->customer_phone);
    $stmt->bindParam(':total_amount', $data->subtotal);
    $stmt->bindParam(':deposit_amount', $deposit_amount);
    $stmt->bindParam(':balance_due', $balance_amount);
    $status = $data->status ?? 'pending';
    $stmt->bindParam(':status', $status);
    $delivery_date = $data->delivery_date ?? null;
    $stmt->bindParam(':delivery_date', $delivery_date);
    $delivery_time = $data->delivery_time ?? null;
    $stmt->bindParam(':delivery_time', $delivery_time);
    $delivery_address = $data->delivery_address ?? '';
    $stmt->bindParam(':delivery_address', $delivery_address);
    $delivery_method = $data->delivery_method ?? 'delivery';
    $stmt->bindParam(':delivery_method', $delivery_method);
    $payment_method = $data->payment_method ?? 'mpesa';
    $stmt->bindParam(':payment_method', $payment_method);
    $notes = $data->special_instructions ?? '';
    $stmt->bindParam(':notes', $notes);

    $stmt->execute();
    $order_id = $db->lastInsertId();

    // Insert order items
    $itemQuery = "INSERT INTO order_items
                  (order_id, menu_item_id, item_name, quantity, price, subtotal, notes)
                  VALUES
                  (:order_id, :menu_item_id, :item_name, :quantity, :price, :subtotal, :notes)";
    $itemStmt = $db->prepare($itemQuery);

    // Prepare items array for email
    $emailItems = [];
    foreach ($data->items as $item) {
        $itemStmt->bindParam(':order_id', $order_id);
        $menu_item_id = $item->id ?? null;
        $itemStmt->bindParam(':menu_item_id', $menu_item_id);
        $item_name = $item->name ?? '';
        $itemStmt->bindParam(':item_name', $item_name);
        $itemStmt->bindParam(':quantity', $item->quantity);
        $itemStmt->bindParam(':price', $item->price);
        $subtotal = $item->price * $item->quantity;
        $itemStmt->bindParam(':subtotal', $subtotal);
        $item_notes = $item->notes ?? '';
        $itemStmt->bindParam(':notes', $item_notes);
        $itemStmt->execute();

        // Add to email items
        $emailItems[] = [
            'name' => $item_name,
            'description' => $item->description ?? '',
            'quantity' => $item->quantity,
            'price' => $item->price
        ];
    }

    $db->commit();

    // Send order confirmation email
    try {
        $emailService = getEmailService();
        $emailData = [
            'order_id' => $order_number,
            'customer_name' => $data->customer_name,
            'customer_email' => $data->customer_email,
            'customer_phone' => $data->customer_phone,
            'items' => $emailItems,
            'subtotal' => $data->subtotal,
            'deposit_amount' => $deposit_amount,
            'balance_amount' => $balance_amount,
            'delivery_date' => $data->delivery_date ?? '',
            'delivery_time' => $data->delivery_time ?? '',
            'delivery_address' => $data->delivery_address ?? '',
            'delivery_method' => $data->delivery_method ?? 'delivery',
            'payment_method' => $data->payment_method ?? 'mpesa',
            'special_instructions' => $data->special_instructions ?? ''
        ];

        $emailService->sendOrderReceipt($data->customer_email, $emailData);
    } catch (Exception $emailError) {
        // Log email error but don't fail the order
        error_log('Failed to send order confirmation email: ' . $emailError->getMessage());
    }

    sendResponse(201, 'Order created successfully', [
        'order_id' => $order_id,
        'order_number' => $order_number,
        'deposit_amount' => $deposit_amount,
        'balance_due' => $balance_amount
    ]);

} catch (Exception $e) {
    $db->rollBack();
    sendError(500, 'Failed to create order: ' . $e->getMessage());
}
?>
