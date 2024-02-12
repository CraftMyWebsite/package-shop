<?php

namespace CMW\Interface\Shop;


use CMW\Entity\Shop\Deliveries\ShopDeliveryUserAddressEntity;
use CMW\Entity\Shop\Deliveries\ShopShippingEntity;
use CMW\Entity\Users\UserEntity;
use CMW\Manager\Env\EnvManager;

interface IPaymentMethod
{
    /**
     * @return string
     * @desc The name of the payment method
     * @example "PayPal"
     */
    public function name(): string;

    /**
     * @return string
     * @desc Small description of the payment method
     */
    public function description(): string;

    /**
     * @return int
     * @desc Fees are optional
     */
    public function fees(): int;

    /**
     * @return void
     * @desc Include the config widgets of the payment method, like ClientId, ClientSecret, etc...
     * @example require_once EnvManager::getInstance()->getValue("DIR") . "App/Package/Shop/Views/Elements/paypal.config.inc.view.php";
     */
    public function includeConfigWidgets(): void;

    /**
     * @param \CMW\Entity\Shop\Carts\ShopCartItemEntity[] $cartItems
     * @param \CMW\Entity\Users\UserEntity $user
     * @param \CMW\Entity\Shop\Deliveries\ShopShippingEntity $shipping
     * @param \CMW\Entity\Shop\Deliveries\ShopDeliveryUserAddressEntity $address
     * @return void
     * @throws \CMW\Exception\Shop\Payment\ShopPaymentException
     * @desc Do payment logic
     */
    public function doPayment(array $cartItems, UserEntity $user, ShopShippingEntity $shipping,
                              ShopDeliveryUserAddressEntity $address): void;
}
