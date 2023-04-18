<?php

namespace PrestaShop\Module\TagConciergeFree\Model;

use Cart as PrestaShopCart;
use Order as PrestaShopOrder;

class Order
{
    /** @var int */
    private $id;

    /** @var string */
    private $status;

    /** @var string */
    private $affiliation;

    /** @var string */
    private $paymentMethod;

    /** @var string */
    private $currency;

    /** @var float */
    private $value;

    /** @var float */
    private $tax;

    /** @var float */
    private $shipping;

    /** @var string */
    private $coupon;

    /** @var OrderProduct[] */
    private $products;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return $this
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getAffiliation(): string
    {
        return $this->affiliation;
    }

    public function setAffiliation(string $affiliation): self
    {
        $this->affiliation = $affiliation;

        return $this;
    }

    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }

    /**
     * @return $this
     */
    public function setPaymentMethod(string $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function setValue(float $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getTax(): float
    {
        return $this->tax;
    }

    public function setTax(float $tax): self
    {
        $this->tax = $tax;

        return $this;
    }

    public function getShipping(): float
    {
        return $this->shipping;
    }

    public function setShipping(float $shipping): self
    {
        $this->shipping = $shipping;

        return $this;
    }

    public function getCoupon(): string
    {
        return $this->coupon;
    }

    public function setCoupon(string $coupon): self
    {
        $this->coupon = $coupon;

        return $this;
    }

    /**
     * @return OrderProduct[]
     */
    public function getProducts(): array
    {
        return $this->products;
    }

    /**
     * @param OrderProduct[] $products
     */
    public function setProducts(array $products): self
    {
        $this->products = $products;

        return $this;
    }

    public function addProduct(OrderProduct $product): self
    {
        $this->products[] = $product;

        return $this;
    }

    /**
     * @return static
     *
     * @throws \Exception
     */
    public static function fromOrderObject(PrestaShopOrder $orderObject): self
    {
        $order = new self();
        $context = \Context::getContext();

        $cartObject = new PrestaShopCart(
            PrestaShopOrder::getCartIdStatic($orderObject->id, $context->customer->id)
        );

        $coupons = [];
        foreach ($cartObject->getCartRules() as $cartRule) {
            $coupons[] = $cartRule['name'];
        }

        $orderState = new \OrderState($orderObject->current_state);

        $order
            ->setId($orderObject->id)
            ->setStatus($orderState->name[1])
            ->setAffiliation(\Configuration::get('PS_SHOP_NAME'))
            ->setPaymentMethod($orderObject->payment)
            ->setCurrency($context->currency->iso_code)
            ->setValue((float) $cartObject->getOrderTotal(true))
            ->setTax(((float) $cartObject->getOrderTotal(true)) - ((float) $cartObject->getOrderTotal(false)))
            ->setShipping((float) $cartObject->getOrderTotal(true, PrestaShopCart::ONLY_SHIPPING))
            ->setCoupon(implode('|', $coupons));

        foreach ($orderObject->getCartProducts() as $product) {
            $order->addProduct(OrderProduct::fromArray($product));
        }

        return $order;
    }

    public function toArray(): array
    {
        $products = array_map(static function (OrderProduct $product) {
            return $product->toArray();
        }, $this->getProducts());

        return [
            'id' => $this->getId(),
            'status' => $this->getStatus(),
            'affiliation' => $this->getAffiliation(),
            'payment_method' => $this->getPaymentMethod(),
            'currency' => $this->getCurrency(),
            'value' => $this->getValue(),
            'tax' => round($this->getTax(), 2),
            'shipping' => $this->getShipping(),
            'coupon' => $this->getCoupon(),
            'products' => $products,
        ];
    }
}
