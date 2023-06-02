<?php

namespace PrestaShop\Module\TagConciergeFree\Hook\Event\Browser;

use Order as PrestaShopOrder;
use PrestaShop\Module\TagConciergeFree\Hook\AbstractHook;
use PrestaShop\Module\TagConciergeFree\Hook\Hooks;
use PrestaShop\Module\TagConciergeFree\Model\Order;

class PurchaseHook extends AbstractHook
{
    private $eventFired = false;

    /** @var array */
    public const HOOKS = [
        Hooks::DISPLAY_ORDER_CONFIRMATION => [
            'addDataElementInOrderConfirmationPage',
        ],
        Hooks::DISPLAY_BEFORE_BODY_CLOSING_TAG => [
            'p24Compatibility',
        ],
    ];

    public function addDataElementInOrderConfirmationPage(array $data): string
    {
        /** @var PrestaShopOrder $orderObject */
        $orderObject = $data['order'];

        return $this->handlePurchaseEvent($orderObject);
    }

    public function p24Compatibility(array $data): string
    {
        $controller = $this->getContext()->controller;

        if (null === $controller) {
            return '';
        }

        $controllerClass = get_class($controller);

        if ('Przelewy24paymentConfirmationModuleFrontController' !== $controllerClass) {
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
        if (true === $this->eventFired) {
            return '';
        }

        $orderModel = Order::fromOrderObject($order);

        $this->getContext()->smarty->assign('tc_order', $orderModel->toArray());

        $this->eventFired = true;

        return $this->module->display(
            \TagConciergeFree::MODULE_FILE,
            'views/templates/hooks/purchase/display_order_confirmation.tpl'
        );
    }
}
