<?php

namespace PrestaShop\Module\TagConciergeFree\Model;

class OrderProduct extends Product
{
    /** @var int */
    private $orderQuantity;

    public function getOrderQuantity(): int
    {
        return $this->orderQuantity;
    }

    /**
     * @return $this
     */
    public function setOrderQuantity(int $orderQuantity): self
    {
        $this->orderQuantity = $orderQuantity;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        $array = parent::toArray();
        $array['order_quantity'] = $this->getOrderQuantity();

        return $array;
    }

    /**
     * @param \ArrayAccess|array $array
     *
     * @return OrderProduct
     */
    public static function fromArray($array): Product
    {
        $array['name'] = $array['product_name'];

        /** @var OrderProduct $orderProduct */
        $orderProduct = parent::fromArray($array);

        return $orderProduct
            ->setStockQuantity($array['current_stock'])
            ->setOrderQuantity($array['cart_quantity'])
            ->setPrice($array['unit_price_tax_incl']);
    }
}
