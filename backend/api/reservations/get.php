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

    if ($id) {
        // Get single reservation
        $query = "SELECT * FROM reservations WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $reservation = $stmt->fetch();

        if ($reservation) {
            sendResponse(200, 'Reservation retrieved successfully', $reservation);
        } else {
            sendError(404, 'Reservation not found');
        }
    } else {
        // Get all reservations
        $query = "SELECT * FROM reservations ORDER BY date DESC, time DESC";
        $stmt = $db->prepare($query);
        $stmt->execute();

        $reservations = $stmt->fetchAll();
        sendResponse(200, 'Reservations retrieved successfully', $reservations);
    }

} catch (Exception $e) {
    sendError(500, 'Failed to retrieve reservations: ' . $e->getMessage());
}
?>
