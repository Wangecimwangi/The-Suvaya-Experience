<?php
/**
 * M-Pesa Timeout Handler
 * Called when M-Pesa request times out
 */

require_once '../../config/database.php';

// Get timeout data
$timeoutData = file_get_contents('php://input');

// Log the timeout
file_put_contents(
    '../../logs/mpesa_timeout_' . date('Y-m-d') . '.log',
    date('Y-m-d H:i:s') . " - " . $timeoutData . "\n",
    FILE_APPEND
);

try {
    $database = new Database();
    $db = $database->getConnection();

    $data = json_decode($timeoutData);

    if ($data && isset($data->Body)) {
        $checkoutRequestID = $data->Body->stkCallback->CheckoutRequestID ?? null;

        if ($checkoutRequestID) {
            // Update transaction as timed out
            $query = "UPDATE mpesa_transactions SET
                      status = 'timeout',
                      result_desc = 'Request timed out',
                      updated_at = NOW()
                      WHERE checkout_request_id = :checkout_request_id";

            $stmt = $db->prepare($query);
            $stmt->bindParam(':checkout_request_id', $checkoutRequestID);
            $stmt->execute();
        }
    }

} catch (Exception $e) {
    file_put_contents(
        '../../logs/mpesa_timeout_errors_' . date('Y-m-d') . '.log',
        date('Y-m-d H:i:s') . " - " . $e->getMessage() . "\n",
        FILE_APPEND
    );
}

// Send success response
header('Content-Type: application/json');
echo json_encode([
    'ResultCode' => 0,
    'ResultDesc' => 'Timeout received'
]);
?>
