<?php

namespace CMW\Entity\Shop\Shippings;

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractEntity;
use CMW\Model\Shop\Country\ShopCountryModel;

class ShopShippingZoneEntity extends AbstractEntity
{
    private int $id;
    private string $name;
    private string $country;

    /**
     * @param int $id
     * @param string $name
     * @param string $country
     */
    public function __construct(int $id, string $name, string $country)
    {
        $this->id = $id;
        $this->name = $name;
        $this->country = $country;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getFormattedCountry(): ?string
    {
        if ($this->country === "ALL") {
            return LangManager::translate('shop.entities.shippingZone.all');
        } else {
            return ShopCountryModel::getInstance()->getCountryByCode($this->country)->getName();
        }
    }
}
