<?php

namespace PrestaShop\Module\TagConciergeFree\Hook\Event\Browser;

use PrestaShop\Module\TagConciergeFree\Hook\AbstractHook;
use PrestaShop\Module\TagConciergeFree\Hook\Event\AbstractEcommerceEventHook;
use PrestaShop\Module\TagConciergeFree\Hook\Hooks;

class AddToCartHook extends AbstractEcommerceEventHook
{
    protected $eventName = 'add_to_cart';

    protected $eventType = 'browser';

    /** @var array */
    public const HOOKS = [
        Hooks::DISPLAY_BEFORE_BODY_CLOSING_TAG => [
            'loadScript',
        ],
    ];

    public function loadScript(): string
    {
        return $this->module->display(
            \TagConciergeFree::MODULE_FILE,
            'views/templates/hooks/add_to_cart/display_before_body_closing_tag.tpl'
        );
    }
}
