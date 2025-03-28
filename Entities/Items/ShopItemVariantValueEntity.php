<?php

namespace CMW\Entity\Shop\Items;

use CMW\Manager\Package\AbstractEntity;
use CMW\Utils\Date;

class ShopItemVariantValueEntity extends AbstractEntity
{
    private int $variantValueId;
    private ShopItemVariantEntity $variant;
    private string $variantValue;
    private string $variantValueCreated;
    private string $variantValueUpdated;

    /**
     * @param int $variantValueId
     * @param \CMW\Entity\Shop\Items\ShopItemVariantEntity $variant
     * @param string $variantValue
     * @param string $variantValueCreated
     * @param string $variantValueUpdated
     */
    public function __construct(int $variantValueId, ShopItemVariantEntity $variant, string $variantValue, string $variantValueCreated, string $variantValueUpdated)
    {
        $this->variantValueId = $variantValueId;
        $this->variant = $variant;
        $this->variantValue = $variantValue;
        $this->variantValueCreated = $variantValueCreated;
        $this->variantValueUpdated = $variantValueUpdated;
    }

    public function getId(): int
    {
        return $this->variantValueId;
    }

    public function getVariant(): ShopItemVariantEntity
    {
        return $this->variant;
    }

    public function getValue(): string
    {
        return $this->variantValue;
    }

    public function getCreated(): string
    {
        return Date::formatDate($this->variantValueCreated);
    }

    public function getUpdated(): string
    {
        return Date::formatDate($this->variantValueUpdated);
    }
}
