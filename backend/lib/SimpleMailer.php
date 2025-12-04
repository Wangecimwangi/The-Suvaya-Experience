<?php
/**
 * Simple SMTP Mailer
 * A lightweight email sender that works without external dependencies
 */

class SimpleMailer {
    private $host;
    private $port;
    private $username;
    private $password;
    private $fromEmail;
    private $fromName;
    private $useTLS;

    public function __construct($config = []) {
        // Default to Gmail SMTP settings
        $this->host = $config['host'] ?? 'smtp.gmail.com';
        $this->port = $config['port'] ?? 587;
        $this->username = $config['username'] ?? '';
        $this->password = $config['password'] ?? '';
        $this->fromEmail = $config['from_email'] ?? 'noreply@suvaya.com';
        $this->fromName = $config['from_name'] ?? 'The Suvaya Experience';
        $this->useTLS = $config['use_tls'] ?? true;
    }

    /**
     * Send email
     */
    public function send($to, $subject, $htmlBody, $textBody = '') {
        // If credentials not set, log to file instead
        if (empty($this->username) || empty($this->password)) {
            return $this->logToFile($to, $subject, $htmlBody);
        }

        try {
            // Create connection
            $socket = fsockopen(
                $this->useTLS ? 'tls://' . $this->host : $this->host,
                $this->port,
                $errno,
                $errstr,
                30
            );

            if (!$socket) {
                throw new Exception("Could not connect to SMTP server: $errstr ($errno)");
            }

            // Read server response
            $this->getResponse($socket);

            // Send EHLO
            fputs($socket, "EHLO " . $this->host . "\r\n");
            $this->getResponse($socket);

            // Authenticate
            fputs($socket, "AUTH LOGIN\r\n");
            $this->getResponse($socket);

            fputs($socket, base64_encode($this->username) . "\r\n");
            $this->getResponse($socket);

            fputs($socket, base64_encode($this->password) . "\r\n");
            $this->getResponse($socket);

            // Set from
            fputs($socket, "MAIL FROM: <" . $this->fromEmail . ">\r\n");
            $this->getResponse($socket);

            // Set to
            fputs($socket, "RCPT TO: <" . $to . ">\r\n");
            $this->getResponse($socket);

            // Start data
            fputs($socket, "DATA\r\n");
            $this->getResponse($socket);

            // Build email
            $boundary = md5(time());
            $headers = "From: " . $this->fromName . " <" . $this->fromEmail . ">\r\n";
            $headers .= "Reply-To: " . $this->fromEmail . "\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: multipart/alternative; boundary=\"$boundary\"\r\n";
            $headers .= "Subject: " . $subject . "\r\n";
            $headers .= "\r\n";

            $body = "--$boundary\r\n";
            $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
            $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
            $body .= $textBody ?: strip_tags($htmlBody);
            $body .= "\r\n\r\n";
            $body .= "--$boundary\r\n";
            $body .= "Content-Type: text/html; charset=UTF-8\r\n";
            $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
            $body .= $htmlBody;
            $body .= "\r\n\r\n";
            $body .= "--$boundary--\r\n";

            // Send email
            fputs($socket, $headers . $body . "\r\n.\r\n");
            $this->getResponse($socket);

            // Quit
            fputs($socket, "QUIT\r\n");
            $this->getResponse($socket);

            fclose($socket);

            return true;

        } catch (Exception $e) {
            error_log("SMTP Error: " . $e->getMessage());
            // Fallback to file logging
            return $this->logToFile($to, $subject, $htmlBody);
        }
    }

    /**
     * Get server response
     */
    private function getResponse($socket) {
        $response = '';
        while ($str = fgets($socket, 515)) {
            $response .= $str;
            if (substr($str, 3, 1) == ' ') {
                break;
            }
        }
        return $response;
    }

    /**
     * Log email to file (fallback when SMTP not configured)
     */
    private function logToFile($to, $subject, $body) {
        $logDir = __DIR__ . '/../logs/emails';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        $logFile = $logDir . '/email_' . date('Y-m-d') . '.log';
        $timestamp = date('Y-m-d H:i:s');

        $logContent = "\n" . str_repeat('=', 80) . "\n";
        $logContent .= "Timestamp: $timestamp\n";
        $logContent .= "To: $to\n";
        $logContent .= "Subject: $subject\n";
        $logContent .= str_repeat('-', 80) . "\n";
        $logContent .= "Body:\n";
        $logContent .= strip_tags($body) . "\n";
        $logContent .= str_repeat('=', 80) . "\n";

        file_put_contents($logFile, $logContent, FILE_APPEND);

        error_log("Email logged to file: $logFile (SMTP not configured)");

        return true;
    }
}
