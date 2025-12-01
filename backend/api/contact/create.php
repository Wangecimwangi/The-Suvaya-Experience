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
if (empty($data->name) || empty($data->email) || empty($data->message)) {
    sendError(400, 'Name, email, and message are required');
}

// Validate email
if (!validateEmail($data->email)) {
    sendError(400, 'Invalid email format');
}

try {
    $query = "INSERT INTO contact_messages (name, email, phone, subject, message, status)
              VALUES (:name, :email, :phone, :subject, :message, 'new')";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':name', $data->name);
    $stmt->bindParam(':email', $data->email);
    $phone = $data->phone ?? '';
    $stmt->bindParam(':phone', $phone);
    $subject = $data->subject ?? '';
    $stmt->bindParam(':subject', $subject);
    $stmt->bindParam(':message', $data->message);

    $stmt->execute();
    $message_id = $db->lastInsertId();

    sendResponse(201, 'Message sent successfully', [
        'message_id' => $message_id
    ]);

} catch (Exception $e) {
    sendError(500, 'Failed to send message: ' . $e->getMessage());
}
?>
