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

        // Hook to inject settings into Payment Settings
        HookRegistry::register('Form::config::before', [$this, 'addSettings']);

        return true;
    }

    /**
     * Get the internal plugin name.
     */
    public function getName()
    {
        return 'flutterwaveOJS';
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
     * Check if the plugin is configured.
     */
    public function isConfigured($contextId)
    {
        return !empty($this->getSetting($contextId, 'publicKey')) &&
               !empty($this->getSetting($contextId, 'secretKey')) &&
               !empty($this->getSetting($contextId, 'encryptionKey'));
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

        $contextId = $context->getId();

        $form->addGroup([
            'id' => 'flutterwave',
            'label' => __('plugins.paymethod.flutterwave.displayName'),
            'showWhen' => 'paymentsEnabled',
        ])
        ->addField(new \PKP\components\forms\FieldText('publicKey', [
            'label' => __('plugins.paymethod.flutterwave.publicKey'),
            'value' => $this->getSetting($contextId, 'publicKey') ?? '',
            'groupId' => 'flutterwave',
        ]))
        ->addField(new \PKP\components\forms\FieldText('secretKey', [
            'label' => __('plugins.paymethod.flutterwave.secretKey'),
            'value' => $this->getSetting($contextId, 'secretKey') ?? '',
            'groupId' => 'flutterwave',
        ]))
        ->addField(new \PKP\components\forms\FieldText('encryptionKey', [
            'label' => __('plugins.paymethod.flutterwave.encryptionKey'),
            'value' => $this->getSetting($contextId, 'encryptionKey') ?? '',
            'groupId' => 'flutterwave',
        ]))
        ->addField(new \PKP\components\forms\FieldOptions('testMode', [
            'label' => __('plugins.paymethod.flutterwave.enableTestMode'),
            'options' => [
                ['value' => 1, 'label' => __('common.enable')],
            ],
            'value' => (bool) $this->getSetting($contextId, 'testMode'),
            'groupId' => 'flutterwave',
        ]));

        return;
    }

    /**
     * Save plugin settings.
     */
    public function saveSettings(string $hookName, array $args)
    {
        $request = $args[0];

        if (!$request instanceof \PKP\core\PKPRequest) {
            return false;
        }

        $context = $request->getContext();
        if (!$context || !method_exists($context, 'getId')) {
            return false;
        }

        $contextId = (int) $context->getId();

        // Get input data
        $publicKey = $request->getUserVar('publicKey');
        $secretKey = $request->getUserVar('secretKey');
        $encryptionKey = $request->getUserVar('encryptionKey');
        $testMode = (bool) $request->getUserVar('testMode');

        // Save settings properly
        $pluginSettingsDao = DAORegistry::getDAO('PluginSettingsDAO');
        $pluginSettingsDao->updateSetting($contextId, 'publicKey', $publicKey, 'string');
        $pluginSettingsDao->updateSetting($contextId, 'secretKey', $secretKey, 'string');
        $pluginSettingsDao->updateSetting($contextId, 'encryptionKey', $encryptionKey, 'string');
        $pluginSettingsDao->updateSetting($contextId, 'testMode', $testMode, 'bool');

        return true;
    }

    /**
     * Get the payment form for users to complete transactions.
     */
    public function getPaymentForm($context, $queuedPayment)
    {
        $contextId = $context->getId();
        $publicKey = $this->getSetting($contextId, 'publicKey');
        $amount = $queuedPayment->getAmount();
        $currency = $queuedPayment->getAmountCurrencyCode();
        $transactionId = $queuedPayment->getQueuedPaymentId();
        $callbackUrl = $context->getUrl() . '/payment/flutterwave/return';

        return '
            <form action="https://api.flutterwave.com/v3/payments" method="POST">
                <input type="hidden" name="public_key" value="' . htmlspecialchars($publicKey) . '">
                <input type="hidden" name="amount" value="' . htmlspecialchars($amount) . '">
                <input type="hidden" name="currency" value="' . htmlspecialchars($currency) . '">
                <input type="hidden" name="tx_ref" value="' . htmlspecialchars($transactionId) . '">
                <input type="hidden" name="redirect_url" value="' . htmlspecialchars($callbackUrl) . '">
                <button type="submit">Pay with Flutterwave</button>
            </form>
        ';
    }

    /**
     * Handle payment requests.
     */
    public function handle($args, $request)
    {
        return null; // Placeholder function for proper class implementation
    }
}
