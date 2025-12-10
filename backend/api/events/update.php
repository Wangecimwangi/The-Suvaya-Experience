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
if (empty($data->id) || empty($data->title) || empty($data->date) || empty($data->time)) {
    sendError(400, 'Missing required fields: id, title, date, and time are required');
}

try {
    // Update event
    $query = "UPDATE events SET
              title = :title,
              date = :date,
              time = :time,
              location = :location,
              description = :description,
              price = :price,
              image = :image,
              spots_available = :spots_available,
              event_type = :event_type
              WHERE id = :id";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $data->id);
    $stmt->bindParam(':title', $data->title);
    $stmt->bindParam(':date', $data->date);
    $stmt->bindParam(':time', $data->time);
    $location = $data->location ?? null;
    $stmt->bindParam(':location', $location);
    $description = $data->description ?? null;
    $stmt->bindParam(':description', $description);
    $price = $data->price ?? null;
    $stmt->bindParam(':price', $price);
    $image = $data->image ?? null;
    $stmt->bindParam(':image', $image);
    $spots_available = $data->spots_available ?? 10;
    $stmt->bindParam(':spots_available', $spots_available);
    $event_type = $data->event_type ?? null;
    $stmt->bindParam(':event_type', $event_type);

    $stmt->execute();

    // Check if update was successful
    if ($stmt->rowCount() === 0) {
        // Check if event exists
        $checkQuery = "SELECT id FROM events WHERE id = :id";
        $checkStmt = $db->prepare($checkQuery);
        $checkStmt->bindParam(':id', $data->id);
        $checkStmt->execute();

        if ($checkStmt->rowCount() === 0) {
            sendError(404, 'Event not found');
        }
    }

    sendResponse(200, 'Event updated successfully', [
        'event_id' => $data->id
    ]);

} catch (Exception $e) {
    sendError(500, 'Failed to update event: ' . $e->getMessage());
}
?>
