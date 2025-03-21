<?php

namespace CMW\Entity\Shop\Items;

use CMW\Manager\Package\AbstractEntity;
use CMW\Utils\Date;

class ShopItemVariantEntity extends AbstractEntity
{
    private int $variantId;
    private ShopItemEntity $item;
    private string $variantName;
    private string $variantCreated;
    private string $variantUpdated;

    /**
     * @param int $variantId
     * @param \CMW\Entity\Shop\Items\ShopItemEntity $item
     * @param string $variantName
     * @param string $variantCreated
     * @param string $variantUpdated
     */
    public function __construct(int $variantId, ShopItemEntity $item, string $variantName, string $variantCreated, string $variantUpdated)
    {
        $this->variantId = $variantId;
        $this->item = $item;
        $this->variantName = $variantName;
        $this->variantCreated = $variantCreated;
        $this->variantUpdated = $variantUpdated;
    }

    public function getId(): int
    {
        return $this->variantId;
    }

    public function getItem(): ShopItemEntity
    {
        return $this->item;
    }

    public function getName(): string
    {
        return $this->variantName;
    }

    public function getCreated(): string
    {
        return Date::formatDate($this->variantCreated);
    }

    public function getUpdated(): string
    {
        return Date::formatDate($this->variantUpdated);
    }
}
