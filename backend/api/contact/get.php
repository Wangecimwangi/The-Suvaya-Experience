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
        // Get single contact message
        $query = "SELECT * FROM contact_messages WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $message = $stmt->fetch();

        if ($message) {
            sendResponse(200, 'Contact message retrieved successfully', $message);
        } else {
            sendError(404, 'Contact message not found');
        }
    } else {
        // Get all contact messages, ordered by newest first
        $query = "SELECT * FROM contact_messages ORDER BY created_at DESC";
        $stmt = $db->prepare($query);
        $stmt->execute();

        $messages = $stmt->fetchAll();
        sendResponse(200, 'Contact messages retrieved successfully', $messages);
    }

} catch (Exception $e) {
    sendError(500, 'Failed to retrieve contact messages: ' . $e->getMessage());
}
?>
