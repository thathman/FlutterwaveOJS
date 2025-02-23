<?php

namespace Workstation\FlutterwaveOjs;

use PKP\form\Form;
use PKP\context\Context;
use PKP\core\DAORegistry;
use PKP\core\Application;

class SettingsForm extends Form
{
    private $plugin;

    public function __construct($plugin)
    {
        parent::__construct($plugin->getTemplateResource('settingsForm.tpl'));

        $this->plugin = $plugin;
        
        // Add validation checks
        $this->addCheck(new \PKP\form\validation\FormValidator($this, 'publicKey', 'required', 'plugins.paymethod.flutterwave.error.publicKeyRequired'));
        $this->addCheck(new \PKP\form\validation\FormValidator($this, 'secretKey', 'required', 'plugins.paymethod.flutterwave.error.secretKeyRequired'));
        $this->addCheck(new \PKP\form\validation\FormValidator($this, 'encryptionKey', 'required', 'plugins.paymethod.flutterwave.error.encryptionKeyRequired'));
    }

    public function initData()
    {
        $contextId = Application::get()->getRequest()->getContext()->getId();
        $this->setData('publicKey', $this->plugin->getSetting($contextId, 'publicKey'));
        $this->setData('secretKey', $this->plugin->getSetting($contextId, 'secretKey'));
        $this->setData('encryptionKey', $this->plugin->getSetting($contextId, 'encryptionKey'));
        $this->setData('liveMode', $this->plugin->getSetting($contextId, 'liveMode'));
    }

    public function readInputData()
    {
        $this->readUserVars(['publicKey', 'secretKey', 'encryptionKey', 'liveMode']);
    }

    public function execute(...$functionArgs)
    {
        $contextId = Application::get()->getRequest()->getContext()->getId();

        $this->plugin->updateSetting($contextId, 'publicKey', $this->getData('publicKey'));
        $this->plugin->updateSetting($contextId, 'secretKey', $this->getData('secretKey'));
        $this->plugin->updateSetting($contextId, 'encryptionKey', $this->getData('encryptionKey'));
        $this->plugin->updateSetting($contextId, 'liveMode', $this->getData('liveMode'));

        // Validate API keys before saving
        $this->validateApiKeys($contextId);
    }

    private function validateApiKeys($contextId)
    {
        $publicKey = $this->plugin->getSetting($contextId, 'publicKey');
        $secretKey = $this->plugin->getSetting($contextId, 'secretKey');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.flutterwave.com/v3/banks/NG");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $secretKey"
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        $decodedResponse = json_decode($response, true);
        if (!isset($decodedResponse['status']) || $decodedResponse['status'] !== 'success') {
            throw new \Exception(__('plugins.paymethod.flutterwave.error.invalidApiKeys'));
        }
    }
}
