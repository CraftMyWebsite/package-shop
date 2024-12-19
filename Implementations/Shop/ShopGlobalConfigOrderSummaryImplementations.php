<?php

namespace CMW\Implementation\Shop\Shop;

use CMW\Controller\Shop\Admin\Item\Virtual\ShopVirtualItemsGiftCodeController;
use CMW\Entity\Shop\Items\ShopItemEntity;
use CMW\Entity\Users\UserEntity;
use CMW\Interface\Shop\IGlobalConfig;
use CMW\Interface\Shop\IVirtualItems;
use CMW\Manager\Env\EnvManager;

class ShopGlobalConfigOrderSummaryImplementations implements IGlobalConfig
{
    public function name(): string
    {
        return 'Récap de commandes';
    }

    public function varName(): string
    {
        return 'order_summary';
    }

    public function includeGlobalConfigWidgets(): void
    {
        $varName = $this->varName();
        require_once EnvManager::getInstance()->getValue('DIR') . 'App/Package/Shop/Views/Elements/Global/orderSummary.config.inc.view.php';
    }
}
