<?php

namespace CMW\Entity\Shop\Discounts;


use CMW\Controller\Core\CoreController;
use CMW\Entity\Shop\Categories\ShopCategoryEntity;
use CMW\Entity\Shop\Items\ShopItemEntity;

class ShopDiscountEntity
{
    private int $id;
    private string $discountName;
    private string $discountDescription;
    private string $discountStartDate;
    private ?string $discountEndDate;
    private ?int $discountDefaultUses;
    private ?int $discountUsesLeft;
    private ?int $discountPercentage;
    private ?float $discountPrice;
    private ?int $discountUsesMultipleByUser;
    private ?int $discountCumulative;
    private ?int $discountStatus;
    private ?ShopItemEntity $itemId;
    private ?ShopCategoryEntity $categoryId;
    private ?string $discountCode;
    private int $discountDefaultActive;
    private ?int $discountUserHaveOrderBeforeUse;
    private string $discountCreated;
    private string $discountUpdated;

    /**
     * @param int $id
     * @param string $discountName
     * @param string $discountDescription
     * @param string $discountStartDate
     * @param string|null $discountEndDate
     * @param int|null $discountDefaultUses
     * @param int|null $discountUsesLeft
     * @param int|null $discountPercentage
     * @param float|null $discountPrice
     * @param int|null $discountUsesMultipleByUser
     * @param int|null $discountCumulative
     * @param int|null $discountStatus
     * @param \CMW\Entity\Shop\Items\ShopItemEntity|null $itemId
     * @param \CMW\Entity\Shop\Categories\ShopCategoryEntity|null $categoryId
     * @param string|null $discountCode
     * @param int $discountDefaultActive
     * @param int|null $discountUserHaveOrderBeforeUse
     * @param string $discountCreated
     * @param string $discountUpdated
     */
    public function __construct(int $id, string $discountName, string $discountDescription, string $discountStartDate, ?string $discountEndDate, ?int $discountDefaultUses, ?int $discountUsesLeft, ?int $discountPercentage, ?float $discountPrice, ?int $discountUsesMultipleByUser, ?int $discountCumulative, ?int $discountStatus, ?ShopItemEntity $itemId, ?ShopCategoryEntity $categoryId, ?string $discountCode, int $discountDefaultActive, ?int $discountUserHaveOrderBeforeUse, string $discountCreated, string $discountUpdated)
    {
        $this->id = $id;
        $this->discountName = $discountName;
        $this->discountDescription = $discountDescription;
        $this->discountStartDate = $discountStartDate;
        $this->discountEndDate = $discountEndDate;
        $this->discountDefaultUses = $discountDefaultUses;
        $this->discountUsesLeft = $discountUsesLeft;
        $this->discountPercentage = $discountPercentage;
        $this->discountPrice = $discountPrice;
        $this->discountUsesMultipleByUser = $discountUsesMultipleByUser;
        $this->discountCumulative = $discountCumulative;
        $this->discountStatus = $discountStatus;
        $this->itemId = $itemId;
        $this->categoryId = $categoryId;
        $this->discountCode = $discountCode;
        $this->discountDefaultActive = $discountDefaultActive;
        $this->discountUserHaveOrderBeforeUse = $discountUserHaveOrderBeforeUse;
        $this->discountCreated = $discountCreated;
        $this->discountUpdated = $discountUpdated;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->discountName;
    }

    public function getDescription(): string
    {
        return $this->discountDescription;
    }

    public function getStartDate(): string
    {
        return $this->discountStartDate;
    }

    public function getEndDate(): ?string
    {
        return $this->discountEndDate;
    }

    public function getDefaultUses(): ?int
    {
        return $this->discountDefaultUses;
    }

    public function getUsesLeft(): ?int
    {
        return $this->discountUsesLeft;
    }

    public function getPercentage(): ?int
    {
        return $this->discountPercentage;
    }

    public function getPrice(): ?float
    {
        return $this->discountPrice;
    }

    public function getUsesMultipleByUser(): ?int
    {
        return $this->discountUsesMultipleByUser;
    }

    public function getCumulative(): ?int
    {
        return $this->discountCumulative;
    }

    public function getStatus(): ?string
    {
        if ($this->discountStatus == 0) {
            return "Inactive";
        }
        if ($this->discountStatus == 1) {
            return "Active";
        }
    }

    public function getItem(): ?ShopItemEntity
    {
        return $this->itemId;
    }

    public function getCategory(): ?ShopCategoryEntity
    {
        return $this->categoryId;
    }

    public function getCode(): ?string
    {
        return $this->discountCode;
    }

    public function getDefaultActive(): int
    {
        return $this->discountDefaultActive;
    }

    public function getUserHaveOrderBeforeUse(): ?int
    {
        return $this->discountUserHaveOrderBeforeUse;
    }

    public function getCreated(): string
    {
        return CoreController::formatDate($this->discountCreated);
    }

    public function getUpdated(): string
    {
        return CoreController::formatDate($this->discountUpdated);
    }



}