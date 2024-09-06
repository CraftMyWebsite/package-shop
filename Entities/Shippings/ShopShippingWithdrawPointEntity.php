<?php

namespace CMW\Entity\Shop\Shippings;

use CMW\Model\Shop\Country\ShopCountryModel;

class ShopShippingWithdrawPointEntity
{
    private int $id;
    private ?int $addressDistance;
    private string $addressLine;
    private string $addressCity;
    private string $addressPostalCode;
    private string $addressCountry;

    /**
     * @param int $id
     * @param int|null $addressDistance
     * @param string $addressLine
     * @param string $addressCity
     * @param string $addressPostalCode
     * @param string $addressCountry
     */
    public function __construct(int $id, ?int $addressDistance, string $addressLine, string $addressCity, string $addressPostalCode, string $addressCountry)
    {
        $this->id = $id;
        $this->addressDistance = $addressDistance;
        $this->addressLine = $addressLine;
        $this->addressCity = $addressCity;
        $this->addressPostalCode = $addressPostalCode;
        $this->addressCountry = $addressCountry;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAddressDistance(): ?int
    {
        return $this->addressDistance;
    }

    public function getAddressLine(): string
    {
        return $this->addressLine;
    }

    public function getAddressCity(): string
    {
        return $this->addressCity;
    }

    public function getAddressPostalCode(): string
    {
        return $this->addressPostalCode;
    }

    public function getAddressCountry(): string
    {
        return $this->addressCountry;
    }

    public function getFormattedCountry(): ?string
    {
        return ShopCountryModel::getInstance()->getCountryByCode($this->addressCountry)->getName();
    }

}