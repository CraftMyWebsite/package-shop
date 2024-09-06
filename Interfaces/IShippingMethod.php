<?php

namespace CMW\Interface\Shop;

use CMW\Entity\Shop\Items\ShopItemEntity;
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
     * @param string $varName
     * @param ShopItemEntity $item
     * @param UserEntity $user
     * @return void
     * @desc Do exec when the administrator accept this command
     */
    public function execAfterCommandValidatedByAdmin(string $varName, ShopItemEntity $item, UserEntity $user): void;

    /*
     * TODO ??
     * Admin command tunel widget
     */
}
