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
if (empty($data->email) || empty($data->password)) {
    sendError(400, 'Email and password are required');
}

try {
    // Get user by email
    $query = "SELECT * FROM users WHERE email = :email";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $data->email);
    $stmt->execute();

    $user = $stmt->fetch();

    if (!$user) {
        sendError(401, 'Invalid email or password');
    }

    // Verify password
    if (!password_verify($data->password, $user['password'])) {
        sendError(401, 'Invalid email or password');
    }

    // Remove password from response
    unset($user['password']);

    sendResponse(200, 'Login successful', [
        'user' => $user,
        'token' => base64_encode($user['id'] . ':' . time()) // Simple token (use JWT in production)
    ]);

} catch (Exception $e) {
    sendError(500, 'Login failed: ' . $e->getMessage());
}
?>
