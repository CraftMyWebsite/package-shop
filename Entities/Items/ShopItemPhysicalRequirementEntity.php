<?php

namespace CMW\Entity\Shop\Items;


use CMW\Controller\Core\CoreController;

class ShopItemPhysicalRequirementEntity
{
    private int $physicalRequirementId;
    private ShopItemEntity $shopItemId;
    private ?float $physicalRequirementWeight;
    private ?float $physicalRequirementLength;
    private ?float $physicalRequirementWidth;
    private ?float $physicalRequirementHeight;
    private string $physicalRequirementCreated;
    private string $physicalRequirementUpdated;

    /**
     * @param int $physicalRequirementId
     * @param int $shopItemId
     * @param float|null $physicalRequirementWeight
     * @param float|null $physicalRequirementLength
     * @param float|null $physicalRequirementWidth
     * @param float|null $physicalRequirementHeight
     * @param string $physicalRequirementCreated
     * @param string $physicalRequirementUpdated
     */
    public function __construct(int $physicalRequirementId, ShopItemEntity $shopItemId, ?float $physicalRequirementWeight, ?float $physicalRequirementLength, ?float $physicalRequirementWidth, ?float $physicalRequirementHeight, string $physicalRequirementCreated, string $physicalRequirementUpdated)
    {
        $this->physicalRequirementId = $physicalRequirementId;
        $this->shopItemId = $shopItemId;
        $this->physicalRequirementWeight = $physicalRequirementWeight;
        $this->physicalRequirementLength = $physicalRequirementLength;
        $this->physicalRequirementWidth = $physicalRequirementWidth;
        $this->physicalRequirementHeight = $physicalRequirementHeight;
        $this->physicalRequirementCreated = $physicalRequirementCreated;
        $this->physicalRequirementUpdated = $physicalRequirementUpdated;
    }

    public function getId(): int
    {
        return $this->physicalRequirementId;
    }

    public function getShopItem(): int
    {
        return $this->shopItemId;
    }

    public function getWeight(): ?float
    {
        return $this->physicalRequirementWeight;
    }

    public function getLength(): ?float
    {
        return $this->physicalRequirementLength;
    }

    public function getWidth(): ?float
    {
        return $this->physicalRequirementWidth;
    }

    public function getHeight(): ?float
    {
        return $this->physicalRequirementHeight;
    }

    public function getCreated(): string
    {
        return CoreController::formatDate($this->physicalRequirementCreated);
    }

    public function getUpdated(): string
    {
        return CoreController::formatDate($this->physicalRequirementUpdated);
    }
}