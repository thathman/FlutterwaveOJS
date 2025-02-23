<?php

namespace Workstation\FlutterwaveOjs;

use PKP\core\Application;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;

class WebhookHandler
{
    private $plugin;
    private $logger;

    public function __construct($plugin, LoggerInterface $logger)
    {
        $this->plugin = $plugin;
        $this->logger = $logger;
    }

    public function handleWebhook($params)
    {
        $data = json_decode($params[0], true);
        
        $txRef = $data['tx_ref'] ?? null;
        $status = $data['status'] ?? null;
        $amount = $data['amount'] ?? null;
        $currency = $data['currency'] ?? null;
        
        if (!$txRef || !$status) {
            $this->logger->error('Invalid webhook data', ['data' => $data]);
            return ['error' => 'Invalid data received'];
        }

        // You can add validation checks here to verify the status of the payment
        if ($status === 'successful') {
            $this->logger->info('Payment confirmed successfully', ['tx_ref' => $txRef]);
            // You can implement further actions (e.g., updating order status in OJS)
            return ['status' => 'success', 'message' => 'Payment processed successfully'];
                } else {
            $this->logger->warning('Payment failed or pending', ['tx_ref' => $txRef, 'status' => $status]);
            return ['status' => 'failed', 'message' => 'Payment failed or pending'];
        }
    }

    public function verifyPayment($txRef)
    {
        $client = new Client();
        $contextId = Application::get()->getRequest()->getContext()->getId();
        $secretKey = $this->plugin->getSetting($contextId, 'secretKey');
        $url = $this->plugin->getSetting($contextId, 'liveMode')
            ? "https://api.flutterwave.com/v3/charges?tx_ref=$txRef"
            : "https://api.flutterwave.com/v3/charges?tx_ref=$txRef"; // Use test URL for testing

        $headers = [
            'Authorization' => 'Bearer ' . $secretKey,
            'Content-Type'  => 'application/json',
        ];

        try {
            $response = $client->get($url, ['headers' => $headers]);

            $responseBody = json_decode($response->getBody()->getContents(), true);

            if (isset($responseBody['status']) && $responseBody['status'] === 'success') {
                $this->logger->info('Payment verified successfully', ['tx_ref' => $txRef, 'response' => $responseBody]);
                return $responseBody;
            }

            $this->logger->error('Payment verification failed', ['tx_ref' => $txRef, 'response' => $responseBody]);
            return ['error' => 'Payment verification failed', 'details' => $responseBody];

        } catch (\Exception $e) {
            $this->logger->error('Error verifying payment', ['tx_ref' => $txRef, 'exception' => $e->getMessage()]);
            return ['error' => $e->getMessage()];
        }
    }
}
