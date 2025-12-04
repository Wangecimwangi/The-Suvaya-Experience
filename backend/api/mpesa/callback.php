<?php
/**
 * M-Pesa Callback Handler
 * Receives payment confirmation from M-Pesa
 */

require_once '../../config/database.php';
require_once '../../utils/helpers.php';

// Get callback data from M-Pesa
$callbackData = file_get_contents('php://input');

// Log the raw callback for debugging
file_put_contents(
    '../../logs/mpesa_callback_' . date('Y-m-d') . '.log',
    date('Y-m-d H:i:s') . " - " . $callbackData . "\n",
    FILE_APPEND
);

try {
    $database = new Database();
    $db = $database->getConnection();

    $data = json_decode($callbackData);

    if (!$data) {
        throw new Exception('Invalid callback data');
    }

    // Extract callback details
    $stkCallback = $data->Body->stkCallback;
    $merchantRequestID = $stkCallback->MerchantRequestID;
    $checkoutRequestID = $stkCallback->CheckoutRequestID;
    $resultCode = $stkCallback->ResultCode;
    $resultDesc = $stkCallback->ResultDesc;

    // Initialize variables
    $mpesaReceiptNumber = null;
    $transactionDate = null;
    $phoneNumber = null;
    $amount = null;

    // If payment was successful, extract additional details
    if ($resultCode == 0 && isset($stkCallback->CallbackMetadata)) {
        $metadata = $stkCallback->CallbackMetadata->Item;

        foreach ($metadata as $item) {
            switch ($item->Name) {
                case 'Amount':
                    $amount = $item->Value;
                    break;
                case 'MpesaReceiptNumber':
                    $mpesaReceiptNumber = $item->Value;
                    break;
                case 'TransactionDate':
                    $transactionDate = $item->Value;
                    // Convert from format: 20231201143045 to Y-m-d H:i:s
                    $transactionDate = date('Y-m-d H:i:s', strtotime($transactionDate));
                    break;
                case 'PhoneNumber':
                    $phoneNumber = $item->Value;
                    break;
            }
        }
    }

    // Determine status
    $status = ($resultCode == 0) ? 'completed' : 'failed';

    // Update transaction in database
    $query = "UPDATE mpesa_transactions SET
              result_code = :result_code,
              result_desc = :result_desc,
              mpesa_receipt_number = :mpesa_receipt_number,
              transaction_date = :transaction_date,
              status = :status,
              updated_at = NOW()
              WHERE checkout_request_id = :checkout_request_id";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':result_code', $resultCode);
    $stmt->bindParam(':result_desc', $resultDesc);
    $stmt->bindParam(':mpesa_receipt_number', $mpesaReceiptNumber);
    $stmt->bindParam(':transaction_date', $transactionDate);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':checkout_request_id', $checkoutRequestID);
    $stmt->execute();

    // If payment was successful, update related order/reservation
    if ($resultCode == 0) {
        // Get the transaction details to find related reference
        $query = "SELECT account_reference, amount FROM mpesa_transactions
                  WHERE checkout_request_id = :checkout_request_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':checkout_request_id', $checkoutRequestID);
        $stmt->execute();
        $transaction = $stmt->fetch();

        if ($transaction) {
            // Check if it's a reservation payment (format: RES-{reservation_id})
            if (strpos($transaction['account_reference'], 'RES-') === 0) {
                $reservationId = str_replace('RES-', '', $transaction['account_reference']);

                // Update reservation as paid
                $query = "UPDATE reservations SET
                          deposit_paid = TRUE,
                          deposit_amount = :amount,
                          payment_method = 'mpesa',
                          mpesa_receipt = :receipt,
                          status = 'confirmed',
                          updated_at = NOW()
                          WHERE id = :id";

                $stmt = $db->prepare($query);
                $stmt->bindParam(':amount', $transaction['amount']);
                $stmt->bindParam(':receipt', $mpesaReceiptNumber);
                $stmt->bindParam(':id', $reservationId);
                $stmt->execute();
            }
            // Check if it's an order payment (format: ORD-{order_number})
            elseif (strpos($transaction['account_reference'], 'ORD-') === 0) {
                $orderNumber = str_replace('ORD-', '', $transaction['account_reference']);

                // Update order as paid
                $query = "UPDATE orders SET
                          payment_status = 'paid',
                          payment_method = 'mpesa',
                          mpesa_receipt = :receipt,
                          status = 'confirmed',
                          updated_at = NOW()
                          WHERE order_number = :order_number";

                $stmt = $db->prepare($query);
                $stmt->bindParam(':receipt', $mpesaReceiptNumber);
                $stmt->bindParam(':order_number', $orderNumber);
                $stmt->execute();
            }
        }
    }

    // Send success response to M-Pesa
    header('Content-Type: application/json');
    echo json_encode([
        'ResultCode' => 0,
        'ResultDesc' => 'Callback received successfully'
    ]);

} catch (Exception $e) {
    // Log error
    file_put_contents(
        '../../logs/mpesa_callback_errors_' . date('Y-m-d') . '.log',
        date('Y-m-d H:i:s') . " - " . $e->getMessage() . "\n",
        FILE_APPEND
    );

    // Still send success to M-Pesa to avoid retries
    header('Content-Type: application/json');
    echo json_encode([
        'ResultCode' => 0,
        'ResultDesc' => 'Callback received'
    ]);
}
?>
