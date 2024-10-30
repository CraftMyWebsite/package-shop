<?php

namespace CMW\Interface\Shop;

use CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity;
use CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersItemsEntity;
use CMW\Entity\Users\UserEntity;

interface IShippingMethod
{
    /**
     * @return string
     * @desc The name of the payment method
     * @example "PayPal"
     */
    public function name(): string;

    /**
     * @return string
     * @desc The variable name
     */
    public function varName(): string;

    /**
     * @return void
     * @desc Include the config widgets for set global variable
     * @example require_once EnvManager::getInstance()->getValue("DIR") . "App/Package/Shop/Views/Elements/Shipping/Global/chronopost.config.inc.view.php";
     */
    public function includeGlobalConfigWidgets(): void;

    /**
     * @return bool
     * @desc return true if you use includeGlobalConfigWidgets()
     */
    public function useGlobalConfigWidgetsInShopDeliveryConfig(): bool;

    /**
     * @return bool
     * @desc if this implementation is allowed for WithdrawnPoint
     */
    public function canUseInWithdrawalMethod(): bool;

    /**
     * @return bool
     * @desc if this implementation is allowed for Shipping
     */
    public function canUseInShippingMethod(): bool;

    /**
     * @param string $varName
     * @param ShopHistoryOrdersItemsEntity[] $items
     * @param UserEntity $user
     * @param ShopHistoryOrdersEntity $order
     * @return void
     * @desc Do exec when the administrator accept this command
     */
    public function execAfterCommandValidatedByAdmin(string $varName, array $items, UserEntity $user, ShopHistoryOrdersEntity $order): void;
}
