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
    $db->beginTransaction();

    // Get reservation details first
    $getQuery = "SELECT id, date, status FROM reservations WHERE id = :id";
    $getStmt = $db->prepare($getQuery);
    $getStmt->bindParam(':id', $data->id);
    $getStmt->execute();
    $reservation = $getStmt->fetch();

    if (!$reservation) {
        sendError(404, 'Reservation not found');
    }

    if ($reservation['status'] === 'cancelled') {
        sendError(400, 'Reservation is already cancelled');
    }

    // Update reservation status to cancelled
    $updateQuery = "UPDATE reservations SET status = 'cancelled' WHERE id = :id";
    $updateStmt = $db->prepare($updateQuery);
    $updateStmt->bindParam(':id', $data->id);
    $updateStmt->execute();

    // Update bookings table to unbook the date
    $bookingQuery = "UPDATE bookings SET booked = FALSE WHERE reference_id = :reference_id AND booking_type = 'reservation'";
    $bookingStmt = $db->prepare($bookingQuery);
    $bookingStmt->bindParam(':reference_id', $data->id);
    $bookingStmt->execute();

    $db->commit();

    sendResponse(200, 'Reservation cancelled successfully', [
        'reservation_id' => $data->id
    ]);

} catch (Exception $e) {
    $db->rollBack();
    sendError(500, 'Failed to cancel reservation: ' . $e->getMessage());
}
?>
