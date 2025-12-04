<?php
/**
 * Reservation Confirmation Email Template
 *
 * Available variables:
 * - $name: Customer name
 * - $email: Customer email
 * - $phone: Customer phone
 * - $date: Reservation date
 * - $time: Reservation time
 * - $guests: Number of guests
 * - $notes: Special requests/notes
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Confirmation - The Suvaya Experience</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #b28704 0%, #d4a017 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .header p {
            margin: 10px 0 0 0;
            font-size: 14px;
            opacity: 0.95;
        }
        .content {
            padding: 30px 20px;
        }
        .greeting {
            font-size: 18px;
            color: #b28704;
            margin-bottom: 15px;
            font-weight: 600;
        }
        .message {
            margin-bottom: 25px;
            line-height: 1.8;
        }
        .confirmation-badge {
            background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);
            color: white;
            text-align: center;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
        }
        .confirmation-badge .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .confirmation-badge h2 {
            margin: 0;
            font-size: 24px;
        }
        .confirmation-badge p {
            margin: 5px 0 0 0;
            opacity: 0.95;
        }
        .info-section {
            background-color: #fffbe6;
            border-left: 4px solid #b28704;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info-section h3 {
            margin: 0 0 10px 0;
            color: #b28704;
            font-size: 16px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #666;
        }
        .info-value {
            color: #333;
            text-align: right;
            font-weight: 500;
        }
        .highlight-box {
            background: linear-gradient(135deg, #fffbe6 0%, #fff8dc 100%);
            border: 2px solid #b28704;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .highlight-box .date {
            font-size: 32px;
            font-weight: 700;
            color: #b28704;
            margin: 10px 0;
        }
        .highlight-box .time {
            font-size: 24px;
            color: #666;
            margin: 5px 0;
        }
        .alert-box {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        .alert-box strong {
            color: #856404;
        }
        .checklist {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .checklist h3 {
            color: #b28704;
            margin: 0 0 15px 0;
        }
        .checklist-item {
            display: flex;
            align-items: flex-start;
            margin: 10px 0;
        }
        .checklist-item .icon {
            color: #2e7d32;
            font-size: 20px;
            margin-right: 10px;
        }
        .footer {
            background-color: #f5f5f5;
            padding: 20px;
            text-align: center;
            font-size: 13px;
            color: #666;
        }
        .footer a {
            color: #b28704;
            text-decoration: none;
        }
        .button {
            display: inline-block;
            background-color: #b28704;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
        }
        @media only screen and (max-width: 600px) {
            body {
                padding: 10px;
            }
            .content {
                padding: 20px 15px;
            }
            .highlight-box .date {
                font-size: 24px;
            }
            .highlight-box .time {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>🎂 The Suvaya Experience</h1>
            <p>Artisan Baking & Events</p>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Confirmation Badge -->
            <div class="confirmation-badge">
                <div class="icon">✓</div>
                <h2>Reservation Confirmed!</h2>
                <p>Your booking has been successfully confirmed</p>
            </div>

            <div class="greeting">Hello <?= htmlspecialchars($name) ?>!</div>

            <div class="message">
                <p>Thank you for choosing The Suvaya Experience for your special occasion!</p>
                <p>We're thrilled to confirm your reservation. Your date has been secured, and we're looking forward to making your event memorable.</p>
            </div>

            <!-- Date and Time Highlight -->
            <div class="highlight-box">
                <h3 style="margin: 0 0 10px 0; color: #b28704;">📅 Your Reservation</h3>
                <div class="date"><?= date('F j, Y', strtotime($date)) ?></div>
                <div class="time">🕐 <?= date('g:i A', strtotime($time)) ?></div>
                <div style="margin-top: 15px; font-size: 18px; color: #666;">
                    👥 <?= $guests ?> <?= $guests == 1 ? 'Guest' : 'Guests' ?>
                </div>
            </div>

            <!-- Reservation Details -->
            <div class="info-section">
                <h3>📋 Reservation Details</h3>
                <div class="info-row">
                    <span class="info-label">Name:</span>
                    <span class="info-value"><?= htmlspecialchars($name) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span class="info-value"><?= htmlspecialchars($email) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Phone:</span>
                    <span class="info-value"><?= htmlspecialchars($phone) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Date:</span>
                    <span class="info-value"><?= date('l, F j, Y', strtotime($date)) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Time:</span>
                    <span class="info-value"><?= date('g:i A', strtotime($time)) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Number of Guests:</span>
                    <span class="info-value"><?= $guests ?></span>
                </div>
            </div>

            <?php if (!empty($notes)): ?>
            <!-- Special Requests -->
            <div class="info-section">
                <h3>📝 Your Special Requests</h3>
                <p style="margin: 5px 0;"><?= nl2br(htmlspecialchars($notes)) ?></p>
            </div>
            <?php endif; ?>

            <!-- Payment Information -->
            <div class="alert-box">
                <strong>💰 Payment Information:</strong><br>
                A 50% deposit is required to confirm your reservation. We'll send you payment instructions shortly.
                The remaining balance will be due on delivery or at the event.
            </div>

            <!-- What to Expect -->
            <div class="checklist">
                <h3>✨ What Happens Next?</h3>
                <div class="checklist-item">
                    <span class="icon">✓</span>
                    <div>
                        <strong>Payment Instructions:</strong><br>
                        You'll receive payment instructions via M-Pesa or our preferred payment method.
                    </div>
                </div>
                <div class="checklist-item">
                    <span class="icon">✓</span>
                    <div>
                        <strong>Order Confirmation:</strong><br>
                        Once your deposit is received, we'll confirm your order and start preparation.
                    </div>
                </div>
                <div class="checklist-item">
                    <span class="icon">✓</span>
                    <div>
                        <strong>Final Details:</strong><br>
                        We'll contact you 2-3 days before your event to finalize any last details.
                    </div>
                </div>
                <div class="checklist-item">
                    <span class="icon">✓</span>
                    <div>
                        <strong>Delivery/Pickup:</strong><br>
                        Your order will be ready at the scheduled time. We'll send a reminder the day before.
                    </div>
                </div>
            </div>

            <!-- Important Reminders -->
            <div class="info-section">
                <h3>⚠️ Important Reminders</h3>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Please arrive 15 minutes early if you're picking up your order</li>
                    <li>For delivery orders, ensure someone is available at the delivery address</li>
                    <li>If you need to make changes, contact us at least 48 hours in advance</li>
                    <li>Cancellations made less than 48 hours before the event may be subject to a cancellation fee</li>
                </ul>
            </div>

            <!-- Contact Information -->
            <div class="info-section">
                <h3>📞 Need to Make Changes?</h3>
                <p style="margin: 5px 0;">
                    If you need to modify or cancel your reservation, please contact us as soon as possible:<br><br>
                    <strong>Phone:</strong> +254 700 000 000<br>
                    <strong>Email:</strong> reservations@suvaya.com<br>
                    <strong>Hours:</strong> Monday - Saturday, 9:00 AM - 6:00 PM
                </p>
            </div>

            <div class="message">
                <p>We're excited to be part of your special occasion and can't wait to create something amazing for you!</p>
                <p style="margin-top: 20px;">
                    <strong>With love,</strong><br>
                    <span style="color: #b28704; font-weight: 600;">The Suvaya Team</span>
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>© <?= date('Y') ?> The Suvaya Experience. All rights reserved.</p>
            <p>
                <a href="https://suvaya.com">Visit Our Website</a> |
                <a href="mailto:info@suvaya.com">Contact Us</a> |
                <a href="https://suvaya.com/my-reservations">View My Reservations</a>
            </p>
            <p style="margin-top: 10px; font-size: 11px; color: #999;">
                This is an automated confirmation email for your reservation at The Suvaya Experience.
            </p>
        </div>
    </div>
</body>
</html>
