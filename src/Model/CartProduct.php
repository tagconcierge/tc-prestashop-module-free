<?php

namespace PrestaShop\Module\TagConciergeFree\Model;

class CartProduct extends Product
{
    /** @var int */
    private $cartQuantity;

    public function getCartQuantity(): int
    {
        return $this->cartQuantity;
    }

    /**
     * @return $this
     */
    public function setCartQuantity(int $cartQuantity): self
    {
        $this->cartQuantity = $cartQuantity;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        $array = parent::toArray();
        $array['cart_quantity'] = $this->getCartQuantity();

        return $array;
    }

    /**
     * @param \ArrayAccess|array $array
     *
     * @return CartProduct
     */
    public static function fromArray($array): Product
    {
        /** @var CartProduct $cartProduct */
        $cartProduct = parent::fromArray($array);

        return $cartProduct
            ->setStockQuantity($array['stock_quantity'])
            ->setCartQuantity($array['cart_quantity'])
        ;
    }
}
