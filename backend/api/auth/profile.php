<?php
require_once '../../config/database.php';
require_once '../../utils/cors.php';
require_once '../../utils/helpers.php';

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendError(405, 'Method not allowed');
}

$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;

if (!$user_id) {
    sendError(400, 'User ID is required');
}

try {
    // Get user profile
    $query = "SELECT id, name, email, phone, is_admin, created_at FROM users WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $user_id);
    $stmt->execute();

    $user = $stmt->fetch();

    if (!$user) {
        sendError(404, 'User not found');
    }

    sendResponse(200, 'Profile retrieved successfully', $user);

} catch (Exception $e) {
    sendError(500, 'Failed to retrieve profile: ' . $e->getMessage());
}
?>
