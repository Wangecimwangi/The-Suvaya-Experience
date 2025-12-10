<?php
/**
 * Order Receipt Email Template
 *
 * Available variables:
 * - $order_id: Order ID
 * - $customer_name: Customer name
 * - $customer_email: Customer email
 * - $customer_phone: Customer phone
 * - $items: Array of order items
 * - $subtotal: Order subtotal
 * - $deposit_amount: Deposit paid
 * - $balance_amount: Balance due
 * - $delivery_date: Delivery date
 * - $delivery_time: Delivery time
 * - $delivery_address: Delivery address
 * - $delivery_method: Delivery method (delivery/pickup)
 * - $payment_method: Payment method
 * - $special_instructions: Special instructions
 */

if (!function_exists('formatPrice')) {
    function formatPrice($amount) {
        return 'KES ' . number_format($amount, 2);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Receipt - The Suvaya Experience</title>
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
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .items-table th {
            background-color: #b28704;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #e0e0e0;
        }
        .items-table tr:last-child td {
            border-bottom: none;
        }
        .items-table .item-name {
            font-weight: 600;
            color: #333;
        }
        .items-table .item-quantity {
            text-align: center;
        }
        .items-table .item-price {
            text-align: right;
            font-weight: 600;
            color: #2e7d32;
        }
        .totals-section {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 15px;
        }
        .total-row.highlight {
            font-size: 18px;
            font-weight: 700;
            color: #2e7d32;
            border-top: 2px solid #b28704;
            padding-top: 15px;
            margin-top: 10px;
        }
        .total-row.balance {
            color: #d32f2f;
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
            .items-table th,
            .items-table td {
                padding: 8px 5px;
                font-size: 13px;
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
            <div class="greeting">Hello <?= htmlspecialchars($customer_name) ?>!</div>

            <div class="message">
                <p>Thank you for your order! We're excited to prepare your delicious treats.</p>
                <p>Your order has been received and confirmed. Here are the details:</p>
            </div>

            <!-- Order Information -->
            <div class="info-section">
                <h3>📋 Order Information</h3>
                <div class="info-row">
                    <span class="info-label">Order ID:</span>
                    <span class="info-value"><strong>#<?= htmlspecialchars($order_id) ?></strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Order Date:</span>
                    <span class="info-value"><?= date('F j, Y g:i A') ?></span>
                </div>
            </div>

            <!-- Delivery Information -->
            <div class="info-section">
                <h3>🚚 <?= $delivery_method === 'pickup' ? 'Pickup' : 'Delivery' ?> Information</h3>
                <div class="info-row">
                    <span class="info-label">Date:</span>
                    <span class="info-value"><?= date('l, F j, Y', strtotime($delivery_date)) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Time:</span>
                    <span class="info-value"><?= date('g:i A', strtotime($delivery_time)) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label"><?= $delivery_method === 'pickup' ? 'Location' : 'Address' ?>:</span>
                    <span class="info-value"><?= htmlspecialchars($delivery_address) ?></span>
                </div>
            </div>

            <!-- Order Items -->
            <h3 style="color: #b28704; margin: 25px 0 15px 0;">🛒 Order Items</h3>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th class="item-quantity">Qty</th>
                        <th style="text-align: right;">Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <div class="item-name"><?= htmlspecialchars($item['name']) ?></div>
                            <?php if (!empty($item['description'])): ?>
                            <div style="font-size: 12px; color: #666;"><?= htmlspecialchars($item['description']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="item-quantity"><?= $item['quantity'] ?></td>
                        <td class="item-price"><?= formatPrice($item['price'] * $item['quantity']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Totals -->
            <div class="totals-section">
                <div class="total-row">
                    <span>Subtotal:</span>
                    <span><?= formatPrice($subtotal) ?></span>
                </div>
                <div class="total-row highlight">
                    <span>✅ Deposit Paid:</span>
                    <span><?= formatPrice($deposit_amount) ?></span>
                </div>
                <div class="total-row balance">
                    <span>⏳ Balance Due (on <?= $delivery_method === 'pickup' ? 'pickup' : 'delivery' ?>):</span>
                    <span><?= formatPrice($balance_amount) ?></span>
                </div>
            </div>

            <?php if (!empty($special_instructions)): ?>
            <!-- Special Instructions -->
            <div class="info-section">
                <h3>📝 Special Instructions</h3>
                <p style="margin: 5px 0;"><?= nl2br(htmlspecialchars($special_instructions)) ?></p>
            </div>
            <?php endif; ?>

            <!-- Payment Alert -->
            <div class="alert-box">
                <strong>⚠️ Payment Reminder:</strong><br>
                The remaining balance of <strong><?= formatPrice($balance_amount) ?></strong> is due on <?= $delivery_method === 'pickup' ? 'pickup' : 'delivery' ?>.
                Payment can be made via M-Pesa or cash.
            </div>

            <!-- Contact Information -->
            <div class="info-section">
                <h3>📞 Contact Us</h3>
                <p style="margin: 5px 0;">
                    If you have any questions about your order, please don't hesitate to contact us:<br>
                    <strong>Phone:</strong> +254 700 000 000<br>
                    <strong>Email:</strong> orders@suvaya.com
                </p>
            </div>

            <div class="message">
                <p>We're working hard to make your order perfect. Thank you for choosing The Suvaya Experience!</p>
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
                <a href="mailto:info@suvaya.com">Contact Us</a>
            </p>
        </div>
    </div>
</body>
</html>
