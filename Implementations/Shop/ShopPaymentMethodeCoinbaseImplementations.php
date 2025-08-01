<?php

namespace CMW\Implementation\Shop\Shop;

use CMW\Controller\Shop\Admin\Payment\Method\ShopPaymentMethodCoinbaseController;
use CMW\Entity\Users\UserEntity;
use CMW\Interface\Shop\IPaymentMethodV2;
use CMW\Manager\Env\EnvManager;
use CMW\Model\Shop\Payment\ShopPaymentMethodSettingsModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;

class ShopPaymentMethodeCoinbaseImplementations implements IPaymentMethodV2
{
    public function name(): string
    {
        return 'CoinBase';
    }

    public function varName(): string
    {
        return 'coinbase';
    }

    public function faIcon(?string $customClass = null): ?string
    {
        return "<i class='fa-brands fa-bitcoin $customClass'></i>";
    }

    public function dashboardURL(): ?string
    {
        return 'https://beta.commerce.coinbase.com/payments';
    }

    public function documentationURL(): ?string
    {
        return 'https://docs.cloud.coinbase.com/commerce-onchain/docs/creating-api-key';
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
        require_once EnvManager::getInstance()->getValue('DIR') . 'App/Package/Shop/Views/Elements/Payments/coinbase.config.inc.view.php';
    }

    public function doPayment(array $cartItems, UserEntity $user): void
    {
        ShopPaymentMethodCoinbaseController::getInstance()->sendCoinbasePayment($cartItems);
    }
}
