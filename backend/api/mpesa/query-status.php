<?php
/**
 * M-Pesa Transaction Status Query
 * Check the status of a payment transaction
 */

require_once '../../config/database.php';
require_once '../../config/mpesa.php';
require_once '../../utils/cors.php';
require_once '../../utils/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendError(405, 'Method not allowed');
}

$checkoutRequestID = $_GET['checkout_request_id'] ?? null;

if (!$checkoutRequestID) {
    sendError(400, 'Checkout request ID is required');
}

try {
    $mpesa = new MpesaConfig();
    $database = new Database();
    $db = $database->getConnection();

    // First check our database
    $query = "SELECT * FROM mpesa_transactions
              WHERE checkout_request_id = :checkout_request_id";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':checkout_request_id', $checkoutRequestID);
    $stmt->execute();

    $transaction = $stmt->fetch();

    if (!$transaction) {
        sendError(404, 'Transaction not found');
    }

    // If transaction is still pending, query M-Pesa API
    if ($transaction['status'] === 'pending') {
        try {
            $accessToken = $mpesa->getAccessToken();
            $timestamp = $mpesa->getTimestamp();
            $password = $mpesa->generatePassword($timestamp);

            $queryUrl = $mpesa->getBaseUrl() . '/mpesa/stkpushquery/v1/query';

            $requestData = [
                'BusinessShortCode' => $mpesa->getShortcode(),
                'Password' => $password,
                'Timestamp' => $timestamp,
                'CheckoutRequestID' => $checkoutRequestID
            ];

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $queryUrl);
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json'
            ]);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($requestData));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);
            curl_close($curl);

            $result = json_decode($response);

            // Update transaction status based on query result
            if (isset($result->ResultCode)) {
                $status = ($result->ResultCode == '0') ? 'completed' : 'failed';

                $updateQuery = "UPDATE mpesa_transactions SET
                                result_code = :result_code,
                                result_desc = :result_desc,
                                status = :status,
                                updated_at = NOW()
                                WHERE checkout_request_id = :checkout_request_id";

                $updateStmt = $db->prepare($updateQuery);
                $updateStmt->bindParam(':result_code', $result->ResultCode);
                $updateStmt->bindParam(':result_desc', $result->ResultDesc);
                $updateStmt->bindParam(':status', $status);
                $updateStmt->bindParam(':checkout_request_id', $checkoutRequestID);
                $updateStmt->execute();

                // Fetch updated transaction
                $stmt->execute();
                $transaction = $stmt->fetch();
            }

        } catch (Exception $e) {
            // If query fails, just return current database status
            // Don't fail the request
        }
    }

    sendResponse(200, 'Transaction status retrieved', [
        'checkout_request_id' => $transaction['checkout_request_id'],
        'merchant_request_id' => $transaction['merchant_request_id'],
        'phone_number' => $transaction['phone_number'],
        'amount' => $transaction['amount'],
        'status' => $transaction['status'],
        'result_code' => $transaction['result_code'],
        'result_desc' => $transaction['result_desc'],
        'mpesa_receipt_number' => $transaction['mpesa_receipt_number'],
        'transaction_date' => $transaction['transaction_date'],
        'account_reference' => $transaction['account_reference'],
        'created_at' => $transaction['created_at']
    ]);

} catch (Exception $e) {
    sendError(500, 'Failed to query transaction status: ' . $e->getMessage());
}
?>
