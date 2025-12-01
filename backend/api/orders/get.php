<?php
require_once '../../config/database.php';
require_once '../../utils/cors.php';
require_once '../../utils/helpers.php';

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendError(405, 'Method not allowed');
}

try {
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    $order_number = isset($_GET['order_number']) ? $_GET['order_number'] : null;

    if ($id || $order_number) {
        // Get single order with items
        $query = "SELECT * FROM orders WHERE ";
        $query .= $id ? "id = :id" : "order_number = :order_number";

        $stmt = $db->prepare($query);
        if ($id) {
            $stmt->bindParam(':id', $id);
        } else {
            $stmt->bindParam(':order_number', $order_number);
        }
        $stmt->execute();

        $order = $stmt->fetch();

        if ($order) {
            // Get order items
            $itemsQuery = "SELECT * FROM order_items WHERE order_id = :order_id";
            $itemsStmt = $db->prepare($itemsQuery);
            $itemsStmt->bindParam(':order_id', $order['id']);
            $itemsStmt->execute();
            $order['items'] = $itemsStmt->fetchAll();

            sendResponse(200, 'Order retrieved successfully', $order);
        } else {
            sendError(404, 'Order not found');
        }
    } else {
        // Get all orders
        $query = "SELECT * FROM orders ORDER BY created_at DESC";
        $stmt = $db->prepare($query);
        $stmt->execute();

        $orders = $stmt->fetchAll();
        sendResponse(200, 'Orders retrieved successfully', $orders);
    }

} catch (Exception $e) {
    sendError(500, 'Failed to retrieve orders: ' . $e->getMessage());
}
?>
