<?php

namespace CMW\Implementation\Shop\Shop;

use CMW\Controller\Shop\Admin\Item\Virtual\ShopVirtualItemsGiftCodeController;
use CMW\Entity\Shop\Items\ShopItemEntity;
use CMW\Entity\Users\UserEntity;
use CMW\Interface\Shop\IVirtualItems;
use CMW\Manager\Lang\LangManager;

class ShopVirtualItemGiftCodeImplementations implements IVirtualItems
{
    public function name(): string
    {
        return LangManager::translate('shop.views.elements.virtual.giftCode.name');
    }

    public function varName(): string
    {
        return 'gift_code';
    }

    public function documentationURL(): ?string
    {
        return '';
    }

    public function description(): ?string
    {
        return LangManager::translate('shop.views.elements.virtual.giftCode.desc');
    }

    public function includeItemConfigWidgets(?int $itemId): void
    {
        return;
    }

    public function execOnBuy(string $varName, ShopItemEntity $item, UserEntity $user): void
    {
        ShopVirtualItemsGiftCodeController::getInstance()->sedMailWithGiftCode($item, $user);
    }

    public function execOnCancel(string $varName, ShopItemEntity $item, UserEntity $user): void
    {
        return;
    }
}
