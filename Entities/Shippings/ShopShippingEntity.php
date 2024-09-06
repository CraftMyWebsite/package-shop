<?php

namespace CMW\Entity\Shop\Shippings;

use CMW\Interface\Shop\IShippingMethod;

class ShopShippingEntity
{
    private int $id;
    private string $name;
    private ?float $price;
    private ShopShippingZoneEntity $zoneEntity;
    private int $type;
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
     * @param \CMW\Entity\Shop\Shippings\ShopShippingWithdrawPointEntity|null $withdrawPointEntity
     * @param \CMW\Interface\Shop\IShippingMethod|null $shippingMethod
     * @param int|null $maxTotalWeight
     * @param float|null $minTotalCartPrice
     * @param float|null $maxTotalCartPrice
     */
    public function __construct(int $id, string $name, ?float $price, ShopShippingZoneEntity $zoneEntity, int $type, ?ShopShippingWithdrawPointEntity $withdrawPointEntity, ?IShippingMethod $shippingMethod, ?int $maxTotalWeight, ?float $minTotalCartPrice, ?float $maxTotalCartPrice)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->zoneEntity = $zoneEntity;
        $this->type = $type;
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

    public function getZone(): ShopShippingZoneEntity
    {
        return $this->zoneEntity;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getFormattedType(): string
    {
        if ($this->type === 0) {
            return "Livraison";
        }
        if ($this->type === 1) {
            return "A emporter";
        }
        return "";
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

}