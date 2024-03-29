<?php

namespace CMW\Implementation\Shop;

use CMW\Controller\Shop\Admin\Payment\Method\ShopPaymentMethodPayPalController;
use CMW\Entity\Shop\Deliveries\ShopDeliveryUserAddressEntity;
use CMW\Entity\Users\UserEntity;
use CMW\Interface\Shop\IPaymentMethod;
use CMW\Manager\Env\EnvManager;
use CMW\Model\Shop\Payment\ShopPaymentMethodSettingsModel;

class ShopPaymentMethodePayPalImplementations implements IPaymentMethod
{
    public function name(): string
    {
        return "PayPal";
    }

    public function varName(): string
    {
        return str_replace(' ', '_', strtolower($this->name()));
    }

    public function faIcon(?string $customClass = null): ?string
    {
        return "<i class='fa-brands fa-cc-paypal $customClass'></i>";
    }

    public function dashboardURL(): ?string
    {
        return "https://developer.paypal.com/dashboard";
    }

    public function documentationURL(): ?string
    {
        return null;
    }

    public function description(): ?string
    {
        return null;
    }

    public function fees(): float
    {
        return ShopPaymentMethodSettingsModel::getInstance()->getSetting($this->varName().'_fee') ?? 0;
    }

    public function isActive(): bool
    {
        return ShopPaymentMethodSettingsModel::getInstance()->getSetting($this->varName().'_is_active') ?? 0;
    }

    public function includeConfigWidgets(): void
    {
        require_once EnvManager::getInstance()->getValue("DIR") . "App/Package/Shop/Views/Elements/Payments/paypal.config.inc.view.php";
    }

    public function doPayment(array $cartItems, UserEntity $user, ShopDeliveryUserAddressEntity $address): void
    {
        ShopPaymentMethodPayPalController::getInstance()->sendPayPalPayment($cartItems, $address);
    }
}