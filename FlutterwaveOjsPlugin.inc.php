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
        $encryptionKey = $this->getSetting($contextId, 'encryptionKey');

        return !empty($publicKey) && !empty($secretKey) && !empty($encryptionKey);
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
        ->addField(new \PKP\components\forms\FieldText('encryptionKey', [
            'label' => __('plugins.paymethod.flutterwave.encryptionKey'),
            'value' => $this->getSetting($context->getId(), 'encryptionKey'),
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
        if (!$context) {
            return false;
        }

        $contextId = $context->getId();

        $this->updateSetting($contextId, 'publicKey', $request->getUserVar('publicKey'));
        $this->updateSetting($contextId, 'secretKey', $request->getUserVar('secretKey'));
        $this->updateSetting($contextId, 'encryptionKey', $request->getUserVar('encryptionKey'));
        $this->updateSetting($contextId, 'liveMode', $request->getUserVar('liveMode') ? 1 : 0);

        return $this->validateApiKeys($contextId);
    }

    public function validateApiKeys($contextId)
    {
        $publicKey = $this->getSetting($contextId, 'publicKey');
        $secretKey = $this->getSetting($contextId, 'secretKey');

        $url = "https://api.flutterwave.com/v3/banks/NG";
        $headers = [
            "Authorization: Bearer " . $secretKey,
            "Content-Type: application/json"
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode == 200) {
            return new \PKP\core\JSONMessage(true, "API Keys are valid!");
        } else {
            return new \PKP\core\JSONMessage(false, "Invalid API Keys. Please check your credentials.");
        }
    }
}
