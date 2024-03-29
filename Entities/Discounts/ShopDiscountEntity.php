<?php

namespace CMW\Entity\Shop\Discounts;

use CMW\Controller\Core\CoreController;

class ShopDiscountEntity
{
    private int $id;
    private string $discountName;
    private string $discountDescription;
    private int $discountLinked;
    private string $discountStartDate;
    private ?string $discountEndDate;
    private ?int $discountDefaultUses;
    private ?int $discountUsesLeft;
    private ?int $discountPercentage;
    private ?float $discountPrice;
    private ?int $discountUsesMultipleByUser;
    private ?int $discountStatus;
    private ?int $discountTest;
    private ?string $discountCode;
    private int $discountDefaultActive;
    private ?int $discountUserHaveOrderBeforeUse;
    private ?int $discountQuantityImpacted;
    private string $discountCreated;
    private string $discountUpdated;

    /**
     * @param int $id
     * @param string $discountName
     * @param string $discountDescription
     * @param int $discountLinked
     * @param string $discountStartDate
     * @param string|null $discountEndDate
     * @param int|null $discountDefaultUses
     * @param int|null $discountUsesLeft
     * @param int|null $discountPercentage
     * @param float|null $discountPrice
     * @param int|null $discountUsesMultipleByUser
     * @param int|null $discountStatus
     * @param int|null $discountTest
     * @param string|null $discountCode
     * @param int $discountDefaultActive
     * @param int|null $discountUserHaveOrderBeforeUse
     * @param int|null $discountQuantityImpacted
     * @param string $discountCreated
     * @param string $discountUpdated
     */
    public function __construct(int $id, string $discountName, string $discountDescription, int $discountLinked, string $discountStartDate, ?string $discountEndDate, ?int $discountDefaultUses, ?int $discountUsesLeft, ?int $discountPercentage, ?float $discountPrice, ?int $discountUsesMultipleByUser, ?int $discountStatus, ?int $discountTest, ?string $discountCode, int $discountDefaultActive, ?int $discountUserHaveOrderBeforeUse, ?int $discountQuantityImpacted, string $discountCreated, string $discountUpdated)
    {
        $this->id = $id;
        $this->discountName = $discountName;
        $this->discountDescription = $discountDescription;
        $this->discountLinked = $discountLinked;
        $this->discountStartDate = $discountStartDate;
        $this->discountEndDate = $discountEndDate;
        $this->discountDefaultUses = $discountDefaultUses;
        $this->discountUsesLeft = $discountUsesLeft;
        $this->discountPercentage = $discountPercentage;
        $this->discountPrice = $discountPrice;
        $this->discountUsesMultipleByUser = $discountUsesMultipleByUser;
        $this->discountStatus = $discountStatus;
        $this->discountTest = $discountTest;
        $this->discountCode = $discountCode;
        $this->discountDefaultActive = $discountDefaultActive;
        $this->discountUserHaveOrderBeforeUse = $discountUserHaveOrderBeforeUse;
        $this->discountQuantityImpacted = $discountQuantityImpacted;
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

    public function getLinked(): int
    {
        return $this->discountLinked;
    }

    public function getLinkedFormatted(): string
    {
        if ($this->discountLinked == 0) {
            return "Tout les produits";
        }
        if ($this->discountLinked == 1) {
            return "Un ou Des article(s)";
        }
        if ($this->discountLinked == 2) {
            return "Une ou Des catégorie(s)";
        }
        if ($this->discountLinked == 3) {
            return "Total du panier";
        }
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

    public function getStatus(): ?int
    {
        return $this->discountStatus;
    }

    public function getTestMode(): ?int
    {
        return $this->discountTest;
    }

    public function getStatusFormatted(): ?string
    {
        if ($this->discountStatus == 0) {
            return "Inactive";
        }
        if ($this->discountStatus == 1) {
            return "Active";
        }
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

    public function getDiscountQuantityImpacted(): ?int
    {
        return $this->discountQuantityImpacted;
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