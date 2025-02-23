<?php

namespace Workstation\FlutterwaveOjs;

use GuzzleHttp\Client;
use PKP\core\Application;
use Psr\Log\LoggerInterface;

class PaymentHandler
{
    private $plugin;
    private $publicKey;
    private $secretKey;
    private $liveMode;
    private $logger;

    public function __construct($plugin, LoggerInterface $logger)
    {
        $this->plugin = $plugin;
        $this->logger = $logger;
        $contextId = Application::get()->getRequest()->getContext()->getId();

        $this->publicKey = $this->plugin->getSetting($contextId, 'publicKey');
        $this->secretKey = $this->plugin->getSetting($contextId, 'secretKey');
        $this->liveMode = $this->plugin->getSetting($contextId, 'liveMode');
    }

    public function processPayment($amount, $currency, $email, $txRef)
    {
        $client = new Client();
        $url = $this->liveMode
            ? "https://api.flutterwave.com/v3/charges?tx_ref=$txRef"
            : "https://api.flutterwave.com/v3/charges?tx_ref=$txRef"; // Update with actual test URL if needed

        $headers = [
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type'  => 'application/json',
        ];

        $body = [
            'tx_ref'       => $txRef,
            'amount'       => $amount,
            'currency'     => $currency,
            'redirect_url' => "https://your-journal.com/flutterwave/callback",
            'payment_type' => 'card',
            'customer'     => ['email' => $email]
        ];

        try {
            $this->logger->info('Sending payment request to Flutterwave', ['tx_ref' => $txRef, 'amount' => $amount, 'currency' => $currency]);

            $response = $client->post($url, [
                'headers' => $headers,
                'json'    => $body,
            ]);

            $responseBody = json_decode($response->getBody()->getContents(), true);

            if (isset($responseBody['status']) && $responseBody['status'] === 'success') {
                $this->logger->info('Payment successful', ['tx_ref' => $txRef, 'response' => $responseBody]);
                return $responseBody;
            }

            $this->logger->error('Payment failed', ['tx_ref' => $txRef, 'response' => $responseBody]);
            return ['error' => 'Payment failed', 'details' => $responseBody];

        } catch (\Exception $e) {
            $this->logger->error('Error processing payment', ['tx_ref' => $txRef, 'exception' => $e->getMessage()]);
            return ['error' => $e->getMessage()];
        }
    }
}
