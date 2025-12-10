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
    // Get order details first
    $getQuery = "SELECT id, status FROM orders WHERE id = :id";
    $getStmt = $db->prepare($getQuery);
    $getStmt->bindParam(':id', $data->id);
    $getStmt->execute();
    $order = $getStmt->fetch();

    if (!$order) {
        sendError(404, 'Order not found');
    }

    if ($order['status'] === 'cancelled') {
        sendError(400, 'Order is already cancelled');
    }

    if ($order['status'] === 'completed' || $order['status'] === 'delivered') {
        sendError(400, 'Cannot cancel completed or delivered order');
    }

    // Update order status to cancelled
    $updateQuery = "UPDATE orders SET status = 'cancelled' WHERE id = :id";
    $updateStmt = $db->prepare($updateQuery);
    $updateStmt->bindParam(':id', $data->id);
    $updateStmt->execute();

    sendResponse(200, 'Order cancelled successfully', [
        'order_id' => $data->id
    ]);

} catch (Exception $e) {
    sendError(500, 'Failed to cancel order: ' . $e->getMessage());
}
?>
