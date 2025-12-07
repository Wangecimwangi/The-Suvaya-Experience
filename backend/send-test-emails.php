<?php
/**
 * Send Test Emails to chegeeddie@gmail.com
 *
 * This script sends sample order and reservation emails
 * Run via: php backend/send-test-emails.php
 * Or access via browser: http://localhost:8000/backend/send-test-emails.php
 */

require_once 'utils/email.php';

$testEmail = 'chegeeddie@gmail.com';

echo "<h1>🧪 Email Test Script</h1>";
echo "<p>Sending test emails to: <strong>$testEmail</strong></p>";
echo "<hr>";

try {
    $emailService = getEmailService();

    // Test 1: Order Receipt Email
    echo "<h2>📦 Test 1: Order Receipt Email</h2>";

    $orderData = [
        'order_id' => 'ORD-TEST-' . date('Ymd-His'),
        'customer_name' => 'Eddie Chege',
        'customer_email' => $testEmail,
        'customer_phone' => '+254712345678',
        'items' => [
            [
                'name' => 'Chocolate Celebration Cake',
                'description' => 'Rich 2kg chocolate cake with buttercream frosting',
                'quantity' => 1,
                'price' => 4500
            ],
            [
                'name' => 'Vanilla Cupcakes',
                'description' => 'Classic vanilla cupcakes with cream cheese frosting (dozen)',
                'quantity' => 2,
                'price' => 1200
            ],
            [
                'name' => 'Assorted Cookies',
                'description' => 'Mix of chocolate chip, oatmeal, and sugar cookies',
                'quantity' => 1,
                'price' => 800
            ]
        ],
        'subtotal' => 7700,
        'deposit_amount' => 3850,
        'balance_amount' => 3850,
        'delivery_date' => date('Y-m-d', strtotime('+5 days')),
        'delivery_time' => '14:00',
        'delivery_address' => '123 Nairobi Street, Westlands, Nairobi',
        'delivery_method' => 'delivery',
        'payment_method' => 'card',
        'special_instructions' => 'Please include birthday candles and a cake knife. The cake should say "Happy Birthday Sarah!" in pink frosting.'
    ];

    $orderResult = $emailService->sendOrderReceipt($testEmail, $orderData);

    if ($orderResult) {
        echo "<p style='color: green;'>✅ <strong>Order receipt email sent successfully!</strong></p>";
        echo "<p>Check your inbox at <strong>$testEmail</strong></p>";
        echo "<p><em>Order ID: {$orderData['order_id']}</em></p>";
    } else {
        echo "<p style='color: red;'>❌ Failed to send order receipt email</p>";
    }

    echo "<hr>";

    // Test 2: Reservation Confirmation Email
    echo "<h2>📅 Test 2: Reservation Confirmation Email</h2>";

    $reservationData = [
        'name' => 'Eddie Chege',
        'email' => $testEmail,
        'phone' => '+254712345678',
        'date' => date('Y-m-d', strtotime('+10 days')),
        'time' => '15:00',
        'guests' => 25,
        'notes' => 'Birthday celebration for my daughter. We would prefer a chocolate theme with pink decorations. Please ensure the area is decorated nicely!'
    ];

    $reservationResult = $emailService->sendReservationConfirmation($testEmail, $reservationData);

    if ($reservationResult) {
        echo "<p style='color: green;'>✅ <strong>Reservation confirmation email sent successfully!</strong></p>";
        echo "<p>Check your inbox at <strong>$testEmail</strong></p>";
        echo "<p><em>Reservation Date: {$reservationData['date']} at {$reservationData['time']}</em></p>";
    } else {
        echo "<p style='color: red;'>❌ Failed to send reservation confirmation email</p>";
    }

    echo "<hr>";

    // Summary
    echo "<h2>📊 Summary</h2>";

    $config = require 'config/email.php';

    if (empty($config['smtp']['username']) || empty($config['smtp']['password'])) {
        echo "<div style='background: #fff3cd; padding: 15px; border-radius: 8px; border-left: 4px solid #ffc107;'>";
        echo "<h3 style='color: #856404; margin-top: 0;'>⚠️ SMTP Not Configured</h3>";
        echo "<p style='color: #856404;'>Emails are being logged to files instead of being sent via email.</p>";
        echo "<p><strong>Email logs location:</strong></p>";
        echo "<code style='background: #fff; padding: 8px; display: block; border-radius: 4px;'>";
        echo "backend/logs/emails/email_" . date('Y-m-d') . ".log";
        echo "</code>";
        echo "<p style='margin-bottom: 0;'>To send real emails, configure SMTP credentials in <code>backend/config/email.php</code></p>";
        echo "<p><a href='../EMAIL_SETUP_GUIDE.md' target='_blank' style='color: #004085;'>📖 View Email Setup Guide</a></p>";
        echo "</div>";
    } else {
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 8px; border-left: 4px solid #28a745;'>";
        echo "<h3 style='color: #155724; margin-top: 0;'>✅ SMTP Configured</h3>";
        echo "<p style='color: #155724;'>Emails should be sent via SMTP to <strong>$testEmail</strong></p>";
        echo "<p><strong>SMTP Server:</strong> {$config['smtp']['host']}:{$config['smtp']['port']}</p>";
        echo "<p style='margin-bottom: 0;'><em>Note: Check your email inbox (and spam folder) for the test emails.</em></p>";
        echo "</div>";
    }

    echo "<hr>";

    echo "<h3>🔍 What to Check:</h3>";
    echo "<ol>";
    echo "<li>Check <strong>$testEmail</strong> inbox</li>";
    echo "<li>Check spam/junk folder if not in inbox</li>";
    if (empty($config['smtp']['username'])) {
        echo "<li>View email logs in <code>backend/logs/emails/</code> directory</li>";
    }
    echo "<li>Both emails should have beautiful HTML formatting</li>";
    echo "<li>Order email should show all 3 items with prices</li>";
    echo "<li>Reservation email should show date, time, and guest count</li>";
    echo "</ol>";

    echo "<hr>";
    echo "<p><small>Test completed at: " . date('Y-m-d H:i:s') . "</small></p>";

} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 8px; border-left: 4px solid #dc3545;'>";
    echo "<h3 style='color: #721c24; margin-top: 0;'>❌ Error Occurred</h3>";
    echo "<p style='color: #721c24;'><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p style='color: #721c24;'>Check error logs for more details.</p>";
    echo "</div>";
}
?>
