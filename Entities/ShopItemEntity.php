<?php

namespace CMW\Entity\Shop;

use CMW\Controller\Core\CoreController;
use CMW\Manager\Env\EnvManager;
use CMW\Model\Shop\ShopCartsModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Website;

class ShopItemEntity
{

    private int $itemId;
    private ?ShopCategoryEntity $category;
    private ?string $itemName;
    private string $itemDescription;
    private string $itemShortDescription;
    private string $itemSlug;
    private ?int $itemImage;
    private int $itemType;
    private ?int $itemDefaultStock;
    private ?int $itemCurrentStock;
    private ?float $itemPrice;
    private ?int $itemByOrderLimit;
    private ?int $itemGlobalLimit;
    private ?int $itemUserLimit;
    private string $itemCreated;
    private string $itemUpdated;


    public function __construct(int $itemId, ?ShopCategoryEntity $category, ?string $itemName, string $itemDescription, string $itemShortDescription, string $itemSlug, ?int $itemImage, int $itemType, ?int $itemDefaultStock, ?int $itemCurrentStock, ?float $itemPrice, ?int $itemByOrderLimit, ?int $itemGlobalLimit, ?int $itemUserLimit, string $itemCreated, string $itemUpdated)
    {
        $this->itemId = $itemId;
        $this->category = $category;
        $this->itemName = $itemName;
        $this->itemDescription = $itemDescription;
        $this->itemShortDescription = $itemShortDescription;
        $this->itemSlug = $itemSlug;
        $this->itemImage = $itemImage;
        $this->itemType = $itemType;
        $this->itemDefaultStock = $itemDefaultStock;
        $this->itemCurrentStock = $itemCurrentStock;
        $this->itemPrice = $itemPrice;
        $this->itemByOrderLimit = $itemByOrderLimit;
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
     * @return ?ShopCategoryEntity
     */
    public function getCategory(): ?ShopCategoryEntity
    {
        return $this->category;
    }


    /**
     * @return ?string
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
    public function getShortDescription(): string
    {
        return $this->itemShortDescription;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->itemSlug;
    }

    /**
     * @return ?int
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
     * @return ?int
     */
    public function getDefaultStock(): ?int
    {
        return $this->itemDefaultStock;
    }

    /**
     * @return ?int
     */
    public function getCurrentStock(): ?int
    {
        return $this->itemCurrentStock;
    }

    /**
     * @return ?string
     */
    public function getFormatedStock(): ?string
    {
        if (is_null($this->getDefaultStock())) {
            return "<b style='color: #0ab312'>Illimité</b>";
        } else {
            return $this->itemCurrentStock ." / ". $this->itemDefaultStock;
        }
    }

    /**
     * @return ?float
     */
    public function getPrice(): ?float
    {
        return $this->itemPrice;
    }

    /**
     * @return ?int
     */
    public function getByOrderLimit(): ?int
    {
        return $this->itemByOrderLimit;
    }

    /**
     * @return ?int
     */
    public function getGlobalLimit(): ?int
    {
        return $this->itemGlobalLimit;
    }

    /**
     * @return ?int
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
     */
    public function getItemLink(): string
    {
        $catSlug = $this->getCategory()->getSlug();
        return Website::getProtocol() . "://" . $_SERVER["SERVER_NAME"] . EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ."shop/cat/$catSlug/item/$this->itemSlug";
    }

    /**
     * @return string
     */
    public function getAddToCartLink(): string
    {
        return Website::getProtocol() . "://" . $_SERVER["SERVER_NAME"] . EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ."shop/add_to_cart/$this->itemId";
    }



    /*
     * ++ Cool features
     * */

    /**
     * @return float
     * @desc perfect for get the total price in cart
     */
    public function getTotalPriceInCart(): float
    {
        //TODO : Gérer les promo
        $quantity = ShopCartsModel::getInstance()->getQuantity($this->itemId, UsersModel::getCurrentUser()?->getId(), session_id());
        return $quantity * $this->getPrice();
    }

    /**
     * @return string
     * @desc perfect if you retrieve the quantities in the cart from the item page
     */
    public function getQuantityInCart(): string
    {
        return ShopCartsModel::getInstance()->getQuantity($this->itemId, UsersModel::getCurrentUser()?->getId(), session_id());
    }

    /**
     * @return string
     * @desc perfect if you want to manage quantities in the cart from the item page
     */
    public function getIncreaseQuantityCartLink(): string
    {
        return Website::getProtocol() . "://" . $_SERVER["SERVER_NAME"] . EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ."shop/cart/increase_quantity/$this->itemId";
    }

    /**
     * @return string
     * @desc perfect if you want to manage quantities in the cart from the item page
     */
    public function getDecreaseQuantityCartLink(): string
    {
        return Website::getProtocol() . "://" . $_SERVER["SERVER_NAME"] . EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ."shop/cart/decrease_quantity/$this->itemId";
    }

    /**
     * @return string
     * @desc perfect if you want to manage quantities in the cart from the item page
     */
    public function getRemoveCartLink(): string
    {
        return Website::getProtocol() . "://" . $_SERVER["SERVER_NAME"] . EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ."shop/cart/remove/$this->itemId";
    }

}