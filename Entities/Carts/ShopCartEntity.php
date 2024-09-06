<?php

namespace CMW\Entity\Shop\Carts;

use CMW\Controller\Core\CoreController;
use CMW\Entity\Users\UserEntity;

class ShopCartEntity
{
    private ?int $id;
    private ?userEntity $user;
    private ?string $sessionId;
    private ?string $cartCreated;
    private ?string $cartUpdated;

    /**
     * @param int|null $id
     * @param \CMW\Entity\Users\UserEntity|null $user
     * @param string|null $sessionId
     * @param string|null $cartCreated
     * @param string|null $cartUpdated
     */
    public function __construct(?int $id, ?UserEntity $user, ?string $sessionId, ?string $cartCreated, ?string $cartUpdated)
    {
        $this->id = $id;
        $this->user = $user;
        $this->sessionId = $sessionId;
        $this->cartCreated = $cartCreated;
        $this->cartUpdated = $cartUpdated;
    }

    public function getId(): ?int
    {
        return $this->id ?? null;
    }

    public function getUser(): ?UserEntity
    {
        return $this->user;
    }

    public function getSession(): ?string
    {
        return $this->sessionId;
    }

    public function getCartCreated(): ?string
    {
        return CoreController::formatDate($this->cartCreated);
    }

    public function getCartUpdated(): ?string
    {
        return CoreController::formatDate($this->cartUpdated);
    }
}
