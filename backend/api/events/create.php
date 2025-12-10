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
if (empty($data->title) || empty($data->date) || empty($data->time)) {
    sendError(400, 'Missing required fields: title, date, and time are required');
}

try {
    // Insert event
    $query = "INSERT INTO events
              (title, date, time, location, description, price, image, spots_available, event_type)
              VALUES
              (:title, :date, :time, :location, :description, :price, :image, :spots_available, :event_type)";

    $stmt = $db->prepare($query);
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
    $event_id = $db->lastInsertId();

    sendResponse(201, 'Event created successfully', [
        'event_id' => $event_id
    ]);

} catch (Exception $e) {
    sendError(500, 'Failed to create event: ' . $e->getMessage());
}
?>
