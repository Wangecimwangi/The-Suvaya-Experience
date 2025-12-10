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

if (empty($data->id) || empty($data->status)) {
    sendError(400, 'Order ID and status are required');
}

// Validate status
$allowedStatuses = ['pending', 'confirmed', 'preparing', 'ready', 'delivered', 'completed', 'cancelled'];
if (!in_array($data->status, $allowedStatuses)) {
    sendError(400, 'Invalid status. Allowed values: ' . implode(', ', $allowedStatuses));
}

try {
    // Check if order exists
    $checkQuery = "SELECT id, status FROM orders WHERE id = :id";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':id', $data->id);
    $checkStmt->execute();

    $order = $checkStmt->fetch();

    if (!$order) {
        sendError(404, 'Order not found');
    }

    // Update order status
    $updateQuery = "UPDATE orders SET status = :status WHERE id = :id";
    $updateStmt = $db->prepare($updateQuery);
    $updateStmt->bindParam(':status', $data->status);
    $updateStmt->bindParam(':id', $data->id);
    $updateStmt->execute();

    sendResponse(200, 'Order status updated successfully', [
        'order_id' => $data->id,
        'old_status' => $order['status'],
        'new_status' => $data->status
    ]);

} catch (Exception $e) {
    sendError(500, 'Failed to update order status: ' . $e->getMessage());
}
?>
