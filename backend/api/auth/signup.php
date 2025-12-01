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
if (empty($data->name) || empty($data->email) || empty($data->password)) {
    sendError(400, 'Missing required fields');
}

// Validate email
if (!validateEmail($data->email)) {
    sendError(400, 'Invalid email format');
}

// Validate password length
if (strlen($data->password) < 6) {
    sendError(400, 'Password must be at least 6 characters long');
}

try {
    // Check if email already exists
    $checkQuery = "SELECT id FROM users WHERE email = :email";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':email', $data->email);
    $checkStmt->execute();

    if ($checkStmt->rowCount() > 0) {
        sendError(409, 'Email already registered');
    }

    // Hash password
    $hashed_password = password_hash($data->password, PASSWORD_DEFAULT);

    // Insert user
    $query = "INSERT INTO users (name, email, phone, password, is_admin)
              VALUES (:name, :email, :phone, :password, FALSE)";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':name', $data->name);
    $stmt->bindParam(':email', $data->email);
    $phone = $data->phone ?? '';
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':password', $hashed_password);

    $stmt->execute();
    $user_id = $db->lastInsertId();

    // Get user data (without password)
    $userQuery = "SELECT id, name, email, phone, is_admin, created_at FROM users WHERE id = :id";
    $userStmt = $db->prepare($userQuery);
    $userStmt->bindParam(':id', $user_id);
    $userStmt->execute();
    $user = $userStmt->fetch();

    sendResponse(201, 'User registered successfully', [
        'user' => $user,
        'token' => base64_encode($user['id'] . ':' . time()) // Simple token (use JWT in production)
    ]);

} catch (Exception $e) {
    sendError(500, 'Failed to register user: ' . $e->getMessage());
}
?>
