<?php

namespace CMW\Implementation\Shop\Shop;

use CMW\Controller\Shop\Admin\Item\Virtual\ShopVirtualItemsGiftCodeController;
use CMW\Entity\Shop\Items\ShopItemEntity;
use CMW\Entity\Users\UserEntity;
use CMW\Interface\Shop\IGlobalConfig;
use CMW\Interface\Shop\IVirtualItems;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;

class ShopGlobalConfigReviewReminderImplementations implements IGlobalConfig
{
    public function name(): string
    {
        return LangManager::translate('shop.views.elements.global.reviewReminder.name');
    }

    public function varName(): string
    {
        return 'review_reminder';
    }

    public function includeGlobalConfigWidgets(): void
    {
        $varName = $this->varName();
        require_once EnvManager::getInstance()->getValue('DIR') . 'App/Package/Shop/Views/Elements/Global/reviewReminder.config.inc.view.php';
    }
}
