<?php

namespace CMW\Implementation\Shop;

use CMW\Controller\Shop\Admin\Payment\Method\ShopPaymentMethodFreeController;
use CMW\Entity\Shop\Deliveries\ShopDeliveryUserAddressEntity;
use CMW\Entity\Shop\Deliveries\ShopShippingEntity;
use CMW\Entity\Users\UserEntity;
use CMW\Interface\Shop\IPaymentMethod;

class ShopPaymentMethodeFreeImplementations implements IPaymentMethod
{
    public function name(): string
    {
        return "Commande offerte";
    }

    public function varName(): string
    {
        return "free";
    }

    public function faIcon(?string $customClass = null): ?string
    {
        return "<i class='fa-regular fa-handshake $customClass'></i>";
    }

    public function dashboardURL(): ?string
    {
        return "";
    }

    public function documentationURL(): ?string
    {
        return "";
    }

    public function description(): string
    {
        return "";
    }

    public function fees(): int
    {
        return 0; //TODO Var ?
    }

    public function isActive(): bool
    {
        return 1;
    }

    public function includeConfigWidgets(): void
    {
        return;
    }

    public function doPayment(array $cartItems, UserEntity $user, ShopDeliveryUserAddressEntity $address): void
    {
        ShopPaymentMethodFreeController::getInstance()->sendFreePayment();
    }
}