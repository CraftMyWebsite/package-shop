<?php

namespace CMW\Implementation\Shop;

use CMW\Controller\Shop\Payment\Method\ShopPaymentMethodPayPalController;
use CMW\Entity\Shop\ShopDeliveryUserAddressEntity;
use CMW\Entity\Shop\ShopShippingEntity;
use CMW\Entity\Users\UserEntity;
use CMW\Interface\Shop\IPaymentMethod;
use CMW\Manager\Env\EnvManager;

class ShopPaymentMethodePayPalImplementations implements IPaymentMethod
{
    public function name(): string
    {
        return "PayPal";
    }

    public function description(): string
    {
        return "TODO DESC I18N";
    }

    public function fees(): int
    {
        return 0; //TODO Var ?
    }

    public function includeConfigWidgets(): void
    {
        require_once EnvManager::getInstance()->getValue("DIR") . "App/Package/Shop/Views/Elements/paypal.config.inc.view.php";
    }

    public function doPayment(array $cartItems, UserEntity $user, ShopShippingEntity $shipping, ShopDeliveryUserAddressEntity $address): void
    {
        ShopPaymentMethodPayPalController::getInstance()->sendPayPalPayment($cartItems, $shipping, $address);
    }
}