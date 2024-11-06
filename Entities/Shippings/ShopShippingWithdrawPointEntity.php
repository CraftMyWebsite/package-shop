<?php

namespace CMW\Entity\Shop\Shippings;

use CMW\Manager\Security\EncryptManager;
use CMW\Model\Shop\Country\ShopCountryModel;

class ShopShippingWithdrawPointEntity
{
    private int $id;
    private string $name;
    private ?int $addressDistance;
    private string $addressLine;
    private string $addressCity;
    private string $addressPostalCode;
    private string $addressLatitude;
    private string $addressLongitude;
    private string $addressCountry;

    /**
     * @param int $id
     * @param string $name
     * @param int|null $addressDistance
     * @param string $addressLine
     * @param string $addressCity
     * @param string $addressPostalCode
     * @param string $addressLatitude
     * @param string $addressLongitude
     * @param string $addressCountry
     */
    public function __construct(int $id, string $name, ?int $addressDistance, string $addressLine, string $addressCity, string $addressPostalCode, string $addressLatitude, string $addressLongitude, string $addressCountry)
    {
        $this->id = $id;
        $this->name = $name;
        $this->addressDistance = $addressDistance;
        $this->addressLine = $addressLine;
        $this->addressCity = $addressCity;
        $this->addressPostalCode = $addressPostalCode;
        $this->addressLatitude = $addressLatitude;
        $this->addressLongitude = $addressLongitude;
        $this->addressCountry = $addressCountry;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAddressDistance(): ?int
    {
        return $this->addressDistance;
    }

    public function getAddressLine(): string
    {
        return EncryptManager::decrypt($this->addressLine);
    }

    public function getAddressCity(): string
    {
        return EncryptManager::decrypt($this->addressCity);
    }

    public function getAddressPostalCode(): string
    {
        return EncryptManager::decrypt($this->addressPostalCode);
    }

    public function getAddressCountry(): string
    {
        return $this->addressCountry;
    }

    public function getFormattedCountry(): ?string
    {
        return ShopCountryModel::getInstance()->getCountryByCode($this->addressCountry)->getName();
    }

    public function getLatitude(): string
    {
        return EncryptManager::decrypt($this->addressLatitude);
    }

    public function getLongitude(): string
    {
        return EncryptManager::decrypt($this->addressLongitude);
    }
}
