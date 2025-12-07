<?php
/**
 * Simple Email Test Script
 *
 * Run this to test your email configuration
 * Usage: php test-email-simple.php
 * Or access via browser: http://localhost:8000/backend/test-email-simple.php
 */

require_once 'utils/email.php';

// Test email address (CHANGE THIS TO YOUR EMAIL)
$testEmail = 'your-email@example.com';

echo "<h1>Email Configuration Test</h1>";
echo "<p>Testing email setup for The Suvaya Experience...</p>";
echo "<hr>";

try {
    // Create email service
    $emailService = getEmailService();

    echo "<h2>✅ Email Service Initialized</h2>";

    // Test Order Receipt
    echo "<h3>Testing Order Receipt Email...</h3>";
    $orderData = [
        'order_id' => 'TEST-' . date('Ymd-His'),
        'customer_name' => 'Test Customer',
        'customer_email' => $testEmail,
        'customer_phone' => '+254712345678',
        'items' => [
            [
                'name' => 'Chocolate Cake',
                'description' => 'Rich chocolate cake',
                'quantity' => 1,
                'price' => 3500
            ],
            [
                'name' => 'Vanilla Cupcakes',
                'description' => '6 pack cupcakes',
                'quantity' => 2,
                'price' => 600
            ]
        ],
        'subtotal' => 4700,
        'deposit_amount' => 2350,
        'balance_amount' => 2350,
        'delivery_date' => date('Y-m-d', strtotime('+3 days')),
        'delivery_time' => '14:00',
        'delivery_address' => '123 Test Street, Nairobi',
        'delivery_method' => 'delivery',
        'payment_method' => 'card',
        'special_instructions' => 'This is a test order'
    ];

    $orderResult = $emailService->sendOrderReceipt($testEmail, $orderData);

    if ($orderResult) {
        echo "<p style='color: green;'>✅ Order receipt email sent/logged successfully!</p>";
    } else {
        echo "<p style='color: red;'>❌ Order receipt email failed</p>";
    }

    echo "<hr>";

    // Test Reservation Confirmation
    echo "<h3>Testing Reservation Confirmation Email...</h3>";
    $reservationData = [
        'name' => 'Test Customer',
        'email' => $testEmail,
        'phone' => '+254712345678',
        'date' => date('Y-m-d', strtotime('+7 days')),
        'time' => '15:00',
        'guests' => 4,
        'notes' => 'This is a test reservation'
    ];

    $reservationResult = $emailService->sendReservationConfirmation($testEmail, $reservationData);

    if ($reservationResult) {
        echo "<p style='color: green;'>✅ Reservation confirmation email sent/logged successfully!</p>";
    } else {
        echo "<p style='color: red;'>❌ Reservation confirmation email failed</p>";
    }

    echo "<hr>";
    echo "<h2>Test Complete!</h2>";

    // Check configuration
    echo "<h3>Configuration Status:</h3>";
    $config = require 'config/email.php';

    if (empty($config['smtp']['username']) || empty($config['smtp']['password'])) {
        echo "<p style='color: orange;'>⚠️ <strong>SMTP Not Configured</strong></p>";
        echo "<p>Emails are being logged to files instead of being sent.</p>";
        echo "<p><strong>Email logs location:</strong> backend/logs/emails/email_" . date('Y-m-d') . ".log</p>";
        echo "<p>To send real emails, configure SMTP in <code>backend/config/email.php</code></p>";
        echo "<p><a href='../EMAIL_SETUP_GUIDE.md' target='_blank'>📖 View Email Setup Guide</a></p>";
    } else {
        echo "<p style='color: green;'>✅ <strong>SMTP Configured</strong></p>";
        echo "<p>Using: " . htmlspecialchars($config['smtp']['host']) . ":" . $config['smtp']['port'] . "</p>";
        echo "<p>From: " . htmlspecialchars($config['from']['email']) . "</p>";
        echo "<p>Check <strong>" . htmlspecialchars($testEmail) . "</strong> inbox for test emails.</p>";
        echo "<p><em>Note: Emails may take a few minutes to arrive and might be in spam folder.</em></p>";
    }

    echo "<hr>";
    echo "<h3>Next Steps:</h3>";
    echo "<ul>";
    echo "<li>If using file logging: Check <code>backend/logs/emails/</code> directory</li>";
    echo "<li>If using SMTP: Check your email inbox (and spam folder)</li>";
    echo "<li>To configure SMTP: Edit <code>backend/config/email.php</code></li>";
    echo "<li>Read the full guide: <a href='../EMAIL_SETUP_GUIDE.md' target='_blank'>EMAIL_SETUP_GUIDE.md</a></li>";
    echo "</ul>";

} catch (Exception $e) {
    echo "<p style='color: red;'><strong>❌ Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Check error logs for more details.</p>";
}

echo "<hr>";
echo "<p><small>Test run at: " . date('Y-m-d H:i:s') . "</small></p>";
?>
