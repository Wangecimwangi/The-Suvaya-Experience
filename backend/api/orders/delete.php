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
    // Get order to check if it exists and its status
    $getQuery = "SELECT id, status FROM orders WHERE id = :id";
    $getStmt = $db->prepare($getQuery);
    $getStmt->bindParam(':id', $data->id);
    $getStmt->execute();
    $order = $getStmt->fetch();

    if (!$order) {
        sendError(404, 'Order not found');
    }

    // Only allow deletion of cancelled orders
    if ($order['status'] !== 'cancelled') {
        sendError(400, 'Only cancelled orders can be deleted');
    }

    // Delete order (order_items will be automatically deleted due to CASCADE)
    $deleteQuery = "DELETE FROM orders WHERE id = :id";
    $deleteStmt = $db->prepare($deleteQuery);
    $deleteStmt->bindParam(':id', $data->id);
    $deleteStmt->execute();

    sendResponse(200, 'Order deleted successfully', [
        'order_id' => $data->id
    ]);

} catch (Exception $e) {
    sendError(500, 'Failed to delete order: ' . $e->getMessage());
}
?>
