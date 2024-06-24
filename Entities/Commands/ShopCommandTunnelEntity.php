<?php

namespace CMW\Entity\Shop\Commands;

use CMW\Controller\Core\CoreController;
use CMW\Entity\Shop\Deliveries\ShopDeliveryUserAddressEntity;
use CMW\Entity\Shop\Deliveries\ShopShippingEntity;
use CMW\Entity\Users\UserEntity;

class ShopCommandTunnelEntity
{
    private ?int $tunnelId;
    private ?int $tunnelStep;
    private ?UserEntity $user;
    private ?ShopShippingEntity $shipping;
    private ?ShopDeliveryUserAddressEntity $shopDeliveryUserAddress;
    private ?string $paymentName;
    private ?string $tunnelCreated;
    private ?string $tunnelUpdated;

    /**
     * @param int|null $tunnelId
     * @param int|null $tunnelStep
     * @param \CMW\Entity\Users\UserEntity|null $user
     * @param \CMW\Entity\Shop\Deliveries\ShopShippingEntity|null $shipping
     * @param \CMW\Entity\Shop\Deliveries\ShopDeliveryUserAddressEntity|null $shopDeliveryUserAddress
     * @param string|null $paymentName
     * @param string|null $tunnelCreated
     * @param string|null $tunnelUpdated
     */
    public function __construct(?int $tunnelId, ?int $tunnelStep, ?UserEntity $user, ?ShopShippingEntity $shipping, ?ShopDeliveryUserAddressEntity $shopDeliveryUserAddress, ?string $paymentName, ?string $tunnelCreated, ?string $tunnelUpdated)
    {
        $this->tunnelId = $tunnelId;
        $this->tunnelStep = $tunnelStep;
        $this->user = $user;
        $this->shipping = $shipping;
        $this->shopDeliveryUserAddress = $shopDeliveryUserAddress;
        $this->paymentName = $paymentName;
        $this->tunnelCreated = $tunnelCreated;
        $this->tunnelUpdated = $tunnelUpdated;
    }

    public function getId(): int
    {
        return $this->tunnelId;
    }

    public function getStep(): ?int
    {
        return $this->tunnelStep;
    }

    public function getUser(): ?UserEntity
    {
        return $this->user;
    }

    public function getShipping(): ?ShopShippingEntity
    {
        return $this->shipping;
    }

    public function getShopDeliveryUserAddress(): ?ShopDeliveryUserAddressEntity
    {
        return $this->shopDeliveryUserAddress;
    }

    public function getPaymentName(): ?string
    {
        return $this->paymentName;
    }

    public function getCreated(): string
    {
        return CoreController::formatDate($this->tunnelCreated);
    }

    public function getUpdated(): string
    {
        return CoreController::formatDate($this->tunnelUpdated);
    }

}