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

if (empty($data->id)) {
    sendError(400, 'Reservation ID is required');
}

try {
    $db->beginTransaction();

    // Get current reservation
    $getQuery = "SELECT * FROM reservations WHERE id = :id";
    $getStmt = $db->prepare($getQuery);
    $getStmt->bindParam(':id', $data->id);
    $getStmt->execute();
    $currentReservation = $getStmt->fetch();

    if (!$currentReservation) {
        sendError(404, 'Reservation not found');
    }

    if ($currentReservation['status'] === 'cancelled') {
        sendError(400, 'Cannot update cancelled reservation');
    }

    // Build update query dynamically based on provided fields
    $updateFields = [];
    $params = [':id' => $data->id];

    if (isset($data->name)) {
        $updateFields[] = "name = :name";
        $params[':name'] = $data->name;
    }
    if (isset($data->phone)) {
        $updateFields[] = "phone = :phone";
        $params[':phone'] = $data->phone;
    }
    if (isset($data->date)) {
        // Check if new date is available
        if ($data->date !== $currentReservation['date']) {
            $checkQuery = "SELECT id FROM bookings WHERE date = :date AND booked = TRUE AND (booking_type != 'reservation' OR reference_id != :ref_id)";
            $checkStmt = $db->prepare($checkQuery);
            $checkStmt->bindParam(':date', $data->date);
            $checkStmt->bindParam(':ref_id', $data->id);
            $checkStmt->execute();

            if ($checkStmt->rowCount() > 0) {
                sendError(409, 'The new date is already booked');
            }

            // Update bookings table
            $updateBookingQuery = "UPDATE bookings SET date = :new_date WHERE reference_id = :ref_id AND booking_type = 'reservation'";
            $updateBookingStmt = $db->prepare($updateBookingQuery);
            $updateBookingStmt->bindParam(':new_date', $data->date);
            $updateBookingStmt->bindParam(':ref_id', $data->id);
            $updateBookingStmt->execute();
        }

        $updateFields[] = "date = :date";
        $params[':date'] = $data->date;
    }
    if (isset($data->time)) {
        $updateFields[] = "time = :time";
        $params[':time'] = $data->time;
    }
    if (isset($data->guests)) {
        $updateFields[] = "guests = :guests";
        $params[':guests'] = $data->guests;
    }
    if (isset($data->notes)) {
        $updateFields[] = "notes = :notes";
        $params[':notes'] = $data->notes;
    }

    if (empty($updateFields)) {
        sendError(400, 'No fields to update');
    }

    // Update reservation
    $updateQuery = "UPDATE reservations SET " . implode(', ', $updateFields) . " WHERE id = :id";
    $updateStmt = $db->prepare($updateQuery);
    foreach ($params as $key => $value) {
        $updateStmt->bindValue($key, $value);
    }
    $updateStmt->execute();

    $db->commit();

    // Get updated reservation
    $getStmt->execute();
    $updatedReservation = $getStmt->fetch();

    sendResponse(200, 'Reservation updated successfully', [
        'reservation' => $updatedReservation
    ]);

} catch (Exception $e) {
    $db->rollBack();
    sendError(500, 'Failed to update reservation: ' . $e->getMessage());
}
?>
