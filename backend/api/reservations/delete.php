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
    sendError(400, 'Reservation ID is required');
}

try {
    // Check if reservation exists
    $checkQuery = "SELECT id FROM reservations WHERE id = :id";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':id', $data->id);
    $checkStmt->execute();

    if ($checkStmt->rowCount() === 0) {
        sendError(404, 'Reservation not found');
    }

    // Delete reservation
    $deleteQuery = "DELETE FROM reservations WHERE id = :id";
    $deleteStmt = $db->prepare($deleteQuery);
    $deleteStmt->bindParam(':id', $data->id);
    $deleteStmt->execute();

    sendResponse(200, 'Reservation deleted successfully', [
        'reservation_id' => $data->id
    ]);

} catch (Exception $e) {
    sendError(500, 'Failed to delete reservation: ' . $e->getMessage());
}
?>
