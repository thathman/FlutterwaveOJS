<?php

import('lib.pkp.classes.plugins.PaymethodPlugin');

class FlutterwaveOjsPlugin extends PaymethodPlugin
{
    /**
     * Register the plugin.
     */
    public function register($category, $path, $mainContextId = null)
    {
        $success = parent::register($category, $path, $mainContextId);
        if (!$success) {
            return false;
        }

        // Load locale data
        $this->addLocaleData();

        return true;
    }

    /**
     * Get the internal plugin name.
     */
    public function getName()
    {
        return 'flutterwaveOJS'; // Must match folder name
    }

    /**
     * Get the plugin display name.
     */
    public function getDisplayName()
    {
        return __('plugins.paymethod.flutterwave.displayName');
    }

    /**
     * Get the plugin description.
     */
    public function getDescription()
    {
        return __('plugins.paymethod.flutterwave.description');
    }

    /**
     * Get the payment method name.
     */
    public function getPaymentMethodName()
    {
        return 'Flutterwave';
    }

    /**
     * Check if the plugin is configured.
     */
    public function isConfigured($contextId)
    {
        $publicKey = $this->getSetting($contextId, 'publicKey');
        $secretKey = $this->getSetting($contextId, 'secretKey');

        return !empty($publicKey) && !empty($secretKey);
    }

    /**
     * Get the template path.
     */
    public function getTemplatePath($inCore = false)
    {
        return $this->getPluginPath() . '/templates/';
    }

    /**
     * Get the payment form for users to complete transactions.
     */
    public function getPaymentForm($queuedPayment, $request)
    {
        $context = $request->getContext();
        $contextId = $context ? $context->getId() : CONTEXT_ID_NONE;

        $publicKey = $this->getSetting($contextId, 'publicKey');
        $secretKey = $this->getSetting($contextId, 'secretKey');
        $liveMode = $this->getSetting($contextId, 'liveMode');

        $amount = $queuedPayment->getAmount();
        $currency = $queuedPayment->getAmountCurrencyCode();
        $transactionId = $queuedPayment->getQueuedPaymentId();
        $callbackUrl = $request->url(null, 'payment', 'flutterwave', 'return');

        $apiEndpoint = $liveMode ? "https://api.flutterwave.com/v3/payments" : "https://api.flutterwave.com/v3/payments";

        return '
            <form action="' . $apiEndpoint . '" method="POST">
                <input type="hidden" name="public_key" value="' . htmlspecialchars($publicKey) . '">
                <input type="hidden" name="tx_ref" value="' . htmlspecialchars($transactionId) . '">
                <input type="hidden" name="amount" value="' . htmlspecialchars($amount) . '">
                <input type="hidden" name="currency" value="' . htmlspecialchars($currency) . '">
                <input type="hidden" name="redirect_url" value="' . htmlspecialchars($callbackUrl) . '">
                <button type="submit">Pay with Flutterwave</button>
            </form>
        ';
    }

    /**
     * Save plugin settings (Corrected method signature).
     */
    public function saveSettings(string $hookName, array $args)
    {
        // Extract request from arguments
        $request = $args[0];

        if (!$request instanceof PKPRequest) {
            return false;
        }

        $context = $request->getContext();
        if (!$context) {
            return false;
        }

        $contextId = $context->getId();

        // Save settings
        $this->updateSetting($contextId, 'publicKey', $request->getUserVar('publicKey'));
        $this->updateSetting($contextId, 'secretKey', $request->getUserVar('secretKey'));
        $this->updateSetting($contextId, 'liveMode', $request->getUserVar('liveMode') ? 1 : 0);

        return true;
    }

    /**
     * Handle payments.
     */
    public function handle($args, $request)
    {
        $context = $request->getContext();
        $queuedPaymentId = $request->getUserVar('queuedPaymentId');

        if (!$queuedPaymentId) {
            return new JSONMessage(false, __('plugins.paymethod.flutterwave.error.noQueuedPaymentId'));
        }

        $paymentManager = Application::getPaymentManager($context);
        $queuedPayment = $paymentManager->getQueuedPayment($queuedPaymentId);

        if (!$queuedPayment) {
            return new JSONMessage(false, __('plugins.paymethod.flutterwave.error.invalidPayment'));
        }

        return $this->getPaymentForm($queuedPayment, $request);
    }
}
