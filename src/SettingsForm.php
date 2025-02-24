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
        $this->setData('testMode', $this->plugin->getSetting($contextId, 'testMode'));
    }

    public function readInputData()
    {
        $this->readUserVars(['publicKey', 'secretKey', 'encryptionKey', 'testMode']);
    }

    public function execute(...$functionArgs)
    {
        $contextId = Application::get()->getRequest()->getContext()->getId();

        $pluginSettingsDao = DAORegistry::getDAO('PluginSettingsDAO');
        $pluginSettingsDao->updateSetting($contextId, 'publicKey', $this->getData('publicKey'), 'string');
        $pluginSettingsDao->updateSetting($contextId, 'secretKey', $this->getData('secretKey'), 'string');
        $pluginSettingsDao->updateSetting($contextId, 'encryptionKey', $this->getData('encryptionKey'), 'string');
        $pluginSettingsDao->updateSetting($contextId, 'testMode', (bool) $this->getData('testMode'), 'bool');
    }
}
