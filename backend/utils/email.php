<?php
/**
 * Email Utility Class
 *
 * Handles sending emails using SMTP
 */

require_once __DIR__ . '/../lib/SimpleMailer.php';

class EmailService {
    private $mailer;
    private $fromEmail;
    private $fromName;
    private $config;

    public function __construct() {
        // Load email configuration
        $this->config = require __DIR__ . '/../config/email.php';

        $this->fromEmail = $this->config['from']['email'];
        $this->fromName = $this->config['from']['name'];

        // Initialize mailer with config
        $smtpConfig = [
            'host' => $this->config['smtp']['host'],
            'port' => $this->config['smtp']['port'],
            'username' => $this->config['smtp']['username'],
            'password' => $this->config['smtp']['password'],
            'from_email' => $this->fromEmail,
            'from_name' => $this->fromName,
            'use_tls' => $this->config['smtp']['use_tls']
        ];

        $this->mailer = new SimpleMailer($smtpConfig);
    }

    /**
     * Send email using a template
     *
     * @param string $to Recipient email address
     * @param string $subject Email subject
     * @param string $templatePath Path to email template
     * @param array $data Data to pass to template
     * @return bool Success status
     */
    public function sendEmail($to, $subject, $templatePath, $data = []) {
        try {
            // Extract data for template
            extract($data);

            // Start output buffering
            ob_start();

            // Include template
            if (!file_exists($templatePath)) {
                throw new Exception("Email template not found: $templatePath");
            }

            include $templatePath;

            // Get email body
            $htmlBody = ob_get_clean();

            // Log email details
            error_log("Sending email to: $to, Subject: $subject");

            // Send email using SimpleMailer
            $success = $this->mailer->send($to, $subject, $htmlBody);

            if (!$success) {
                error_log("Failed to send email to: $to, Subject: $subject");
                return false;
            }

            error_log("Email sent successfully to: $to");
            return true;

        } catch (Exception $e) {
            error_log("Email sending error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send order receipt email
     *
     * @param string $to Recipient email
     * @param array $orderData Order data
     * @return bool Success status
     */
    public function sendOrderReceipt($to, $orderData) {
        $subject = "Order Confirmation #{$orderData['order_id']} - The Suvaya Experience";
        $templatePath = __DIR__ . '/../emails/templates/order-receipt.php';

        return $this->sendEmail($to, $subject, $templatePath, $orderData);
    }

    /**
     * Send reservation confirmation email
     *
     * @param string $to Recipient email
     * @param array $reservationData Reservation data
     * @return bool Success status
     */
    public function sendReservationConfirmation($to, $reservationData) {
        $subject = "Reservation Confirmed - The Suvaya Experience";
        $templatePath = __DIR__ . '/../emails/templates/reservation-confirmation.php';

        return $this->sendEmail($to, $subject, $templatePath, $reservationData);
    }

    /**
     * Set custom from email and name
     *
     * @param string $email From email address
     * @param string $name From name
     */
    public function setFrom($email, $name) {
        $this->fromEmail = $email;
        $this->fromName = $name;
    }
}

/**
 * Helper function to get email service instance
 *
 * @return EmailService
 */
function getEmailService() {
    return new EmailService();
}
