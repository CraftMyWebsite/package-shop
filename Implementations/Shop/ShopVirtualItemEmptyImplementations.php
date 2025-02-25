<?php

namespace CMW\Implementation\Shop\Shop;

use CMW\Entity\Shop\Items\ShopItemEntity;
use CMW\Entity\Users\UserEntity;
use CMW\Interface\Shop\IVirtualItems;
use CMW\Manager\Lang\LangManager;

class ShopVirtualItemEmptyImplementations implements IVirtualItems
{
    public function name(): string
    {
        return LangManager::translate('shop.views.elements.virtual.empty.name');
    }

    public function varName(): string
    {
        return 'nothing';
    }

    public function documentationURL(): ?string
    {
        return null;
    }

    public function description(): ?string
    {
        return null;
    }

    public function includeItemConfigWidgets(?int $itemId): void
    {
        return;
    }

    public function execOnBuy(string $varName, ShopItemEntity $item, UserEntity $user): void
    {
        return;
    }

    public function execOnCancel(string $varName, ShopItemEntity $item, UserEntity $user): void
    {
        return;
    }
}
