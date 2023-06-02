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
        Hooks::DISPLAY_AFTER_BODY_OPENING_TAG => [
            'p24Compatibility',
        ],
    ];

    public function addDataElementInOrderConfirmationPage(array $data): string
    {
        if (true === $this->isP24ConfirmationPage()) {
            return '';
        }

        /** @var PrestaShopOrder $orderObject */
        $orderObject = $data['order'];

        return $this->handlePurchaseEvent($orderObject);
    }

    public function p24Compatibility(array $data): string
    {
        if (false === $this->isP24ConfirmationPage()) {
            return '';
        }

        $orderId = PrestaShopOrder::getIdByCartId($this->getContext()->cart->id);

        if (false === $orderId) {
            return '';
        }

        $order = new PrestaShopOrder($orderId);

        return $this->handlePurchaseEvent($order);
    }

    private function handlePurchaseEvent(PrestaShopOrder $order): string
    {
        $orderModel = Order::fromOrderObject($order);

        $this->getContext()->smarty->assign('tc_order', $orderModel->toArray());

        return $this->module->display(
            \TagConciergeFree::MODULE_FILE,
            'views/templates/hooks/purchase/display_order_confirmation.tpl'
        );
    }

    private function isP24ConfirmationPage(): bool
    {
        $controller = $this->getContext()->controller;

        if (null === $controller) {
            return false;
        }

        $controllerClass = get_class($controller);

        return 'przelewy24paymentconfirmationmodulefrontcontroller' === strtolower($controllerClass);
    }
}
