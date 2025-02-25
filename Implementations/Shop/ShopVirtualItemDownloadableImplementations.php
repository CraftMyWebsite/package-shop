<?php

namespace CMW\Implementation\Shop\Shop;

use CMW\Controller\Shop\Admin\Item\Virtual\ShopVirtualItemsDownloadableController;
use CMW\Entity\Shop\Items\ShopItemEntity;
use CMW\Entity\Users\UserEntity;
use CMW\Interface\Shop\IVirtualItems;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;

class ShopVirtualItemDownloadableImplementations implements IVirtualItems
{
    public function name(): string
    {
        return LangManager::translate('shop.views.elements.virtual.download.name');
    }

    public function varName(): string
    {
        return 'downloadable';
    }

    public function documentationURL(): ?string
    {
        return null;
    }

    public function description(): ?string
    {
        return LangManager::translate('shop.views.elements.virtual.download.desc');
    }

    public function includeItemConfigWidgets(?int $itemId): void
    {
        $varName = $this->varName();
        require_once EnvManager::getInstance()->getValue('DIR') . 'App/Package/Shop/Views/Elements/Virtual/Item/downloadable.config.inc.view.php';
    }

    public function execOnBuy(string $varName, ShopItemEntity $item, UserEntity $user): void
    {
        ShopVirtualItemsDownloadableController::getInstance()->sedMailWithDownloadLink($varName, $item, $user);
    }

    public function execOnCancel(string $varName, ShopItemEntity $item, UserEntity $user): void
    {
        return;
    }
}
