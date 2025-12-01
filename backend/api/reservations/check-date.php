<?php
require_once '../../config/database.php';
require_once '../../utils/cors.php';
require_once '../../utils/helpers.php';

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendError(405, 'Method not allowed');
}

$date = isset($_GET['date']) ? $_GET['date'] : null;

if (!$date) {
    sendError(400, 'Date parameter is required');
}

try {
    $query = "SELECT id, booked, booking_type FROM bookings WHERE date = :date";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':date', $date);
    $stmt->execute();

    $booking = $stmt->fetch();

    sendResponse(200, 'Date checked successfully', [
        'date' => $date,
        'is_booked' => $booking ? true : false,
        'booking_type' => $booking['booking_type'] ?? null
    ]);

} catch (Exception $e) {
    sendError(500, 'Failed to check date: ' . $e->getMessage());
}
?>
