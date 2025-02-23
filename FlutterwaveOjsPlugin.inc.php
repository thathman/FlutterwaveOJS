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

        // Hook to add settings to OJS payment settings form
        HookRegistry::register('Form::config::before', [$this, 'addSettings']);

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
     * Hook to add settings to OJS Payments form.
     */
    public function addSettings($hookName, $form)
    {
        import('lib.pkp.classes.components.forms.context.PKPPaymentSettingsForm');
        if ($form->id !== FORM_PAYMENT_SETTINGS) {
            return;
        }

        $context = Application::get()->getRequest()->getContext();
        if (!$context) {
            return;
        }

        $form->addGroup([
            'id' => 'flutterwave',
            'label' => __('plugins.paymethod.flutterwave.displayName'),
            'showWhen' => 'paymentsEnabled',
        ])
        ->addField(new \PKP\components\forms\FieldText('publicKey', [
            'label' => __('plugins.paymethod.flutterwave.publicKey'),
            'value' => $this->getSetting($context->getId(), 'publicKey'),
            'groupId' => 'flutterwave',
        ]))
        ->addField(new \PKP\components\forms\FieldText('secretKey', [
            'label' => __('plugins.paymethod.flutterwave.secretKey'),
            'value' => $this->getSetting($context->getId(), 'secretKey'),
            'groupId' => 'flutterwave',
        ]))
        ->addField(new \PKP\components\forms\FieldOptions('liveMode', [
            'label' => __('plugins.paymethod.flutterwave.liveMode'),
            'options' => [
                ['value' => 1, 'label' => __('plugins.paymethod.flutterwave.liveMode')],
                ['value' => 0, 'label' => __('plugins.paymethod.flutterwave.sandboxMode')],
            ],
            'value' => (bool) $this->getSetting($context->getId(), 'liveMode'),
            'groupId' => 'flutterwave',
        ]));

        return;
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
     * Save plugin settings.
     */
    public function saveSettings(string $hookName, array $args)
    {
        // Extract request from arguments
        $request = $args[0];

        if (!$request instanceof \PKP\core\PKPRequest) {
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
            return new \PKP\core\JSONMessage(false, __('plugins.paymethod.flutterwave.error.noQueuedPaymentId'));
        }

        $paymentManager = \Application::getPaymentManager($context);
        $queuedPayment = $paymentManager->getQueuedPayment($queuedPaymentId);

        if (!$queuedPayment) {
            return new \PKP\core\JSONMessage(false, __('plugins.paymethod.flutterwave.error.invalidPayment'));
        }

        return $this->getPaymentForm($queuedPayment, $request);
    }
}
