<?php
require_once '../../config/database.php';
require_once '../../utils/cors.php';
require_once '../../utils/helpers.php';

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendError(405, 'Method not allowed');
}

// Get reservation ID from query parameter
$reservationId = $_GET['id'] ?? null;

if (empty($reservationId)) {
    sendError(400, 'Reservation ID is required');
}

try {
    // Get reservation details
    $query = "SELECT
                r.id,
                r.name,
                r.email,
                r.phone,
                r.date,
                r.time,
                r.guests,
                r.notes,
                r.status,
                r.created_at,
                u.name as user_name
              FROM reservations r
              LEFT JOIN users u ON r.user_id = u.id
              WHERE r.id = :id";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $reservationId);
    $stmt->execute();

    $reservation = $stmt->fetch();

    if (!$reservation) {
        sendError(404, 'Reservation not found');
    }

    // Format the receipt data
    $receipt = [
        'id' => $reservation['id'],
        'name' => $reservation['name'],
        'email' => $reservation['email'],
        'phone' => $reservation['phone'],
        'date' => $reservation['date'],
        'time' => $reservation['time'],
        'guests' => $reservation['guests'],
        'notes' => $reservation['notes'] ?? '',
        'status' => $reservation['status'],
        'created_at' => $reservation['created_at'],
        'confirmation_number' => 'RES-' . str_pad($reservation['id'], 6, '0', STR_PAD_LEFT)
    ];

    sendResponse(200, 'Receipt retrieved successfully', [
        'receipt' => $receipt
    ]);

} catch (Exception $e) {
    sendError(500, 'Failed to retrieve receipt: ' . $e->getMessage());
}
?>
