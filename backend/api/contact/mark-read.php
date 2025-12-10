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
    sendError(400, 'Message ID is required');
}

try {
    // Update message status to 'read'
    $query = "UPDATE contact_messages SET status = 'read' WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $data->id);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        // Check if message exists
        $checkQuery = "SELECT id FROM contact_messages WHERE id = :id";
        $checkStmt = $db->prepare($checkQuery);
        $checkStmt->bindParam(':id', $data->id);
        $checkStmt->execute();

        if ($checkStmt->rowCount() === 0) {
            sendError(404, 'Contact message not found');
        }
    }

    sendResponse(200, 'Message marked as read', [
        'message_id' => $data->id
    ]);

} catch (Exception $e) {
    sendError(500, 'Failed to mark message as read: ' . $e->getMessage());
}
?>
