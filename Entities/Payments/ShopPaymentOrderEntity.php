<?php

namespace CMW\Entity\Shop\Payments;

use CMW\Entity\Users\UserEntity;
use CMW\Manager\Package\AbstractEntity;

/**
 * Class: @ShopPaymentOrderEntity
 * @package Shop
 * @link https://craftmywebsite.fr/docs/fr/technical/creer-un-package/entities
 */
class ShopPaymentOrderEntity extends AbstractEntity
{
   private int $id;
   private UserEntity $user;
   private int $amount;
   private string $currency;
   private string $status;
   private string $nonce;
   private ?string $sessionId;
   private ?string $paymentIntent;
   private string $createdAt;
   private ?string $paidAt;
   private ?string $updatedAt;

    /**
     * @param int $id
     * @param \CMW\Entity\Users\UserEntity $user
     * @param int $amount
     * @param string $currency
     * @param string $status
     * @param string $nonce
     * @param string|null $sessionId
     * @param string|null $paymentIntent
     * @param string $createdAt
     * @param string|null $paidAt
     * @param string|null $updatedAt
     */
    public function __construct(int $id, UserEntity $user, int $amount, string $currency, string $status, string $nonce, ?string $sessionId, ?string $paymentIntent, string $createdAt, ?string $paidAt, ?string $updatedAt)
    {
        $this->id = $id;
        $this->user = $user;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->status = $status;
        $this->nonce = $nonce;
        $this->sessionId = $sessionId;
        $this->paymentIntent = $paymentIntent;
        $this->createdAt = $createdAt;
        $this->paidAt = $paidAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): UserEntity
    {
        return $this->user;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getAmountCents(): int
    {
        return $this->amount / 100;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getNonce(): string
    {
        return $this->nonce;
    }

    public function getSessionId(): ?string
    {
        return $this->sessionId;
    }

    public function getPaymentIntent(): ?string
    {
        return $this->paymentIntent;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getPaidAt(): ?string
    {
        return $this->paidAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }
}
