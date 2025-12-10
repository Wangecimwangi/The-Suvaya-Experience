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
if (empty($data->id)) {
    sendError(400, 'Menu item ID is required');
}

try {
    // Check if menu item exists
    $checkQuery = "SELECT id, name FROM menu_items WHERE id = :id";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':id', $data->id);
    $checkStmt->execute();

    $menuItem = $checkStmt->fetch();

    if (!$menuItem) {
        sendError(404, 'Menu item not found');
    }

    // Delete menu item (CASCADE will delete related packages and classes)
    $query = "DELETE FROM menu_items WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $data->id);
    $stmt->execute();

    sendResponse(200, 'Menu item deleted successfully', [
        'deleted_item' => $menuItem['name']
    ]);

} catch (Exception $e) {
    sendError(500, 'Failed to delete menu item: ' . $e->getMessage());
}
?>
