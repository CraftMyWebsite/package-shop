<?php

namespace CMW\Entity\Shop;

use CMW\Controller\Core\CoreController;
use CMW\Entity\Shop\Items\ShopItemEntity;
use CMW\Entity\Users\UserEntity;
use CMW\Manager\Env\EnvManager;
use CMW\Model\Shop\ShopCartsModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Website;

class ShopCartEntity
{

    private int $cartId;
    private ?string $sessionId;
    private ?ShopItemEntity $item;
    private ?userEntity $user;
    private int $cartQuantity;
    private string $cartCreated;
    private string $cartUpdated;


    public function __construct(int $cartId, ?string $sessionId, ?ShopItemEntity $item, ?userEntity $user, int $cartQuantity, string $cartCreated, string $cartUpdated)
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
     * @return ?string
     */
    public function getSessionId(): ?string
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
     * @return ?\CMW\Entity\Shop\Items\ShopItemEntity
     */
    public function getItem(): ?ShopItemEntity
    {
        return $this->item;
    }

    /**
     * @return string
     */
    public function getFirstImageItemUrl(): string
    {
        $return = ShopCartsModel::getInstance()->getFirstImageByItemId($this->getItem()->getId());
        return EnvManager::getInstance()->getValue("PATH_SUBFOLDER") . "Public/Uploads/Shop/" . $return;
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
        $itemPrice = $this->item->getPrice();
        return $this->cartQuantity * $itemPrice;
    }

    /**
     * @return float
     */
    public function getTotalPriceAfterDiscount(): float
    {
        //TODO : Gérer les promo
        $itemPrice = $this->item->getPrice();
        return $this->cartQuantity * $itemPrice;
    }

    /**
     * @return float
     */
    public function getTotalCartPrice(): float
    {
        $total = 0;
        foreach (ShopCartsModel::getInstance()->getShopCartsByUserId(UsersModel::getCurrentUser()?->getId(), session_id()) as $test) {
            $total += $test->getTotalPrice();
        }
        return $total;
    }

    /**
     * @return float
     */
    public function getTotalCartPriceAfterDiscount(): float
    {
        //TODO : Gérer les promo
        $total = 0;
        foreach (ShopCartsModel::getInstance()->getShopCartsByUserId(UsersModel::getCurrentUser()?->getId(), session_id()) as $test) {
            $total += $test->getTotalPrice();
        }

        return number_format($total, 2);
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
        return Website::getProtocol() . "://" . $_SERVER["SERVER_NAME"] . EnvManager::getInstance()->getValue("PATH_SUBFOLDER") . "shop/cart/increase_quantity/$itemId";
    }

    /**
     * @return string
     */
    public function getDecreaseQuantityLink(): string
    {
        $itemId = $this->item->getId();
        return Website::getProtocol() . "://" . $_SERVER["SERVER_NAME"] . EnvManager::getInstance()->getValue("PATH_SUBFOLDER") . "shop/cart/decrease_quantity/$itemId";
    }

    /**
     * @return string
     */
    public function getRemoveLink(): string
    {
        $itemId = $this->item->getId();
        return Website::getProtocol() . "://" . $_SERVER["SERVER_NAME"] . EnvManager::getInstance()->getValue("PATH_SUBFOLDER") . "shop/cart/remove/$itemId";
    }

}