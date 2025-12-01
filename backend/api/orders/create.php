<?php
require_once '../../config/database.php';
require_once '../../utils/cors.php';
require_once '../../utils/helpers.php';

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError(405, 'Method not allowed');
}

$data = json_decode(file_get_contents("php://input"));

// Validate required fields
if (empty($data->name) || empty($data->email) || empty($data->phone) ||
    empty($data->items) || empty($data->total_amount)) {
    sendError(400, 'Missing required fields');
}

try {
    $db->beginTransaction();

    // Generate order number
    $order_number = generateOrderNumber();

    // Calculate deposit (50%)
    $deposit_amount = $data->total_amount * 0.5;
    $balance_due = $data->total_amount - $deposit_amount;

    // Insert order
    $query = "INSERT INTO orders
              (order_number, name, email, phone, total_amount, deposit_amount,
               balance_due, status, delivery_date, delivery_address, notes)
              VALUES
              (:order_number, :name, :email, :phone, :total_amount, :deposit_amount,
               :balance_due, 'pending', :delivery_date, :delivery_address, :notes)";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':order_number', $order_number);
    $stmt->bindParam(':name', $data->name);
    $stmt->bindParam(':email', $data->email);
    $stmt->bindParam(':phone', $data->phone);
    $stmt->bindParam(':total_amount', $data->total_amount);
    $stmt->bindParam(':deposit_amount', $deposit_amount);
    $stmt->bindParam(':balance_due', $balance_due);
    $delivery_date = $data->delivery_date ?? null;
    $stmt->bindParam(':delivery_date', $delivery_date);
    $delivery_address = $data->delivery_address ?? '';
    $stmt->bindParam(':delivery_address', $delivery_address);
    $notes = $data->notes ?? '';
    $stmt->bindParam(':notes', $notes);

    $stmt->execute();
    $order_id = $db->lastInsertId();

    // Insert order items
    $itemQuery = "INSERT INTO order_items
                  (order_id, menu_item_id, item_name, quantity, price, subtotal, notes)
                  VALUES
                  (:order_id, :menu_item_id, :item_name, :quantity, :price, :subtotal, :notes)";
    $itemStmt = $db->prepare($itemQuery);

    foreach ($data->items as $item) {
        $itemStmt->bindParam(':order_id', $order_id);
        $menu_item_id = $item->menu_item_id ?? null;
        $itemStmt->bindParam(':menu_item_id', $menu_item_id);
        $itemStmt->bindParam(':item_name', $item->item_name);
        $itemStmt->bindParam(':quantity', $item->quantity);
        $itemStmt->bindParam(':price', $item->price);
        $itemStmt->bindParam(':subtotal', $item->subtotal);
        $item_notes = $item->notes ?? '';
        $itemStmt->bindParam(':notes', $item_notes);
        $itemStmt->execute();
    }

    $db->commit();

    sendResponse(201, 'Order created successfully', [
        'order_id' => $order_id,
        'order_number' => $order_number,
        'deposit_amount' => $deposit_amount,
        'balance_due' => $balance_due
    ]);

} catch (Exception $e) {
    $db->rollBack();
    sendError(500, 'Failed to create order: ' . $e->getMessage());
}
?>
