<?php

namespace CMW\Entity\Shop\Shippings;

use CMW\Interface\Shop\IShippingMethod;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractEntity;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Model\Shop\Shipping\ShopCoordinatesModel;

class ShopShippingEntity extends AbstractEntity
{
    private int $id;
    private string $name;
    private ?float $price;
    private ShopShippingZoneEntity $zoneEntity;
    private int $type;
    private ?int $alwaysDisplayed;
    private ?ShopShippingWithdrawPointEntity $withdrawPointEntity;
    private ?IShippingMethod $shippingMethod;
    private ?int $maxTotalWeight;
    private ?float $minTotalCartPrice;
    private ?float $maxTotalCartPrice;

    /**
     * @param int $id
     * @param string $name
     * @param float|null $price
     * @param \CMW\Entity\Shop\Shippings\ShopShippingZoneEntity $zoneEntity
     * @param int $type
     * @param ?int $alwaysDisplayed
     * @param \CMW\Entity\Shop\Shippings\ShopShippingWithdrawPointEntity|null $withdrawPointEntity
     * @param \CMW\Interface\Shop\IShippingMethod|null $shippingMethod
     * @param int|null $maxTotalWeight
     * @param float|null $minTotalCartPrice
     * @param float|null $maxTotalCartPrice
     */
    public function __construct(int $id, string $name, ?float $price, ShopShippingZoneEntity $zoneEntity, int $type, ?int $alwaysDisplayed, ?ShopShippingWithdrawPointEntity $withdrawPointEntity, ?IShippingMethod $shippingMethod, ?int $maxTotalWeight, ?float $minTotalCartPrice, ?float $maxTotalCartPrice)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->zoneEntity = $zoneEntity;
        $this->type = $type;
        $this->alwaysDisplayed = $alwaysDisplayed;
        $this->withdrawPointEntity = $withdrawPointEntity;
        $this->shippingMethod = $shippingMethod;
        $this->maxTotalWeight = $maxTotalWeight;
        $this->minTotalCartPrice = $minTotalCartPrice;
        $this->maxTotalCartPrice = $maxTotalCartPrice;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * @return string
     * @desc return the price for views
     */
    public function getPriceFormatted(): string
    {
        $formattedPrice = number_format($this->price, 2, '.', '');
        $symbol = ShopSettingsModel::getInstance()->getSettingValue('symbol');

        $symbolIsAfter = ShopSettingsModel::getInstance()->getSettingValue('after');
        if ($symbolIsAfter) {
            return $formattedPrice . $symbol;
        } else {
            return $symbol . $formattedPrice;
        }
    }

    public function getZone(): ShopShippingZoneEntity
    {
        return $this->zoneEntity;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getAlwaysDisplayed(): ?int
    {
        return $this->alwaysDisplayed;
    }

    public function getFormattedType(): string
    {
        if ($this->type === 0) {
            return LangManager::translate('shop.entities.shipping.shipping');
        }
        if ($this->type === 1) {
            return LangManager::translate('shop.entities.shipping.withdraw');
        }
        return '';
    }

    public function getWithdrawPoint(): ?ShopShippingWithdrawPointEntity
    {
        return $this->withdrawPointEntity;
    }

    public function getShippingMethod(): ?IShippingMethod
    {
        return $this->shippingMethod;
    }

    public function getMaxTotalWeight(): ?int
    {
        return $this->maxTotalWeight;
    }

    public function getMinTotalCartPrice(): ?float
    {
        return $this->minTotalCartPrice;
    }

    public function getMaxTotalCartPrice(): ?float
    {
        return $this->maxTotalCartPrice;
    }

    /**
     * @desc Permet d'afficher la distance entre le point de retrait et l'adresse du client
     * */
    public function getDistance(float $userAddressLatitude, float $userAddressLongitude): ?float
    {
        $distance = ShopCoordinatesModel::getInstance()->calculateDistance($userAddressLatitude, $userAddressLongitude, $this->getWithdrawPoint()->getLatitude(), $this->withdrawPointEntity->getLongitude());
        return round($distance, 2);
    }
}
