<?php

namespace CMW\Entity\Shop;

use CMW\Controller\Core\CoreController;
use CMW\Entity\Users\UserEntity;

class ShopOrdersEntity {
    private int $orderId;
    private ?userEntity $user;
    private int $orderStatus;
    private ?ShopShippingEntity $shipping;
    private ?ShopDeliveryUserAddressEntity $deliveryAddress;
    private string $orderCreated;
    private string $orderUpdated;

    /**
     * @param int $orderId
     * @param \CMW\Entity\Users\UserEntity|null $user
     * @param int $orderStatus
     * @param ShopShippingEntity|null $shipping
     * @param ShopDeliveryUserAddressEntity|null $deliveryAddress
     * @param string $orderCreated
     * @param string $orderUpdated
     */
    public function __construct(int $orderId, ?UserEntity $user, int $orderStatus, ?ShopShippingEntity $shipping, ?ShopDeliveryUserAddressEntity $deliveryAddress, string $orderCreated, string $orderUpdated)
    {
        $this->orderId = $orderId;
        $this->user = $user;
        $this->orderStatus = $orderStatus;
        $this->shipping = $shipping;
        $this->deliveryAddress = $deliveryAddress;
        $this->orderCreated = $orderCreated;
        $this->orderUpdated = $orderUpdated;
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function getUser(): ?UserEntity
    {
        return $this->user;
    }

    public function getOrderStatus(): string
    {
        if ($this->orderStatus == -2) {
            return "Remboursé";
        }
        if ($this->orderStatus == -1) {
            return "Annulé";
        }
        if ($this->orderStatus == 0) {
            return "En attente de validation";
        }
        if ($this->orderStatus == 1) {
            return "En attente de la livraison";
        }
        if ($this->orderStatus == 2) {
            return "Livraison en cours";
        }
        if ($this->orderStatus == 3) {
            return "Terminé";
        }
    }

    public function getShippingMethod(): ?ShopShippingEntity
    {
        return $this->shipping;
    }

    public function getDeliveryAddress(): ?ShopDeliveryUserAddressEntity
    {
        return $this->deliveryAddress;
    }

    public function getOrderCreated(): string
    {
        return CoreController::formatDate($this->orderCreated);
    }

    public function getOrderUpdated(): string
    {
        return CoreController::formatDate($this->orderUpdated);
    }
}