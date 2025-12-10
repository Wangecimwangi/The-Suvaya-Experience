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
    sendError(400, 'Event ID is required');
}

try {
    // Check if event exists before deleting
    $checkQuery = "SELECT id, title FROM events WHERE id = :id";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':id', $data->id);
    $checkStmt->execute();

    $event = $checkStmt->fetch();

    if (!$event) {
        sendError(404, 'Event not found');
    }

    // Delete event
    $query = "DELETE FROM events WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $data->id);
    $stmt->execute();

    sendResponse(200, 'Event deleted successfully', [
        'deleted_event' => $event['title']
    ]);

} catch (Exception $e) {
    sendError(500, 'Failed to delete event: ' . $e->getMessage());
}
?>
