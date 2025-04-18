<?php

namespace CMW\Implementation\Shop\Shop;

use CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity;
use CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersItemsEntity;
use CMW\Entity\Users\UserEntity;
use CMW\Interface\Shop\IShippingMethod;

class ShopShippingEmptyImplementations implements IShippingMethod
{
    public function name(): string
    {
        return 'Aucune';
    }

    public function varName(): string
    {
        return 'nothing';
    }

    public function includeGlobalConfigWidgets(): void
    {
        return;
    }

    public function useGlobalConfigWidgetsInShopDeliveryConfig(): bool
    {
        return false;
    }

    public function canUseInWithdrawalMethod(): bool
    {
        return true;
    }

    public function canUseInShippingMethod(): bool
    {
        return true;
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
        return;
    }
}
