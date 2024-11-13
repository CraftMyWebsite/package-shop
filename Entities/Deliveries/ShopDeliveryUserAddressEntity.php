<?php

namespace CMW\Entity\Shop\Deliveries;

use CMW\Manager\Package\AbstractEntity;
use CMW\Manager\Security\EncryptManager;
use CMW\Utils\Date;
use CMW\Entity\Users\UserEntity;
use CMW\Model\Shop\Country\ShopCountryModel;

class ShopDeliveryUserAddressEntity extends AbstractEntity
{
    private int $deliveryId;
    private ?string $deliveryLabel;
    private int $deliveryFav;
    private ?UserEntity $user;
    private ?string $deliveryFirstName;
    private ?string $deliveryLastName;
    private ?string $deliveryLine1;
    private ?string $deliveryLine2;
    private ?string $deliveryCity;
    private ?string $deliveryPostalCode;
    private ?string $deliveryCountry;
    private ?string $deliveryPhone;
    private string $deliveryLatitude;
    private string $deliveryLongitude;
    private string $deliveryCreated;
    private string $deliveryUpdated;

    /**
     * @param int $deliveryId
     * @param string|null $deliveryLabel
     * @param int $deliveryFav
     * @param \CMW\Entity\Users\UserEntity|null $user
     * @param string|null $deliveryFirstName
     * @param string|null $deliveryLastName
     * @param string|null $deliveryLine1
     * @param string|null $deliveryLine2
     * @param string|null $deliveryCity
     * @param string|null $deliveryPostalCode
     * @param string|null $deliveryCountry
     * @param string|null $deliveryPhone
     * @param string|null $deliveryLatitude
     * @param string|null $deliveryLongitude
     * @param string $deliveryCreated
     * @param string $deliveryUpdated
     */
    public function __construct(int $deliveryId, int $deliveryFav, ?string $deliveryLabel, ?UserEntity $user, ?string $deliveryFirstName, ?string $deliveryLastName, ?string $deliveryLine1, ?string $deliveryLine2, ?string $deliveryCity, ?string $deliveryPostalCode, ?string $deliveryCountry, ?string $deliveryPhone, ?string $deliveryLatitude, ?string $deliveryLongitude, string $deliveryCreated, string $deliveryUpdated)
    {
        $this->deliveryId = $deliveryId;
        $this->deliveryFav = $deliveryFav;
        $this->deliveryLabel = $deliveryLabel;
        $this->user = $user;
        $this->deliveryFirstName = $deliveryFirstName;
        $this->deliveryLastName = $deliveryLastName;
        $this->deliveryLine1 = $deliveryLine1;
        $this->deliveryLine2 = $deliveryLine2;
        $this->deliveryCity = $deliveryCity;
        $this->deliveryPostalCode = $deliveryPostalCode;
        $this->deliveryCountry = $deliveryCountry;
        $this->deliveryPhone = $deliveryPhone;
        $this->deliveryLatitude = $deliveryLatitude;
        $this->deliveryLongitude = $deliveryLongitude;
        $this->deliveryCreated = $deliveryCreated;
        $this->deliveryUpdated = $deliveryUpdated;
    }

    public function getId(): int
    {
        return $this->deliveryId;
    }

    public function getIsFav(): int
    {
        return $this->deliveryFav;
    }

    public function getLabel(): ?string
    {
        return $this->deliveryLabel;
    }

    public function getUser(): ?UserEntity
    {
        return $this->user;
    }

    public function getFirstName(): ?string
    {
        return $this->deliveryFirstName;
    }

    public function getLastName(): ?string
    {
        return $this->deliveryLastName;
    }

    public function getLine1(): ?string
    {
        return EncryptManager::decrypt($this->deliveryLine1);
    }

    public function getLine2(): ?string
    {
        return EncryptManager::decrypt($this->deliveryLine2);
    }

    public function getCity(): ?string
    {
        return EncryptManager::decrypt($this->deliveryCity);
    }

    public function getPostalCode(): ?string
    {
        return EncryptManager::decrypt($this->deliveryPostalCode);
    }

    public function getCountry(): ?string
    {
        return $this->deliveryCountry;
    }

    public function getFormattedCountry(): ?string
    {
        return ShopCountryModel::getInstance()->getCountryByCode($this->deliveryCountry)?->getName();
    }

    public function getPhone(): ?string
    {
        return EncryptManager::decrypt($this->deliveryPhone);
    }

    public function getCreated(): string
    {
        return Date::formatDate($this->deliveryCreated);
    }

    public function getUpdated(): string
    {
        return Date::formatDate($this->deliveryUpdated);
    }

    public function getLatitude(): string
    {
        return EncryptManager::decrypt($this->deliveryLatitude);
    }

    public function getLongitude(): string
    {
        return EncryptManager::decrypt($this->deliveryLongitude);
    }
}
