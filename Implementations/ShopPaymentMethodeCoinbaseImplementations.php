<?php

namespace CMW\Implementation\Shop;

use CMW\Controller\Shop\Admin\Payment\Method\ShopPaymentMethodCoinbaseController;
use CMW\Entity\Shop\Deliveries\ShopDeliveryUserAddressEntity;
use CMW\Entity\Shop\Deliveries\ShopShippingEntity;
use CMW\Entity\Users\UserEntity;
use CMW\Interface\Shop\IPaymentMethod;
use CMW\Manager\Env\EnvManager;
use CMW\Model\Shop\Payment\ShopPaymentMethodSettingsModel;

class ShopPaymentMethodeCoinbaseImplementations implements IPaymentMethod
{
    public function name(): string
    {
        return "CoinBase";
    }

    public function varName(): string
    {
        return str_replace(' ', '_', strtolower($this->name()));
    }

    public function faIcon(?string $customClass = null): ?string
    {
        return "<i class='fa-brands fa-bitcoin $customClass'></i>";
    }

    public function dashboardURL(): ?string
    {
        return "https://beta.commerce.coinbase.com/payments";
    }

    public function documentationURL(): ?string
    {
        return "https://docs.cloud.coinbase.com/commerce-onchain/docs/creating-api-key";
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
        require_once EnvManager::getInstance()->getValue("DIR") . "App/Package/Shop/Views/Elements/Payments/coinbase.config.inc.view.php";
    }

    public function doPayment(array $cartItems, UserEntity $user, ShopDeliveryUserAddressEntity $address): void
    {
        ShopPaymentMethodCoinbaseController::getInstance()->sendCoinbasePayment($cartItems, $address);
    }
}