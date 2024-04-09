<?php

namespace CMW\Implementation\Shop;


use CMW\Controller\Shop\Admin\Item\Virtual\ShopVirtualItemsDownloadableController;
use CMW\Entity\Shop\Items\ShopItemEntity;
use CMW\Entity\Users\UserEntity;
use CMW\Interface\Shop\IVirtualItems;
use CMW\Manager\Env\EnvManager;

class ShopVirtualItemEmptyImplementations implements IVirtualItems
{
    public function name(): string
    {
        return "Aucun";
    }

    public function varName(): string
    {
        return "nothing";
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

    public function includeGlobalConfigWidgets(): void {}

    public function useGlobalConfigWidgetsInShopConfig(): bool
    {
        return false;
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