<?php

namespace PrestaShop\Module\TagConciergeFree\Hook\Event\Browser;

use Order as PrestaShopOrder;
use PrestaShop\Module\TagConciergeFree\Hook\Event\AbstractEcommerceEventHook;
use PrestaShop\Module\TagConciergeFree\Hook\Hooks;
use PrestaShop\Module\TagConciergeFree\Model\Order;

class PurchaseHook extends AbstractEcommerceEventHook
{
    protected $eventName = 'purchase';

    protected $eventType = 'browser';

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
        if (true === $this->isP24ConfirmationPage() || true === $this->isP24SuccessPage()) {
            return '';
        }

        /** @var PrestaShopOrder $orderObject */
        $orderObject = $data['order'];

        return $this->handlePurchaseEvent($orderObject);
    }

    public function p24Compatibility(array $data): string
    {
        if (false === $this->isP24ConfirmationPage() && false === $this->isP24SuccessPage()) {
            return '';
        }

        $cartId = $this->isP24ConfirmationPage() ? $this->getContext()->cart->id : $this->getContext()->cookie->tc_cart_id;

        if (true === $this->isP24ConfirmationPage()) {
            $this->getContext()->cookie->tc_cart_id = $cartId;
        }

        $orderId = PrestaShopOrder::getIdByCartId($cartId);

        if (false === $orderId) {
            return '';
        }

        if (true === $this->isP24SuccessPage()) {
            $this->getContext()->cookie->tc_cart_id = null;
        }

        $order = new PrestaShopOrder($orderId);

        return $this->handlePurchaseEvent($order);
    }

    private function handlePurchaseEvent(PrestaShopOrder $order): string
    {
        if ($order->id === (int) $this->getContext()->cookie->tc_tracked_purchase_id) {
            return '';
        }

        $orderModel = Order::fromOrderObject($order);

        $this->getContext()->smarty->assign('tc_order', $orderModel->toArray());

        $this->getContext()->cookie->tc_tracked_purchase_id = $order->id;

        return $this->module->render('hooks/purchase/display_order_confirmation.tpl');
    }

    private function isP24ConfirmationPage(): bool
    {
        return 'przelewy24paymentconfirmationmodulefrontcontroller' === strtolower($this->getControllerClass());
    }

    private function isP24SuccessPage(): bool
    {
        return 'przelewy24paymentsuccessfulmodulefrontcontroller' === strtolower($this->getControllerClass());
    }

    private function getControllerClass(): string
    {
        $controller = $this->getContext()->controller;

        if (null === $controller) {
            return '';
        }

        return get_class($controller);
    }
}
