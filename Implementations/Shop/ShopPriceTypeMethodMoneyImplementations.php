<?php

namespace CMW\Implementation\Shop\Shop;

use CMW\Entity\Users\UserEntity;
use CMW\Interface\Shop\IPriceTypeMethod;
use CMW\Model\Shop\Setting\ShopSettingsModel;

class ShopPriceTypeMethodMoneyImplementations implements IPriceTypeMethod
{
    public function name(): string
    {
        return ShopSettingsModel::getInstance()->getSettingValue("symbol");
    }

    public function varName(): string
    {
        return "money";
    }
}