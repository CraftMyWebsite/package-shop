<?php

namespace CMW\Implementation\Shop\Shop;

use CMW\Interface\Shop\IPriceTypeMethod;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Type\Shop\Const\Payment\PaymentPriceTypeConst;

class ShopPriceTypeMethodMoneyImplementations implements IPriceTypeMethod
{
    public function name(): string
    {
        return ShopSettingsModel::getInstance()->getSettingValue('symbol');
    }

    public function varName(): string
    {
        return PaymentPriceTypeConst::MONEY;
    }
}
