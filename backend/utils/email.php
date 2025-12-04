<?php
/**
 * Email Utility Class
 *
 * Handles sending emails using PHP's mail() function
 * For production, consider using PHPMailer or similar library with SMTP
 */

class EmailService {
    private $fromEmail;
    private $fromName;

    public function __construct() {
        $this->fromEmail = 'noreply@suvaya.com';
        $this->fromName = 'The Suvaya Experience';
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
            $body = ob_get_clean();

            // Set headers for HTML email
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=UTF-8\r\n";
            $headers .= "From: {$this->fromName} <{$this->fromEmail}>\r\n";
            $headers .= "Reply-To: {$this->fromEmail}\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion();

            // Send email
            $success = mail($to, $subject, $body, $headers);

            if (!$success) {
                error_log("Failed to send email to: $to, Subject: $subject");
                return false;
            }

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
