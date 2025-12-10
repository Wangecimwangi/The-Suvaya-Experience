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

if (empty($data->id)) {
    sendError(400, 'Order ID is required');
}

try {
    // Get current order
    $getQuery = "SELECT * FROM orders WHERE id = :id";
    $getStmt = $db->prepare($getQuery);
    $getStmt->bindParam(':id', $data->id);
    $getStmt->execute();
    $currentOrder = $getStmt->fetch();

    if (!$currentOrder) {
        sendError(404, 'Order not found');
    }

    if ($currentOrder['status'] === 'cancelled') {
        sendError(400, 'Cannot update cancelled order');
    }

    if ($currentOrder['status'] === 'completed' || $currentOrder['status'] === 'delivered') {
        sendError(400, 'Cannot update completed or delivered order');
    }

    // Build update query dynamically based on provided fields
    $updateFields = [];
    $params = [':id' => $data->id];

    if (isset($data->delivery_address)) {
        $updateFields[] = "delivery_address = :delivery_address";
        $params[':delivery_address'] = $data->delivery_address;
    }
    if (isset($data->delivery_date)) {
        $updateFields[] = "delivery_date = :delivery_date";
        $params[':delivery_date'] = $data->delivery_date;
    }
    if (isset($data->delivery_time)) {
        $updateFields[] = "delivery_time = :delivery_time";
        $params[':delivery_time'] = $data->delivery_time;
    }
    if (isset($data->notes)) {
        $updateFields[] = "notes = :notes";
        $params[':notes'] = $data->notes;
    }

    if (empty($updateFields)) {
        sendError(400, 'No fields to update');
    }

    // Update order
    $updateQuery = "UPDATE orders SET " . implode(', ', $updateFields) . " WHERE id = :id";
    $updateStmt = $db->prepare($updateQuery);
    foreach ($params as $key => $value) {
        $updateStmt->bindValue($key, $value);
    }
    $updateStmt->execute();

    // Get updated order
    $getStmt->execute();
    $updatedOrder = $getStmt->fetch();

    sendResponse(200, 'Order updated successfully', [
        'order' => $updatedOrder
    ]);

} catch (Exception $e) {
    sendError(500, 'Failed to update order: ' . $e->getMessage());
}
?>
