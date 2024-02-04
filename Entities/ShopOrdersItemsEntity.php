<?php

namespace CMW\Entity\Shop;

use CMW\Controller\Core\CoreController;
use CMW\Entity\Shop\Items\ShopItemEntity;
use CMW\Manager\Env\EnvManager;
use CMW\Model\Shop\ShopImagesModel;

class ShopOrdersItemsEntity {
    private int $orderItemId;
    private ?ShopItemEntity $item;
    private ?ShopOrdersEntity $order;
    private ?ShopPaymentDiscountEntity $discount;
    private ?int $orderItemQuantity;
    private float $orderItemPrice;
    private string $orderItemCreated;
    private string $orderItemUpdated;

    /**
     * @param int $orderItemId
     * @param \CMW\Entity\Shop\ShopItemEntity|null $item
     * @param \CMW\Entity\Shop\ShopOrdersEntity|null $order
     * @param \CMW\Entity\Shop\ShopPaymentDiscountEntity|null $discount
     * @param int|null $orderItemQuantity
     * @param float $orderItemPrice
     * @param string $orderItemCreated
     * @param string $orderItemUpdated
     */
    public function __construct(int $orderItemId, ?ShopItemEntity $item, ?ShopOrdersEntity $order, ?ShopPaymentDiscountEntity $discount, ?int $orderItemQuantity, float $orderItemPrice, string $orderItemCreated, string $orderItemUpdated)
    {
        $this->orderItemId = $orderItemId;
        $this->item = $item;
        $this->order = $order;
        $this->discount = $discount;
        $this->orderItemQuantity = $orderItemQuantity;
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

    /**
     * @return string
     */
    public function getFirstImageItemUrl(): string
    {
        $return = ShopImagesModel::getInstance()->getFirstImageByItemId($this->getItem()->getId());
        return EnvManager::getInstance()->getValue("PATH_SUBFOLDER") . "Public/Uploads/Shop/" . $return;
    }
}