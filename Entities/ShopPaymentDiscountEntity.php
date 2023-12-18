<?php

namespace CMW\Entity\Shop;

use CMW\Controller\Core\CoreController;

class ShopPaymentDiscountEntity {
    private int $paymentDiscountId;
    private string $paymentDiscountName;
    private string $paymentDiscountDescription;
    private string $paymentDiscountStartDate;
    private ?string $paymentDiscountEndDate;
    private ?int $paymentDiscountDefaultUses;
    private ?int $paymentDiscountUsesLeft;
    private ?int $paymentDiscountPercent;
    private ?float $paymentDiscountPrice;
    private ?int $paymentDiscountMultiplePerUsers;
    private ?int $paymentDiscountCumulative;
    private ?int $paymentDiscountStatus;
    private ?ShopItemEntity $item;
    private ?ShopCategoryEntity $category;
    private ?string $paymentDiscountCode;
    private int $paymentDiscountDefaultActive;
    private ?int $paymentDiscountUsersNeedPurchaseBeforeUse;
    private string $paymentDiscountCreated;
    private string $paymentDiscountUpdated;

    /**
     * @param int $paymentDiscountId
     * @param string $paymentDiscountName
     * @param string $paymentDiscountDescription
     * @param string $paymentDiscountStartDate
     * @param string $paymentDiscountEndDate
     * @param int $paymentDiscountDefaultUses
     * @param int $paymentDiscountUsesLeft
     * @param int $paymentDiscountPercent
     * @param float $paymentDiscountPrice
     * @param int $paymentDiscountMultiplePerUsers
     * @param int $paymentDiscountCumulative
     * @param int $paymentDiscountStatus
     * @param \CMW\Entity\Shop\ShopItemEntity $item
     * @param \CMW\Entity\Shop\ShopCategoryEntity $category
     * @param string $paymentDiscountCode
     * @param int $paymentDiscountDefaultActive
     * @param int $paymentDiscountUsersNeedPurchaseBeforeUse
     * @param string $paymentDiscountCreated
     * @param string $paymentDiscountUpdated
     */
    public function __construct(int $paymentDiscountId, string $paymentDiscountName, string $paymentDiscountDescription, string $paymentDiscountStartDate, string $paymentDiscountEndDate, int $paymentDiscountDefaultUses, int $paymentDiscountUsesLeft, int $paymentDiscountPercent, float $paymentDiscountPrice, int $paymentDiscountMultiplePerUsers, int $paymentDiscountCumulative, int $paymentDiscountStatus, ShopItemEntity $item, ShopCategoryEntity $category, string $paymentDiscountCode, int $paymentDiscountDefaultActive, int $paymentDiscountUsersNeedPurchaseBeforeUse, string $paymentDiscountCreated, string $paymentDiscountUpdated)
    {
        $this->paymentDiscountId = $paymentDiscountId;
        $this->paymentDiscountName = $paymentDiscountName;
        $this->paymentDiscountDescription = $paymentDiscountDescription;
        $this->paymentDiscountStartDate = $paymentDiscountStartDate;
        $this->paymentDiscountEndDate = $paymentDiscountEndDate;
        $this->paymentDiscountDefaultUses = $paymentDiscountDefaultUses;
        $this->paymentDiscountUsesLeft = $paymentDiscountUsesLeft;
        $this->paymentDiscountPercent = $paymentDiscountPercent;
        $this->paymentDiscountPrice = $paymentDiscountPrice;
        $this->paymentDiscountMultiplePerUsers = $paymentDiscountMultiplePerUsers;
        $this->paymentDiscountCumulative = $paymentDiscountCumulative;
        $this->paymentDiscountStatus = $paymentDiscountStatus;
        $this->item = $item;
        $this->category = $category;
        $this->paymentDiscountCode = $paymentDiscountCode;
        $this->paymentDiscountDefaultActive = $paymentDiscountDefaultActive;
        $this->paymentDiscountUsersNeedPurchaseBeforeUse = $paymentDiscountUsersNeedPurchaseBeforeUse;
        $this->paymentDiscountCreated = $paymentDiscountCreated;
        $this->paymentDiscountUpdated = $paymentDiscountUpdated;
    }

    public function getPaymentDiscountId(): int
    {
        return $this->paymentDiscountId;
    }

    public function getPaymentDiscountName(): string
    {
        return $this->paymentDiscountName;
    }

    public function getPaymentDiscountDescription(): string
    {
        return $this->paymentDiscountDescription;
    }

    public function getPaymentDiscountStartDate(): string
    {
        return $this->paymentDiscountStartDate;
    }

    public function getPaymentDiscountEndDate(): string
    {
        return $this->paymentDiscountEndDate;
    }

    public function getPaymentDiscountDefaultUses(): int
    {
        return $this->paymentDiscountDefaultUses;
    }

    public function getPaymentDiscountUsesLeft(): int
    {
        return $this->paymentDiscountUsesLeft;
    }

    public function getPaymentDiscountPercent(): int
    {
        return $this->paymentDiscountPercent;
    }

    public function getPaymentDiscountPrice(): float
    {
        return $this->paymentDiscountPrice;
    }

    public function getPaymentDiscountMultiplePerUsers(): int
    {
        return $this->paymentDiscountMultiplePerUsers;
    }

    public function getPaymentDiscountCumulative(): int
    {
        return $this->paymentDiscountCumulative;
    }

    public function getPaymentDiscountStatus(): int
    {
        return $this->paymentDiscountStatus;
    }

    public function getItem(): ShopItemEntity
    {
        return $this->item;
    }

    public function getCategory(): ShopCategoryEntity
    {
        return $this->category;
    }

    public function getPaymentDiscountCode(): string
    {
        return $this->paymentDiscountCode;
    }

    public function getPaymentDiscountDefaultActive(): int
    {
        return $this->paymentDiscountDefaultActive;
    }

    public function getPaymentDiscountUsersNeedPurchaseBeforeUse(): int
    {
        return $this->paymentDiscountUsersNeedPurchaseBeforeUse;
    }

    public function getPaymentDiscountCreated(): string
    {
        return CoreController::formatDate($this->paymentDiscountCreated);
    }

    public function getPaymentDiscountUpdated(): string
    {
        return CoreController::formatDate($this->paymentDiscountUpdated);
    }
}