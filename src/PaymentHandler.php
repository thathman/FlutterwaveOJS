<?php

namespace Workstation\FlutterwaveOjs;

use GuzzleHttp\Client;
use PKP\plugins\HookRegistry;
use PKP\core\Application;

class PaymentHandler
{
    private $plugin;
    private $publicKey;
    private $secretKey;
    private $liveMode;

    public function __construct($plugin)
    {
        $this->plugin = $plugin;
        $contextId = Application::get()->getRequest()->getContext()->getId();

        $this->publicKey = $this->plugin->getSetting($contextId, 'publicKey');
        $this->secretKey = $this->plugin->getSetting($contextId, 'secretKey');
        $this->liveMode = $this->plugin->getSetting($contextId, 'liveMode');
    }

    public function register($category, $path, $mainContextId)
    {
        HookRegistry::register('LoadHandler', function ($hookName, $params) {
            $page = $params[0];
            $op = $params[1];

            if ($page === 'flutterwave' && $op === 'webhook') {
                define('HANDLER_CLASS', WebhookHandler::class);
                return true;
            }
            return false;
        });

        return parent::register($category, $path, $mainContextId);
    }

    public function processPayment($amount, $currency, $email, $txRef)
    {
        $client = new Client();
        $url = $this->liveMode 
            ? "https://api.flutterwave.com/v3/payments" 
            : "https://api.flutterwave.com/v3/payments";

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
            $response = $client->post($url, [
                'headers' => $headers,
                'json'    => $body,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
