<?php

namespace CMW\Entity\Shop;

use CMW\Controller\Core\CoreController;
use CMW\Entity\Users\UserEntity;

class ShopOrdersEntity {
    private int $orderId;
    private ?userEntity $user;
    private string $orderCreated;
    private string $orderUpdated;

    /**
     * @param int $orderId
     * @param \CMW\Entity\Users\UserEntity|null $user
     * @param string $orderCreated
     * @param string $orderUpdated
     */
    public function __construct(int $orderId, ?UserEntity $user, string $orderCreated, string $orderUpdated)
    {
        $this->orderId = $orderId;
        $this->user = $user;
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

    public function getOrderCreated(): string
    {
        return CoreController::formatDate($this->orderCreated);
    }

    public function getOrderUpdated(): string
    {
        return CoreController::formatDate($this->orderUpdated);
    }
}