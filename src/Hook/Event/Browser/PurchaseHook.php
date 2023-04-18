<?php

namespace PrestaShop\Module\TagConciergeFree\Hook\Event\Browser;

use Order as PrestaShopOrder;
use PrestaShop\Module\TagConciergeFree\Hook\AbstractHook;
use PrestaShop\Module\TagConciergeFree\Hook\Hooks;
use PrestaShop\Module\TagConciergeFree\Model\Order;

class PurchaseHook extends AbstractHook
{
    /** @var array */
    public const HOOKS = [
        Hooks::DISPLAY_ORDER_CONFIRMATION => [
            'addDataElementInOrderConfirmationPage',
        ],
    ];

    public function addDataElementInOrderConfirmationPage(array $data): string
    {
        /** @var PrestaShopOrder $orderObject */
        $orderObject = $data['order'];

        $order = Order::fromOrderObject($orderObject);

        $this->getContext()->smarty->assign('tc_order', $order->toArray());

        return $this->module->display(
            \TagConciergeFree::MODULE_FILE,
            'views/templates/hooks/purchase/display_order_confirmation.tpl'
        );
    }
}
