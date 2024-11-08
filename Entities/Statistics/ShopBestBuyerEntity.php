<?php

namespace CMW\Entity\Shop\Statistics;

use CMW\Model\Shop\Setting\ShopSettingsModel;

class ShopBestBuyerEntity
{

    private string $userPseudo;
    private string $userImage;
    private ?float $spent;

    /**
     * @param string $userPseudo
     * @param string $userImage
     * @param float|null $spent
     */
    public function __construct(string $userPseudo, string $userImage, ?float $spent)
    {
        $this->userPseudo = $userPseudo;
        $this->userImage = $userImage;
        $this->spent = $spent;
    }

    public function getUserPseudo(): string
    {
        return $this->userPseudo;
    }

    public function getUserImage(): string
    {
        return $this->userImage;
    }

    public function getFormattedSpent(): string
    {
        $formattedPrice = number_format($this->spent, 2, '.', '');
        $symbol = ShopSettingsModel::getInstance()->getSettingValue('symbol');
        $symbolIsAfter = ShopSettingsModel::getInstance()->getSettingValue('after');
        if ($symbolIsAfter) {
            return $formattedPrice . $symbol;
        }

        return $symbol . $formattedPrice;
    }

    public function getSpent(): ?float
    {
        return $this->spent;
    }
}
