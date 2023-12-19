<?php

namespace CMW\Entity\Shop;

use CMW\Controller\Core\CoreController;

class ShopOrdersItemsEntity {
    private int $orderItemId;
    private ?ShopItemEntity $item;
    private ?ShopOrdersEntity $order;
    private ?ShopPaymentDiscountEntity $discount;
    private ?int $orderItemQuantity;
    private int $orderItemStatus;
    private float $orderItemPrice;
    private string $orderItemCreated;
    private string $orderItemUpdated;

    /**
     * @param int $orderItemId
     * @param \CMW\Entity\Shop\ShopItemEntity|null $item
     * @param \CMW\Entity\Shop\ShopOrdersEntity|null $order
     * @param \CMW\Entity\Shop\ShopPaymentDiscountEntity|null $discount
     * @param int|null $orderItemQuantity
     * @param int $orderItemStatus
     * @param float $orderItemPrice
     * @param string $orderItemCreated
     * @param string $orderItemUpdated
     */
    public function __construct(int $orderItemId, ?ShopItemEntity $item, ?ShopOrdersEntity $order, ?ShopPaymentDiscountEntity $discount, ?int $orderItemQuantity, int $orderItemStatus, float $orderItemPrice, string $orderItemCreated, string $orderItemUpdated)
    {
        $this->orderItemId = $orderItemId;
        $this->item = $item;
        $this->order = $order;
        $this->discount = $discount;
        $this->orderItemQuantity = $orderItemQuantity;
        $this->orderItemStatus = $orderItemStatus;
        $this->orderItemPrice = $orderItemPrice;
        $this->orderItemCreated = $orderItemCreated;
        $this->orderItemUpdated = $orderItemUpdated;
    }

    public function getOrderItemId(): int
    {
        return $this->orderItemId;
    }

    public function getItem(): ?ShopItemEntity
    {
        return $this->item;
    }

    public function getOrder(): ?ShopOrdersEntity
    {
        return $this->order;
    }

    public function getDiscount(): ?ShopPaymentDiscountEntity
    {
        return $this->discount;
    }

    public function getOrderItemQuantity(): ?int
    {
        return $this->orderItemQuantity;
    }

    public function getOrderItemStatus(): string
    {
        if ($this->orderItemStatus == -1) {
            return "Remboursé";
        }
        if ($this->orderItemStatus == 0) {
            return "Annulé";
        }
        if ($this->orderItemStatus == 1) {
            return "Terminé";
        }
    }

    public function getOrderItemPrice(): float
    {
        return $this->orderItemPrice;
    }

    public function getOrderItemCreated(): string
    {
        return CoreController::formatDate($this->orderItemCreated);
    }

    public function getOrderItemUpdated(): string
    {
        return CoreController::formatDate($this->orderItemUpdated);
    }
}