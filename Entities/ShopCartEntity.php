<?php

namespace CMW\Entity\Shop;

use CMW\Controller\Core\CoreController;
use CMW\Entity\Users\UserEntity;
use CMW\Manager\Env\EnvManager;
use CMW\Model\Shop\ShopItemsModel;
use CMW\Utils\Website;

class ShopCartEntity
{

    private int $cartId;
    private ?int $sessionId;
    private ?ShopItemEntity $item;
    private ?userEntity $user;
    private int $cartQuantity;
    private string $cartCreated;
    private string $cartUpdated;


    public function __construct(int $cartId, ?int $sessionId, ?ShopItemEntity $item, ?userEntity $user, int $cartQuantity, string $cartCreated, string $cartUpdated)
    {
        $this->cartId = $cartId;
        $this->sessionId = $sessionId;
        $this->item = $item;
        $this->user = $user;
        $this->cartQuantity = $cartQuantity;
        $this->cartCreated = $cartCreated;
        $this->cartUpdated = $cartUpdated;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->cartId;
    }

    /**
     * @return ?int
     */
    public function getSessionId(): ?int
    {
        return $this->sessionId;
    }

    /**
     * @return ?\CMW\Entity\Users\userEntity
     */
    public function getUser(): ?userEntity
    {
        return $this->user;
    }

    /**
     * @return ?\CMW\Entity\Shop\ShopItemEntity
     */
    public function getItem(): ?ShopItemEntity
    {
        return $this->item;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->cartQuantity;
    }

    /**
     * @return float
     */
    public function getTotalPrice(): float
    {
        //TODO : Gérer les promo
        $itemPrice = $this->item->getPrice();
        return $this->cartQuantity * $itemPrice;
    }

    /**
     * @return string
     */
    public function getCreated(): string
    {
        return CoreController::formatDate($this->cartCreated);
    }

    /**
     * @return string
     */
    public function getUpdate(): string
    {
        return CoreController::formatDate($this->cartUpdated);
    }

    /**
     * @return string
     */
    public function getIncreaseQuantityLink(): string
    {
        $itemId = $this->item->getId();
        return Website::getProtocol() . "://" . $_SERVER["SERVER_NAME"] . EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ."shop/cart/increase_quantity/$itemId";
    }

    /**
     * @return string
     */
    public function getDecreaseQuantityLink(): string
    {
        $itemId = $this->item->getId();
        return Website::getProtocol() . "://" . $_SERVER["SERVER_NAME"] . EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ."shop/cart/decrease_quantity/$itemId";
    }

    /**
     * @return string
     */
    public function getRemoveLink(): string
    {
        $itemId = $this->item->getId();
        return Website::getProtocol() . "://" . $_SERVER["SERVER_NAME"] . EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ."shop/cart/remove/$itemId";
    }

}