<?php

namespace CMW\Entity\Shop\Orders;

use CMW\Controller\Core\CoreController;
use CMW\Entity\Shop\Deliveries\ShopDeliveryUserAddressEntity;
use CMW\Entity\Shop\Deliveries\ShopShippingEntity;
use CMW\Entity\Users\UserEntity;

class ShopOrdersEntity {
    private int $orderId;
    private ?userEntity $user;
    private string $orderNumber;
    private int $orderStatus;
    private ?ShopShippingEntity $shipping;
    private ?ShopDeliveryUserAddressEntity $deliveryAddress;
    private ?string $paymentName;
    private ?string $shippingLink;
    private string $orderCreated;
    private string $orderUpdated;

    /**
     * @param int $orderId
     * @param \CMW\Entity\Users\UserEntity|null $user
     * @param string $orderNumber
     * @param int $orderStatus
     * @param ShopShippingEntity|null $shipping
     * @param ShopDeliveryUserAddressEntity|null $deliveryAddress
     * @param string|null $paymentName
     * @param string|null $shippingLink
     * @param string $orderCreated
     * @param string $orderUpdated
     */
    public function __construct(int $orderId, ?UserEntity $user, string $orderNumber, int $orderStatus, ?ShopShippingEntity $shipping, ?ShopDeliveryUserAddressEntity $deliveryAddress, ?string $paymentName, ?string $shippingLink, string $orderCreated, string $orderUpdated)
    {
        $this->orderId = $orderId;
        $this->user = $user;
        $this->orderNumber = $orderNumber;
        $this->orderStatus = $orderStatus;
        $this->shipping = $shipping;
        $this->deliveryAddress = $deliveryAddress;
        $this->paymentName = $paymentName;
        $this->shippingLink = $shippingLink;
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

    public function getNumber(): string
    {
        return $this->orderNumber;
    }

    public function getAdminStatus(): string
    {
        if ($this->orderStatus == -2) {
            return "Remboursé";
        }
        if ($this->orderStatus == -1) {
            return "<b style='color: orangered'>Annulé !</b>";
        }
        if ($this->orderStatus == 0) {
            return "<i style='color: orangered' class='fa-solid fa-triangle-exclamation fa-fade'></i> Nouvelle commande !";
        }
        if ($this->orderStatus == 1) {
            return "<i style='color: orange' class='fa-solid fa-spinner fa-spin-pulse'></i> En attente de livraison";
        }
        if ($this->orderStatus == 2) {
            return "<i style='color: #517331' class='fa-solid fa-truck-fast'></i> Livraison en cours";
        }
        if ($this->orderStatus == 3) {
            return "<i style='color: green' class='fa-regular fa-circle-check'></i> Terminé";
        }
    }

    public function getPublicStatus(): string
    {
        if ($this->orderStatus == -2) {
            return "Remboursé" ;
        }
        if ($this->orderStatus == -1) {
            return "Annulé (Remboursement en cours ...)";
        }
        if ($this->orderStatus == 0) {
            return "Commande en préparation";
        }
        if ($this->orderStatus == 1) {
            return "Commande prête, votre colis sera remis dans un centre de livraison";
        }
        if ($this->orderStatus == 2) {
            return "Livraison en cours";
        }
        if ($this->orderStatus == 3) {
            return "Terminé";
        }
    }

    /**
     * @return string
     * @desc : return the order status code as integer. Between -2 and 3 | -2 = refunded | -1 = canceled | 0 = new order | 1 = ready to send | 2 = delivery in progress | 3 = finished
     */
    public function getStatusCode(): int
    {
        return $this->orderStatus ;
    }

    public function getShippingMethod(): ?ShopShippingEntity
    {
        return $this->shipping;
    }

    public function getDeliveryAddress(): ?ShopDeliveryUserAddressEntity
    {
        return $this->deliveryAddress;
    }

    public function getPaymentName(): ?string
    {
        return $this->paymentName;
    }
    public function getShippingLink(): ?string
    {
        return $this->shippingLink;
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