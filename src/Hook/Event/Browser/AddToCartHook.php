<?php

namespace PrestaShop\Module\TagConciergeFree\Hook\Event\Browser;

use PrestaShop\Module\TagConciergeFree\Hook\Event\AbstractEcommerceEventHook;
use PrestaShop\Module\TagConciergeFree\Hook\Hooks;
use PrestaShop\Module\TagConciergeFree\ValueObject\EcommerceEventVO;

class AddToCartHook extends AbstractEcommerceEventHook
{
    protected $eventName = EcommerceEventVO::ADD_TO_CART;

    protected $eventType = EcommerceEventVO::BROWSER_SIDE;

    /** @var array */
    public const HOOKS = [
        Hooks::DISPLAY_BEFORE_BODY_CLOSING_TAG => [
            'loadScript',
        ],
    ];

    public function loadScript(): string
    {
        return $this->module->render('hooks/add_to_cart/display_before_body_closing_tag.tpl');
    }
}
