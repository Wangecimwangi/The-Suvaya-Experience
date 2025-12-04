<?php
require_once '../../config/database.php';
require_once '../../utils/cors.php';
require_once '../../utils/helpers.php';
require_once '../../utils/email.php';

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError(405, 'Method not allowed');
}

$data = json_decode(file_get_contents("php://input"));

// Validate required fields
if (empty($data->name) || empty($data->email) || empty($data->phone) ||
    empty($data->date) || empty($data->time) || empty($data->guests)) {
    sendError(400, 'Missing required fields');
}

// Validate email
if (!validateEmail($data->email)) {
    sendError(400, 'Invalid email format');
}

try {
    // Check if date is already booked
    $checkQuery = "SELECT id FROM bookings WHERE date = :date AND booked = TRUE";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':date', $data->date);
    $checkStmt->execute();

    if ($checkStmt->rowCount() > 0) {
        sendError(409, 'This date is already booked');
    }

    // Start transaction
    $db->beginTransaction();

    // Insert reservation
    $query = "INSERT INTO reservations
              (name, email, phone, date, time, guests, notes, status, deposit_paid)
              VALUES
              (:name, :email, :phone, :date, :time, :guests, :notes, 'pending', FALSE)";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':name', $data->name);
    $stmt->bindParam(':email', $data->email);
    $stmt->bindParam(':phone', $data->phone);
    $stmt->bindParam(':date', $data->date);
    $stmt->bindParam(':time', $data->time);
    $stmt->bindParam(':guests', $data->guests);
    $notes = $data->notes ?? '';
    $stmt->bindParam(':notes', $notes);

    $stmt->execute();
    $reservation_id = $db->lastInsertId();

    // Mark date as booked
    $bookingQuery = "INSERT INTO bookings (date, booked, booking_type, reference_id)
                     VALUES (:date, TRUE, 'reservation', :reference_id)";
    $bookingStmt = $db->prepare($bookingQuery);
    $bookingStmt->bindParam(':date', $data->date);
    $bookingStmt->bindParam(':reference_id', $reservation_id);
    $bookingStmt->execute();

    // Commit transaction
    $db->commit();

    // Send reservation confirmation email
    try {
        $emailService = getEmailService();
        $emailData = [
            'name' => $data->name,
            'email' => $data->email,
            'phone' => $data->phone,
            'date' => $data->date,
            'time' => $data->time,
            'guests' => $data->guests,
            'notes' => $data->notes ?? ''
        ];

        $emailService->sendReservationConfirmation($data->email, $emailData);
    } catch (Exception $emailError) {
        // Log email error but don't fail the reservation
        error_log('Failed to send reservation confirmation email: ' . $emailError->getMessage());
    }

    sendResponse(201, 'Reservation created successfully', [
        'reservation_id' => $reservation_id,
        'date' => $data->date,
        'time' => $data->time
    ]);

} catch (Exception $e) {
    $db->rollBack();
    sendError(500, 'Failed to create reservation: ' . $e->getMessage());
}
?>
