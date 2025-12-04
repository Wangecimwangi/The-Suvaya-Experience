<?php
/**
 * M-Pesa STK Push (Lipa na M-Pesa Online)
 * Initiates a payment request to customer's phone
 */

require_once '../../config/database.php';
require_once '../../config/mpesa.php';
require_once '../../utils/cors.php';
require_once '../../utils/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError(405, 'Method not allowed');
}

$data = json_decode(file_get_contents("php://input"));

// Validate input
if (empty($data->phone) || empty($data->amount)) {
    sendError(400, 'Phone number and amount are required');
}

// Sanitize phone number (remove spaces, ensure starts with 254)
$phone = preg_replace('/\s+/', '', $data->phone);
if (substr($phone, 0, 1) === '0') {
    $phone = '254' . substr($phone, 1);
} elseif (substr($phone, 0, 1) === '+') {
    $phone = substr($phone, 1);
} elseif (substr($phone, 0, 3) !== '254') {
    $phone = '254' . $phone;
}

// Validate phone number format
if (!preg_match('/^254[17]\d{8}$/', $phone)) {
    sendError(400, 'Invalid phone number format. Use format: 254712345678');
}

// Validate amount (minimum 1 KES)
$amount = (int)$data->amount;
if ($amount < 1) {
    sendError(400, 'Amount must be at least 1 KES');
}

try {
    $mpesa = new MpesaConfig();
    $database = new Database();
    $db = $database->getConnection();

    // Get access token
    $accessToken = $mpesa->getAccessToken();

    // Generate timestamp and password
    $timestamp = $mpesa->getTimestamp();
    $password = $mpesa->generatePassword($timestamp);

    // Prepare STK Push request
    $stkPushUrl = $mpesa->getBaseUrl() . '/mpesa/stkpush/v1/processrequest';

    $requestData = [
        'BusinessShortCode' => $mpesa->getShortcode(),
        'Password' => $password,
        'Timestamp' => $timestamp,
        'TransactionType' => 'CustomerPayBillOnline',
        'Amount' => $amount,
        'PartyA' => $phone,
        'PartyB' => $mpesa->getShortcode(),
        'PhoneNumber' => $phone,
        'CallBackURL' => $mpesa->getCallbackUrl(),
        'AccountReference' => $data->reference ?? 'SUVAYA' . time(),
        'TransactionDesc' => $data->description ?? 'Payment for Suvaya Experience'
    ];

    // Make API call
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $stkPushUrl);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json'
    ]);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($requestData));
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($curl);
    $error = curl_error($curl);
    curl_close($curl);

    if ($error) {
        throw new Exception('M-Pesa API request failed: ' . $error);
    }

    $result = json_decode($response);

    // Check if request was successful
    if (!isset($result->ResponseCode) || $result->ResponseCode !== '0') {
        $errorMessage = isset($result->errorMessage) ? $result->errorMessage : 'STK Push failed';
        throw new Exception($errorMessage);
    }

    // Save transaction to database
    $query = "INSERT INTO mpesa_transactions
              (merchant_request_id, checkout_request_id, phone_number, amount,
               account_reference, transaction_desc, status, created_at)
              VALUES
              (:merchant_request_id, :checkout_request_id, :phone_number, :amount,
               :account_reference, :transaction_desc, 'pending', NOW())";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':merchant_request_id', $result->MerchantRequestID);
    $stmt->bindParam(':checkout_request_id', $result->CheckoutRequestID);
    $stmt->bindParam(':phone_number', $phone);
    $stmt->bindParam(':amount', $amount);
    $stmt->bindParam(':account_reference', $requestData['AccountReference']);
    $stmt->bindParam(':transaction_desc', $requestData['TransactionDesc']);
    $stmt->execute();

    sendResponse(200, 'STK Push sent successfully', [
        'merchant_request_id' => $result->MerchantRequestID,
        'checkout_request_id' => $result->CheckoutRequestID,
        'response_code' => $result->ResponseCode,
        'response_description' => $result->ResponseDescription,
        'customer_message' => $result->CustomerMessage,
        'phone' => $phone,
        'amount' => $amount
    ]);

} catch (Exception $e) {
    sendError(500, 'Payment initiation failed: ' . $e->getMessage());
}
?>
