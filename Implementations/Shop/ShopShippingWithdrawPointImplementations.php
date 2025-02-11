<?php

namespace CMW\Implementation\Shop\Shop;

use CMW\Controller\Shop\Admin\Item\Shipping\ShopShippingNotifyWithdrawController;
use CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity;
use CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersItemsEntity;
use CMW\Entity\Users\UserEntity;
use CMW\Interface\Shop\IShippingMethod;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;

class ShopShippingWithdrawPointImplementations implements IShippingMethod
{
    public function name(): string
    {
        return LangManager::translate('shop.views.elements.shipping.withdraw_point.title');
    }

    public function varName(): string
    {
        return 'withdrawReadyNotifyUser';
    }

    public function includeGlobalConfigWidgets(): void
    {
        $varName = $this->varName();
        require_once EnvManager::getInstance()->getValue('DIR') . 'App/Package/Shop/Views/Elements/Shipping/Global/withdraw.config.inc.view.php';
    }

    public function useGlobalConfigWidgetsInShopDeliveryConfig(): bool
    {
        return true;
    }

    public function canUseInWithdrawalMethod(): bool
    {
        return true;
    }

    public function canUseInShippingMethod(): bool
    {
        return false;
    }

    /**
     * @param string $varName
     * @param ShopHistoryOrdersItemsEntity[] $items
     * @param UserEntity $user
     * @param ShopHistoryOrdersEntity $order
     * @return void
     * @desc Do exec when the administrator accept this command
     */
    public function execAfterCommandValidatedByAdmin(string $varName, array $items, UserEntity $user, ShopHistoryOrdersEntity $order): void
    {
        ShopShippingNotifyWithdrawController::getInstance()->sedMailWithInfo($varName, $items, $user, $order);
    }
}
