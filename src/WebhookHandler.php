<?php
namespace Workstation\FlutterwaveOjs;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use PKP\core\PKPHandler;
use PKP\db\DAORegistry;

class WebhookHandler extends PKPHandler
{
    private $secretKey;

    public function __construct()
    {
        parent::__construct();
        $this->secretKey = getenv('FLUTTERWAVE_SECRET_KEY');
    }

    public function webhook($args, $request)
    {
        // Read the incoming JSON payload
        $input = @file_get_contents("php://input");
        $event = json_decode($input, true);

        // Log webhook payload (useful for debugging)
        file_put_contents(__DIR__ . "/../logs/webhook.log", date('Y-m-d H:i:s') . " - " . json_encode($event) . "\n", FILE_APPEND);

        // Validate payload
        if (!$event || !isset($event['event'], $event['data'])) {
            http_response_code(400);
            exit("Invalid webhook payload");
        }

        // Check if it's a Flutterwave transaction event
        if ($event['event'] !== "charge.completed") {
            http_response_code(200);
            exit("Ignoring non-payment event");
        }

        $transactionId = $event['data']['id'];
        $status = $event['data']['status'];
        $orderRef = $event['data']['tx_ref']; // Reference OJS uses
        $amount = $event['data']['amount'];
        $currency = $event['data']['currency'];

        // Validate transaction status
        if ($status !== "successful") {
            http_response_code(400);
            exit("Payment failed or incomplete");
        }

        // Verify transaction with Flutterwave API
        if (!$this->verifyTransaction($transactionId)) {
            http_response_code(400);
            exit("Transaction verification failed");
        }

        // Mark the payment as completed in OJS
        $this->updatePaymentStatus($orderRef, $amount, $currency);

        http_response_code(200);
        exit("Webhook processed successfully");
    }

    private function verifyTransaction($transactionId)
    {
        try {
            $client = new Client();
            $response = $client->request("GET", "https://api.flutterwave.com/v3/transactions/{$transactionId}/verify", [
                "headers" => [
                    "Authorization" => "Bearer " . $this->secretKey,
                    "Content-Type" => "application/json"
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            return isset($data["status"]) && $data["status"] === "success";
        } catch (RequestException $e) {
            return false;
        }
    }

    private function updatePaymentStatus($orderRef, $amount, $currency)
    {
        // Retrieve OJS payment DAO
        $dao = DAORegistry::getDAO('OJSPluginPaymentDAO'); // Ensure this DAO exists
        if (!$dao) {
            error_log("OJS Payment DAO not found!");
            return;
        }

        // Mark the order as paid
        $dao->markOrderPaid($orderRef, $amount, $currency);

        error_log("Payment updated successfully for Order: $orderRef, Amount: $amount $currency");
    }
}
