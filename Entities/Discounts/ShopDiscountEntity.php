<?php

namespace CMW\Entity\Shop\Discounts;

use CMW\Utils\Date;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use DateInterval;
use DateTime;

class ShopDiscountEntity
{
    private int $id;
    private string $discountName;
    private int $discountLinked;
    private string $discountStartDate;
    private ?string $discountEndDate;
    private ?int $discountMaxUses;
    private ?int $discountCurrentUses;
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
     * @param int $discountLinked
     * @param string $discountStartDate
     * @param string|null $discountEndDate
     * @param int|null $discountMaxUses
     * @param int|null $discountCurrentUses
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
    public function __construct(int $id, string $discountName, int $discountLinked, string $discountStartDate, ?string $discountEndDate, ?int $discountMaxUses, ?int $discountCurrentUses, ?int $discountPercentage, ?float $discountPrice, ?int $discountUsesMultipleByUser, ?int $discountStatus, ?int $discountTest, ?string $discountCode, int $discountDefaultActive, ?int $discountUserHaveOrderBeforeUse, ?int $discountQuantityImpacted, string $discountCreated, string $discountUpdated)
    {
        $this->id = $id;
        $this->discountName = $discountName;
        $this->discountLinked = $discountLinked;
        $this->discountStartDate = $discountStartDate;
        $this->discountEndDate = $discountEndDate;
        $this->discountMaxUses = $discountMaxUses;
        $this->discountCurrentUses = $discountCurrentUses;
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

    public function getLinked(): int
    {
        return $this->discountLinked;
    }

    public function getLinkedFormatted(): string
    {
        if ($this->discountLinked == 0) {
            return 'Tout les produits';
        }
        if ($this->discountLinked == 1) {
            return 'Un ou Des article(s)';
        }
        if ($this->discountLinked == 2) {
            return 'Une ou Des catégorie(s)';
        }
        if ($this->discountLinked == 3) {
            return 'Total du panier';
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

    public function getStartDateFormatted(): string
    {
        return Date::formatDate($this->discountStartDate);
    }

    public function getEndDateFormatted(): ?string
    {
        return Date::formatDate($this->discountEndDate);
    }

    /**
     * @throws \Exception
     */
    public function getDuration(): string
    {
        $now = new DateTime();
        $startDate = new DateTime($this->discountStartDate);
        $endDate = $this->discountEndDate ? new DateTime($this->discountEndDate) : null;
        $status = $this->discountStatus;

        if ($now < $startDate) {
            // La promotion n'a pas encore commencé
            $interval = $now->diff($startDate);
            return $this->formatInterval($interval);
        } elseif ($endDate && $now < $endDate) {
            // La promotion est en cours
            $interval = $now->diff($endDate);
            return 'Termine dans ' . $this->formatInterval($interval);
        } elseif ($endDate && $now >= $endDate) {
            // La promotion est terminée
            return 'Promotion terminée';
        } else {
            // Pas de date de fin ou statut à 0, considérée comme terminée ou indéfiniment active selon le statut
            return $status == 0 ? 'Promotion terminée' : 'En cours, sans date de fin spécifiée';
        }
    }

    private function formatInterval(DateInterval $interval): string
    {
        if ($interval->days >= 1) {
            return $interval->format('%a jour(s)');
        } else if ($interval->h > 0) {
            return $interval->format('%h heure(s)');
        } else {
            return $interval->format('%i minute(s)');
        }
    }

    public function getMaxUses(): ?int
    {
        return $this->discountMaxUses;
    }

    public function getCurrentUses(): ?int
    {
        return $this->discountCurrentUses;
    }

    public function getPercentage(): ?int
    {
        return $this->discountPercentage;
    }

    public function getPrice(): ?float
    {
        return $this->discountPrice;
    }

    /**
     * @return string
     * @desc return the price for views
     */
    public function getPriceFormatted(): string
    {
        $formattedPrice = number_format($this->getPrice(), 2, '.', '');
        $symbol = ShopSettingsModel::getInstance()->getSettingValue('symbol');
        $symbolIsAfter = ShopSettingsModel::getInstance()->getSettingValue('after');
        if ($symbolIsAfter) {
            return $formattedPrice . $symbol;
        } else {
            return $symbol . $formattedPrice;
        }
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
            return 'Inactive';
        }
        if ($this->discountStatus == 1) {
            return 'Active';
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
        return Date::formatDate($this->discountCreated);
    }

    public function getUpdated(): string
    {
        return Date::formatDate($this->discountUpdated);
    }
}
