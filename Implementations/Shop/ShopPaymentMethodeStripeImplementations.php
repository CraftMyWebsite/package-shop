<?php

namespace CMW\Implementation\Shop\Shop;

use CMW\Controller\Shop\Admin\Payment\Method\ShopPaymentMethodStripeController;
use CMW\Entity\Users\UserEntity;
use CMW\Interface\Shop\IPaymentMethodV2;
use CMW\Manager\Env\EnvManager;
use CMW\Model\Shop\Payment\ShopPaymentMethodSettingsModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;

class ShopPaymentMethodeStripeImplementations implements IPaymentMethodV2
{
    public function name(): string
    {
        return 'Stripe';
    }

    public function varName(): string
    {
        return 'stripe';
    }

    public function faIcon(?string $customClass = null): ?string
    {
        return "<i class='fa-brands fa-cc-stripe $customClass'></i>";
    }

    public function dashboardURL(): ?string
    {
        return 'https://dashboard.stripe.com/dashboard';
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
        return ShopPaymentMethodSettingsModel::getInstance()->getSetting($this->varName() . '_fee') ?? 0;
    }

    /**
     * @return string
     * @desc return the price for views
     */
    public function getFeesFormatted(): string
    {
        $formattedPrice = number_format($this->fees(), 2, '.', '');
        $symbol = ShopSettingsModel::getInstance()->getSettingValue('symbol');
        $symbolIsAfter = ShopSettingsModel::getInstance()->getSettingValue('after');
        if ($symbolIsAfter) {
            return $formattedPrice . $symbol;
        } else {
            return $symbol . $formattedPrice;
        }
    }

    public function isActive(): bool
    {
        return ShopPaymentMethodSettingsModel::getInstance()->getSetting($this->varName() . '_is_active') ?? 0;
    }

    public function isVirtualCurrency(): bool
    {
        return 0;
    }

    public function includeConfigWidgets(): void
    {
        $varName = $this->varName();
        require_once EnvManager::getInstance()->getValue('DIR') . 'App/Package/Shop/Views/Elements/Payments/stripe.config.inc.view.php';
    }

    public function doPayment(array $cartItems, UserEntity $user): void
    {
        ShopPaymentMethodStripeController::getInstance()->sendStripePayment($cartItems);
    }
}
