<?php

namespace CMW\Implementation\Shop;

use CMW\Controller\Shop\Admin\Payment\Method\ShopPaymentMethodStripeController;
use CMW\Entity\Shop\Deliveries\ShopDeliveryUserAddressEntity;
use CMW\Entity\Shop\Deliveries\ShopShippingEntity;
use CMW\Entity\Users\UserEntity;
use CMW\Interface\Shop\IPaymentMethod;
use CMW\Manager\Env\EnvManager;
use CMW\Model\Shop\Payment\ShopPaymentMethodSettingsModel;

class ShopPaymentMethodeStripeImplementations implements IPaymentMethod
{
    public function name(): string
    {
        return "Stripe";
    }

    public function varName(): string
    {
        return str_replace(' ', '_', strtolower($this->name()));
    }

    public function faIcon(?string $customClass = null): ?string
    {
        return "<i class='fa-brands fa-cc-stripe $customClass'></i>";
    }

    public function dashboardURL(): ?string
    {
        return "https://dashboard.stripe.com/dashboard";
    }

    public function documentationURL(): ?string
    {
        return "";
    }

    public function description(): string
    {
        return "TODO DESC I18N";
    }

    public function fees(): int
    {
        return 0; //TODO Var ?
    }

    public function isActive(): bool
    {
        return ShopPaymentMethodSettingsModel::getInstance()->getSetting($this->varName().'_is_active') ?? 0;
    }
    public function includeConfigWidgets(): void
    {
        require_once EnvManager::getInstance()->getValue("DIR") . "App/Package/Shop/Views/Elements/Payments/stripe.config.inc.view.php";
    }

    public function doPayment(array $cartItems, UserEntity $user, ShopShippingEntity $shipping, ShopDeliveryUserAddressEntity $address): void
    {
        ShopPaymentMethodStripeController::getInstance()->sendStripePayment($cartItems, $shipping, $address);
    }
}