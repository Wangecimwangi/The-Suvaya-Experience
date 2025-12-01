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
    $type = isset($_GET['type']) ? $_GET['type'] : null;
    $upcoming = isset($_GET['upcoming']) ? $_GET['upcoming'] : false;

    if ($id) {
        // Get single event
        $query = "SELECT * FROM events WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $event = $stmt->fetch();

        if ($event) {
            sendResponse(200, 'Event retrieved successfully', $event);
        } else {
            sendError(404, 'Event not found');
        }
    } else {
        // Get all events
        $query = "SELECT * FROM events WHERE 1=1";

        if ($upcoming) {
            $query .= " AND date >= CURDATE()";
        }

        if ($type) {
            $query .= " AND event_type = :type";
        }

        $query .= " ORDER BY date ASC, time ASC";

        $stmt = $db->prepare($query);

        if ($type) {
            $stmt->bindParam(':type', $type);
        }

        $stmt->execute();
        $events = $stmt->fetchAll();

        sendResponse(200, 'Events retrieved successfully', $events);
    }

} catch (Exception $e) {
    sendError(500, 'Failed to retrieve events: ' . $e->getMessage());
}
?>
