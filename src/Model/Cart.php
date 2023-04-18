<?php

namespace PrestaShop\Module\TagConciergeFree\Model;

use Cart as PrestaShopCart;

class Cart
{
    /** @var int */
    private $id;

    /** @var float */
    private $value;

    /** @var CartProduct[] */
    private $products = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return $this
     */
    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * @return $this
     */
    public function setValue(float $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return CartProduct[]
     */
    public function getProducts(): array
    {
        return $this->products;
    }

    /**
     * @param CartProduct[] $products
     *
     * @return $this
     */
    public function setProducts(array $products): self
    {
        $this->products = $products;
    }

    /**
     * @return $this
     */
    public function addProduct(CartProduct $product): self
    {
        $this->products[] = $product;

        return $this;
    }

    public function getChecksum(): string
    {
        return hash('sha512', serialize($this));
    }

    /**
     * @param PrestaShopCart $cartObject
     *
     * @return Cart
     *
     * @throws \Exception
     */
    public static function fromCartObject(PrestaShopCart $cartObject): self
    {
        $cart = new self();

        $cart
            ->setId($cartObject->id)
            ->setValue((float) $cartObject->getOrderTotal(false, PrestaShopCart::BOTH_WITHOUT_SHIPPING))
        ;

        foreach ($cartObject->getProducts() as $product) {
            $cart->addProduct(CartProduct::fromArray($product));
        }

        return $cart;
    }

    public function toArray(): array
    {
        $products = array_map(static function (CartProduct $product) {
            return $product->toArray();
        }, $this->getProducts());

        return [
            'id' => $this->getId(),
            'value' => $this->getValue(),
            'checksum' => $this->getChecksum(),
            'products' => $products,
        ];
    }
}
