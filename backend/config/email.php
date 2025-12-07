<?php
/**
 * Email Configuration
 *
 * Configure your email settings here
 */

return [
    // SMTP Settings
    'smtp' => [
        'enabled' => true, // Set to false to use file logging only

        // Gmail SMTP (Recommended for testing)
        // You need to create an "App Password" in your Google Account
        // https://myaccount.google.com/apppasswords
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'username' => '', // Your Gmail address (e.g., youremail@gmail.com)
        'password' => '', // Your Gmail App Password (NOT your regular password)
        'use_tls' => true,

        // Alternative: Mailtrap (Testing only - emails don't actually send)
        // 'host' => 'smtp.mailtrap.io',
        // 'port' => 2525,
        // 'username' => '', // Your Mailtrap username
        // 'password' => '', // Your Mailtrap password
        // 'use_tls' => true,

        // Alternative: SendGrid
        // 'host' => 'smtp.sendgrid.net',
        // 'port' => 587,
        // 'username' => 'apikey',
        // 'password' => '', // Your SendGrid API Key
        // 'use_tls' => true,

        // Alternative: Mailgun
        // 'host' => 'smtp.mailgun.org',
        // 'port' => 587,
        // 'username' => '', // Your Mailgun SMTP username
        // 'password' => '', // Your Mailgun SMTP password
        // 'use_tls' => true,
    ],

    // Email From Settings
    'from' => [
        'email' => 'noreply@suvaya.com',
        'name' => 'The Suvaya Experience'
    ],

    // Admin Notifications
    'admin' => [
        'email' => 'chegeeddie@gmail.com', // Admin email
        'name' => 'Suvaya Admin'
    ],

    // Email Logging
    'log' => [
        'enabled' => true, // Always log emails to file
        'path' => __DIR__ . '/../logs/emails'
    ]
];
