<?php

namespace CMW\Entity\Shop\HistoryOrders;

use CMW\Manager\Package\AbstractEntity;
use CMW\Manager\Security\EncryptManager;
use CMW\Model\Shop\Country\ShopCountryModel;

class ShopHistoryOrdersUserAddressEntity extends AbstractEntity
{
    private int $historyOrderUserAddressId;
    private ShopHistoryOrdersEntity $historyOrder;
    private ?string $historyOrderUserAddressName;
    private ?string $historyOrderUserAddressMail;
    private ?string $historyOrderUserAddressLastName;
    private ?string $historyOrderUserAddressFirstName;
    private ?string $historyOrderUserAddressLine1;
    private ?string $historyOrderUserAddressLine2;
    private ?string $historyOrderUserAddressCity;
    private ?string $historyOrderUserAddressPostalCode;
    private ?string $historyOrderUserAddressCountry;
    private ?string $historyOrderUserAddressPhone;

    /**
     * @param int $historyOrderUserAddressId
     * @param \CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity $historyOrder
     * @param string|null $historyOrderUserAddressName
     * @param string|null $historyOrderUserAddressMail
     * @param string|null $historyOrderUserAddressLastName
     * @param string|null $historyOrderUserAddressFirstName
     * @param string|null $historyOrderUserAddressLine1
     * @param string|null $historyOrderUserAddressLine2
     * @param string|null $historyOrderUserAddressCity
     * @param string|null $historyOrderUserAddressPostalCode
     * @param string|null $historyOrderUserAddressCountry
     * @param string|null $historyOrderUserAddressPhone
     */
    public function __construct(int $historyOrderUserAddressId, ShopHistoryOrdersEntity $historyOrder, ?string $historyOrderUserAddressName, ?string $historyOrderUserAddressMail, ?string $historyOrderUserAddressLastName, ?string $historyOrderUserAddressFirstName, ?string $historyOrderUserAddressLine1, ?string $historyOrderUserAddressLine2, ?string $historyOrderUserAddressCity, ?string $historyOrderUserAddressPostalCode, ?string $historyOrderUserAddressCountry, ?string $historyOrderUserAddressPhone)
    {
        $this->historyOrderUserAddressId = $historyOrderUserAddressId;
        $this->historyOrder = $historyOrder;
        $this->historyOrderUserAddressName = $historyOrderUserAddressName;
        $this->historyOrderUserAddressMail = $historyOrderUserAddressMail;
        $this->historyOrderUserAddressLastName = $historyOrderUserAddressLastName;
        $this->historyOrderUserAddressFirstName = $historyOrderUserAddressFirstName;
        $this->historyOrderUserAddressLine1 = $historyOrderUserAddressLine1;
        $this->historyOrderUserAddressLine2 = $historyOrderUserAddressLine2;
        $this->historyOrderUserAddressCity = $historyOrderUserAddressCity;
        $this->historyOrderUserAddressPostalCode = $historyOrderUserAddressPostalCode;
        $this->historyOrderUserAddressCountry = $historyOrderUserAddressCountry;
        $this->historyOrderUserAddressPhone = $historyOrderUserAddressPhone;
    }

    public function getId(): int
    {
        return $this->historyOrderUserAddressId;
    }

    public function getHistoryOrder(): ShopHistoryOrdersEntity
    {
        return $this->historyOrder;
    }

    public function getName(): ?string
    {
        return $this->historyOrderUserAddressName;
    }

    public function getUserMail(): ?string
    {
        return EncryptManager::decrypt($this->historyOrderUserAddressMail);
    }

    public function getUserLastName(): ?string
    {
        return $this->historyOrderUserAddressLastName;
    }

    public function getUserFirstName(): ?string
    {
        return $this->historyOrderUserAddressFirstName;
    }

    public function getUserLine1(): ?string
    {
        return EncryptManager::decrypt($this->historyOrderUserAddressLine1);
    }

    public function getUserLine2(): ?string
    {
        return EncryptManager::decrypt($this->historyOrderUserAddressLine2);
    }

    public function getUserCity(): ?string
    {
        return EncryptManager::decrypt($this->historyOrderUserAddressCity);
    }

    public function getUserPostalCode(): ?string
    {
        return EncryptManager::decrypt($this->historyOrderUserAddressPostalCode);
    }

    public function getUserCountry(): ?string
    {
        return $this->historyOrderUserAddressCountry;
    }

    public function getUserFormattedCountry(): ?string
    {
        return ShopCountryModel::getInstance()->getCountryByCode($this->historyOrderUserAddressCountry)->getName();
    }

    public function getUserPhone(): ?string
    {
        return EncryptManager::decrypt($this->historyOrderUserAddressPhone);
    }
}
