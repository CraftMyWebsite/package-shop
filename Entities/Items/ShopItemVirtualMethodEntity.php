<?php

namespace CMW\Entity\Shop\Items;

use CMW\Manager\Package\AbstractEntity;
use CMW\Utils\Date;
use CMW\Interface\Shop\IVirtualItems;

class ShopItemVirtualMethodEntity extends AbstractEntity
{
    private int $virtualMethodId;
    private IVirtualItems $virtualMethod;
    private ShopItemEntity $shopItem;
    private string $virtualMethodCreated;
    private string $virtualMethodUpdated;

    /**
     * @param int $virtualMethodId
     * @param \CMW\Interface\Shop\IVirtualItems $virtualMethod
     * @param \CMW\Entity\Shop\Items\ShopItemEntity $shopItem
     * @param string $virtualMethodCreated
     * @param string $virtualMethodUpdated
     */
    public function __construct(int $virtualMethodId, IVirtualItems $virtualMethod, ShopItemEntity $shopItem, string $virtualMethodCreated, string $virtualMethodUpdated)
    {
        $this->virtualMethodId = $virtualMethodId;
        $this->virtualMethod = $virtualMethod;
        $this->shopItem = $shopItem;
        $this->virtualMethodCreated = $virtualMethodCreated;
        $this->virtualMethodUpdated = $virtualMethodUpdated;
    }

    public function getId(): int
    {
        return $this->virtualMethodId;
    }

    public function getVirtualMethod(): IVirtualItems
    {
        return $this->virtualMethod;
    }

    public function getShopItem(): ShopItemEntity
    {
        return $this->shopItem;
    }

    public function getCreated(): string
    {
        return Date::formatDate($this->virtualMethodCreated);
    }

    public function getUpdated(): string
    {
        return Date::formatDate($this->virtualMethodUpdated);
    }
}
