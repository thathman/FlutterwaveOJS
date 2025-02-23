<?php

namespace Workstation\FlutterwaveOjs;

use PKP\form\Form;
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
    }

    public function initData()
    {
        $contextId = \Application::get()->getRequest()->getContext()->getId();
        $this->setData('publicKey', $this->plugin->getSetting($contextId, 'publicKey'));
        $this->setData('secretKey', $this->plugin->getSetting($contextId, 'secretKey'));
        $this->setData('liveMode', $this->plugin->getSetting($contextId, 'liveMode'));
    }

    public function readInputData()
    {
        $this->readUserVars(['publicKey', 'secretKey', 'liveMode']);
    }

    public function execute(...$functionArgs)
    {
        $contextId = \Application::get()->getRequest()->getContext()->getId();

        // Save settings to the plugin
        $this->plugin->updateSetting($contextId, 'publicKey', $this->getData('publicKey'));
        $this->plugin->updateSetting($contextId, 'secretKey', $this->getData('secretKey'));
        $this->plugin->updateSetting($contextId, 'liveMode', $this->getData('liveMode'));
    }
}
