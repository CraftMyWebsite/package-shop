<?php

namespace CMW\Entity\Shop\Items;

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Package\AbstractEntity;
use CMW\Utils\Date;

class ShopItemVariantValueEntity extends AbstractEntity
{
    private int $variantValueId;
    private ShopItemVariantEntity $variant;
    private string $variantValue;
    private ?string $variantImage;
    private string $variantValueCreated;
    private string $variantValueUpdated;

    /**
     * @param int $variantValueId
     * @param \CMW\Entity\Shop\Items\ShopItemVariantEntity $variant
     * @param string $variantValue
     * @param ?string $variantImage
     * @param string $variantValueCreated
     * @param string $variantValueUpdated
     */
    public function __construct(int $variantValueId, ShopItemVariantEntity $variant, string $variantValue, ?string $variantImage, string $variantValueCreated, string $variantValueUpdated)
    {
        $this->variantValueId = $variantValueId;
        $this->variant = $variant;
        $this->variantValue = $variantValue;
        $this->variantImage = $variantImage;
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

    public function getImage(): ?string
    {
        return $this->variantImage ?? null;
    }

    /**
     * @return ?string
     */
    public function getImageLink(): ?string
    {
        if ($this->variantImage) {
            return EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'Public/Uploads/Shop/Variants/' . $this->variantImage;
        }
        return null;
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
