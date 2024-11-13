<?php

namespace CMW\Entity\Shop\Carts;

use CMW\Manager\Package\AbstractEntity;
use CMW\Utils\Date;
use CMW\Entity\Shop\Items\ShopItemVariantValueEntity;

class ShopCartVariantesEntity extends AbstractEntity
{
    private int $cartVariantId;
    private ShopCartItemEntity $cartId;
    private ShopItemVariantValueEntity $variantValueId;
    private string $cartVariantCreated;
    private string $cartVariantUpdated;

    /**
     * @param int $cartVariantId
     * @param \CMW\Entity\Shop\Carts\ShopCartItemEntity $cartId
     * @param \CMW\Entity\Shop\Items\ShopItemVariantValueEntity $variantValueId
     * @param string $cartVariantCreated
     * @param string $cartVariantUpdated
     */
    public function __construct(int $cartVariantId, ShopCartItemEntity $cartId, ShopItemVariantValueEntity $variantValueId, string $cartVariantCreated, string $cartVariantUpdated)
    {
        $this->cartVariantId = $cartVariantId;
        $this->cartId = $cartId;
        $this->variantValueId = $variantValueId;
        $this->cartVariantCreated = $cartVariantCreated;
        $this->cartVariantUpdated = $cartVariantUpdated;
    }

    public function getId(): int
    {
        return $this->cartVariantId;
    }

    public function getCart(): ShopCartItemEntity
    {
        return $this->cartId;
    }

    public function getVariantValue(): ShopItemVariantValueEntity
    {
        return $this->variantValueId;
    }

    public function getCreated(): string
    {
        return Date::formatDate($this->cartVariantCreated);
    }

    public function getUpdated(): string
    {
        return Date::formatDate($this->cartVariantUpdated);
    }
}
