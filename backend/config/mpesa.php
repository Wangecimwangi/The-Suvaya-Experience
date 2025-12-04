<?php
/**
 * M-Pesa Daraja API Configuration
 *
 * To get your credentials:
 * 1. Go to https://developer.safaricom.co.ke/
 * 2. Create an account and login
 * 3. Create an app to get Consumer Key and Secret
 * 4. Use sandbox for testing, production for live
 */

class MpesaConfig {
    // Environment: 'sandbox' or 'production'
    private $environment = 'sandbox';

    // Sandbox Credentials (for testing)
    private $sandbox_consumer_key = 'VcORe1bpTxizseT8OGdiMgAKXWXZepTzPNHnb97g0DbcHm3V';
    private $sandbox_consumer_secret = '2aTxm3K9fhfgmTx9YZjCSm7dvCce6oGVjJVxtzcZY51dZ9Cazg7O99nFvuBrF2Em';
    private $sandbox_shortcode = '174379'; // Sandbox shortcode
    private $sandbox_passkey = 'gBj6wcqXIMJ1dJP7XKkNg7W+aQ/JlZOnFEhfJYBglgj1CMHpDeyrKbcVfanCbMw9GNt2tqubRyek7FAyX/1j87/gUZ7di+ZBkPF5fJPgfTLthOXlAT3uAw75g4Dw/qdDmlvRCLprsiJZyJhx1P9te58I4Nkn/VmfPEx9FefCodzMSY5q8XrTuhpfHm2+6CeGl/j8XGYgjcPh82X50pig5Dr+nt/MKyI7UHFopA8w3PfD6x8knOv0iT+Fzv+qPkwu6qI7+aBw4bHsLby/59vXhBjzh9I1nqhZ6h+ZthKgJ0IJGaDuGKmmrxJX2iWcR1LimWdTAfLvU+mhclu8ziCFqA=='; // Sandbox passkey

    // Production Credentials (for live)
    private $production_consumer_key = 'YOUR_PRODUCTION_CONSUMER_KEY';
    private $production_consumer_secret = 'YOUR_PRODUCTION_CONSUMER_SECRET';
    private $production_shortcode = 'YOUR_BUSINESS_SHORTCODE';
    private $production_passkey = 'YOUR_PRODUCTION_PASSKEY';

    // API URLs
    private $sandbox_base_url = 'https://sandbox.safaricom.co.ke';
    private $production_base_url = 'https://api.safaricom.co.ke';

    // Callback URLs (update these to your actual server URLs)
    private $callback_url = 'http://localhost:8000/api/mpesa/callback.php';
    private $timeout_url = 'http://localhost:8000/api/mpesa/timeout.php';

    public function getConsumerKey() {
        return $this->environment === 'sandbox'
            ? $this->sandbox_consumer_key
            : $this->production_consumer_key;
    }

    public function getConsumerSecret() {
        return $this->environment === 'sandbox'
            ? $this->sandbox_consumer_secret
            : $this->production_consumer_secret;
    }

    public function getShortcode() {
        return $this->environment === 'sandbox'
            ? $this->sandbox_shortcode
            : $this->production_shortcode;
    }

    public function getPasskey() {
        return $this->environment === 'sandbox'
            ? $this->sandbox_passkey
            : $this->production_passkey;
    }

    public function getBaseUrl() {
        return $this->environment === 'sandbox'
            ? $this->sandbox_base_url
            : $this->production_base_url;
    }

    public function getCallbackUrl() {
        return $this->callback_url;
    }

    public function getTimeoutUrl() {
        return $this->timeout_url;
    }

    public function getEnvironment() {
        return $this->environment;
    }

    /**
     * Generate OAuth Access Token
     */
    public function getAccessToken() {
        $url = $this->getBaseUrl() . '/oauth/v1/generate?grant_type=client_credentials';
        $credentials = base64_encode($this->getConsumerKey() . ':' . $this->getConsumerSecret());

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . $credentials]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            throw new Exception('Failed to get access token: ' . $error);
        }

        $result = json_decode($response);

        if (!isset($result->access_token)) {
            throw new Exception('Invalid access token response: ' . $response);
        }

        return $result->access_token;
    }

    /**
     * Generate Password for STK Push
     */
    public function generatePassword($timestamp) {
        return base64_encode($this->getShortcode() . $this->getPasskey() . $timestamp);
    }

    /**
     * Get current timestamp in the format required by M-Pesa
     */
    public function getTimestamp() {
        return date('YmdHis');
    }
}
?>
