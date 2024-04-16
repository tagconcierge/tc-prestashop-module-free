<?php

namespace PrestaShop\Module\TagConciergeFree\Hook\Event;

use Configuration;
use PrestaShop\Module\TagConciergeFree\Hook\AbstractHook;

abstract class AbstractEcommerceEventHook extends AbstractHook
{
    protected $eventName = '';

    protected $eventType = '';

    public function isEnabled(): bool
    {
        $configurationKey = sprintf('TC_EVENT_STATE_%s_%s', strtoupper($this->eventType), strtoupper($this->eventName));

        return false === Configuration::hasKey($configurationKey) || '1' === Configuration::get($configurationKey);
    }
}
