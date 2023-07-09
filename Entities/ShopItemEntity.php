<?php

namespace CMW\Entity\Shop;

use CMW\Controller\Core\CoreController;
use CMW\Manager\Env\EnvManager;
use CMW\Utils\Website;

class ShopItemEntity
{

    private int $itemId;
    private ?int $categoryId;
    private ?string $itemName;
    private string $itemDescription;
    private string $itemSlug;
    private ?int $itemImage;
    private int $itemType;
    private ?int $itemDefaultStock;
    private ?int $itemCurrentStock;
    private ?float $itemPrice;
    private ?int $itemGlobalLimit;
    private ?int $itemUserLimit;
    private string $itemCreated;
    private string $itemUpdated;


    public function __construct(int $itemId, ?int $categoryId, ?string $itemName, string $itemDescription, string $itemSlug, ?int $itemImage, int $itemType, ?int $itemDefaultStock, ?int $itemCurrentStock, ?float $itemPrice, ?int $itemGlobalLimit, ?int $itemUserLimit, string $itemCreated, string $itemUpdated)
    {
        $this->itemId = $itemId;
        $this->categoryId = $categoryId;
        $this->itemName = $itemName;
        $this->itemDescription = $itemDescription;
        $this->itemSlug = $itemSlug;
        $this->itemImage = $itemImage;
        $this->itemType = $itemType;
        $this->itemDefaultStock = $itemDefaultStock;
        $this->itemCurrentStock = $itemCurrentStock;
        $this->itemPrice = $itemPrice;
        $this->itemGlobalLimit = $itemGlobalLimit;
        $this->itemUserLimit = $itemUserLimit;
        $this->itemCreated = $itemCreated;
        $this->itemUpdated = $itemUpdated;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->itemId;
    }

    /**
     * @return int
     */
    public function getCategoryId(): ?int
    {
        return $this->categoryId;
    }


    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->itemName;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->itemDescription;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->itemSlug;
    }

    /**
     * @return int
     */
    public function getImage(): ?int
    {
        return $this->itemImage;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->itemType;
    }

    /**
     * @return int
     */
    public function getDefaultStock(): ?int
    {
        return $this->itemDefaultStock;
    }

    /**
     * @return int
     */
    public function getCurrentStock(): ?int
    {
        return $this->itemCurrentStock;
    }

    /**
     * @return float
     */
    public function getPrice(): ?float
    {
        return $this->itemPrice;
    }

    /**
     * @return int
     */
    public function getGlobalLimit(): ?int
    {
        return $this->itemGlobalLimit;
    }

    /**
     * @return int
     */
    public function getUserLimit(): ?int
    {
        return $this->itemUserLimit;
    }

    /**
     * @return string
     */
    public function getCreated(): string
    {
        return CoreController::formatDate($this->itemCreated);
    }

    /**
     * @return string
     */
    public function getUpdate(): string
    {
        return CoreController::formatDate($this->itemUpdated);
    }

    /**
     * @return string
     * @param string $catSlug
     */
    public function getFullLink($catSlug): string
    {
        return Website::getProtocol() . "://" . $_SERVER["SERVER_NAME"] . EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ."shop/cat/$catSlug/item/$this->itemSlug";
    }

}