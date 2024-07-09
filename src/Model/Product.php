<?php

namespace PrestaShop\Module\TagConciergeFree\Model;

use ArrayAccess;
use Category;
use Context;
use Manufacturer;
use Tools;

class Product
{
    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var float */
    private $price;

    /** @var string */
    private $brand;

    /** @var string */
    private $category;

    /** @var string */
    private $variant;

    /** @var int */
    private $variantId;

    /** @var int */
    private $minimalQuantity;

    /** @var int */
    private $stockQuantity;

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param $id
     *
     * @return $this
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @return $this
     */
    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getBrand(): string
    {
        return $this->brand;
    }

    /**
     * @return $this
     */
    public function setBrand(string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * @return $this
     */
    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getVariant(): string
    {
        return $this->variant;
    }

    /**
     * @return $this
     */
    public function setVariant(string $variant): self
    {
        $this->variant = $variant;

        return $this;
    }

    public function getStockQuantity(): int
    {
        return $this->stockQuantity;
    }

    /**
     * @return $this
     */
    public function setStockQuantity(int $stockQuantity): self
    {
        $this->stockQuantity = $stockQuantity;

        return $this;
    }

    public function getMinimalQuantity(): int
    {
        return $this->minimalQuantity;
    }

    /**
     * @return $this
     */
    public function setMinimalQuantity(int $minimalQuantity): self
    {
        $this->minimalQuantity = $minimalQuantity;

        return $this;
    }

    public function getVariantId(): int
    {
        return $this->variantId;
    }

    public function setVariantId(int $variantId): self
    {
        $this->variantId = $variantId;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'price' => $this->getPrice(),
            'brand' => $this->getBrand(),
            'category' => $this->getCategory(),
            'variant' => $this->getVariant(),
            'variant_id' => $this->getVariantId(),
            'stock_quantity' => $this->getStockQuantity(),
            'minimal_quantity' => $this->getMinimalQuantity(),
        ];
    }

    public static function fromProductLazyArray($productLazyArray): self
    {
        return static::fromArray($productLazyArray);
    }

    /**
     * @param ArrayAccess|array $array
     */
    public static function fromArray($array): self
    {
        $context = Context::getContext();
        $category = new Category($array['id_category_default'], $context->language->id);
        $manufacturer = new Manufacturer($array['id_manufacturer']);

        if (false === isset($array['attributes'])) {
            $array['attributes'] = '';
        }

        if (false === isset($array['id_product_attribute']) || false === is_int($array['id_product_attribute'])) {
            $array['id_product_attribute'] = 0;
        }

        if (true === \is_array($array['attributes'])) {
            $attributes = array_map(static function ($attribute) {
                return Tools::strtolower(trim(
                    sprintf('%s_%s', $attribute['group'], $attribute['name'])
                ));
            }, $array['attributes']);

            $variant = implode('___', $attributes);
        } else {
            $variant = Tools::strtolower(trim(
                str_replace([' : ', '- '], ['_', '___'], $array['attributes'])
            ));
        }

        $calledClass = get_called_class();

        return (new $calledClass())
            ->setId($array['id_product'])
            ->setName(Tools::replaceAccentedChars($array['name']))
            ->setPrice((float) ($array['price_amount'] ?? $array['price']))
            ->setBrand($manufacturer->name ?? '')
            ->setCategory($category->name ?? '')
            ->setVariant($variant)
            ->setVariantId($array['id_product_attribute'])
            ->setStockQuantity($array['quantity'])
            ->setMinimalQuantity($array['minimal_quantity']);
    }
}
