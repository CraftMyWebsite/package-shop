<?php

namespace CMW\Implementation\Shop;

use CMW\Interface\Shop\IPaymentMethod;
use CMW\Manager\Env\EnvManager;

class ShopPaymentMethodePayPalImplementations implements IPaymentMethod {

    public function name(): string
    {
        return "PayPal";
    }

    public function description(): string
    {
        return "TODO DESC I18N";
    }

    public function fees(): ?int
    {
       return 0; //TODO Var ?
    }

    public function action(int $amount): bool
    {
        return false;
    }

    public function includeConfigWidgets(): void
    {
        require_once EnvManager::getInstance()->getValue("DIR") . "App/Package/Shop/Views/Elements/paypal.config.inc.view.php";
    }
}