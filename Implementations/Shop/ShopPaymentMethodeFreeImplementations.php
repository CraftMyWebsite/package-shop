<?php

namespace CMW\Implementation\Shop\Shop;

use CMW\Controller\Shop\Admin\Payment\Method\ShopPaymentMethodFreeController;
use CMW\Entity\Shop\Const\Payment\PaymentMethodConst;
use CMW\Entity\Users\UserEntity;
use CMW\Interface\Shop\IPaymentMethodV2;
use CMW\Manager\Lang\LangManager;
use CMW\Model\Shop\Setting\ShopSettingsModel;

class ShopPaymentMethodeFreeImplementations implements IPaymentMethodV2
{
    public function name(): string
    {
        return LangManager::translate('shop.views.elements.payments.free.title');
    }

    public function varName(): string
    {
        return PaymentMethodConst::FREE;
    }

    public function faIcon(?string $customClass = null): ?string
    {
        return "<i class='fa-regular fa-handshake $customClass'></i>";
    }

    public function dashboardURL(): ?string
    {
        return null;
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
        return 0;
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
        return 1;
    }

    public function isVirtualCurrency(): bool
    {
        return 0;
    }

    public function includeConfigWidgets(): void
    {
        return;
    }

    public function doPayment(array $cartItems, UserEntity $user): void
    {
        ShopPaymentMethodFreeController::getInstance()->sendFreePayment();
    }
}
