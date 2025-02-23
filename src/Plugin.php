<?php

namespace Workstation\FlutterwaveOjs;

use PKP\payment\PaymentPlugin;
use PKP\plugins\HookRegistry;
use PKP\core\Application;

class Plugin extends PaymentPlugin
{
    public function register($category, $path, $mainContextId)
    {
        if (parent::register($category, $path, $mainContextId)) {
            HookRegistry::register('LoadHandler', function ($hookName, $params) {
                $page = $params[0];
                $op = $params[1];

                if ($page === 'flutterwave' && $op === 'webhook') {
                    define('HANDLER_CLASS', WebhookHandler::class);
                    return true;
                }
                return false;
            });

            return true;
        }
        return false;
    }

    public function getName()
    {
        return 'flutterwaveOJS';
    }

    public function getDisplayName()
    {
        return __('plugins.paymethod.flutterwave.displayName');
    }

    public function getDescription()
    {
        return __('plugins.paymethod.flutterwave.description');
    }

    public function getSettingsForm($context)
    {
        return new SettingsForm($this);
    }

    public function isConfigured($context)
    {
        $contextId = $context->getId();
        return !empty($this->getSetting($contextId, 'publicKey')) &&
               !empty($this->getSetting($contextId, 'secretKey')) &&
               !empty($this->getSetting($contextId, 'liveMode'));
    }

    public function createPaymentHandler()
    {
        return new PaymentHandler($this);
    }
}
