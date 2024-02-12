<?php

namespace CMW\Entity\Shop\Carts;

use CMW\Controller\Core\CoreController;
use CMW\Entity\Shop\Items\ShopItemEntity;
use CMW\Manager\Env\EnvManager;
use CMW\Model\Shop\Cart\ShopCartItemModel;
use CMW\Model\Shop\Image\ShopImagesModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Website;

class ShopCartItemEntity
{

    private int $id;
    private ShopCartEntity $cart;
    private ?ShopItemEntity $item;
    private int $cartQuantity;
    private string $cartCreated;
    private string $cartUpdated;
    private int $cartAside;


    public function __construct(int $id, ShopCartEntity $cart, ?ShopItemEntity $item, int $cartQuantity, string $cartCreated, string $cartUpdated, int $cartAside)
    {
        $this->id = $id;
        $this->cart = $cart;
        $this->item = $item;
        $this->cartQuantity = $cartQuantity;
        $this->cartCreated = $cartCreated;
        $this->cartUpdated = $cartUpdated;
        $this->cartAside = $cartAside;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return \CMW\Entity\Shop\Carts\ShopCartEntity
     */
    public function getSessionId(): ShopCartEntity
    {
        return $this->cart;
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
        $return = ShopImagesModel::getInstance()->getFirstImageByItemId($this->getItem()->getId());
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
        $cartContent = ShopCartItemModel::getInstance()->getShopCartsItemsByUserId(UsersModel::getCurrentUser()?->getId(), session_id());

        $total = 0;
        foreach ($cartContent as $itemPrice) {
            $total += $itemPrice->getTotalPrice();
        }
        return $total;
    }

    /**
     * @return float
     */
    public function getTotalCartPriceAfterDiscount(): float
    {
        //TODO : Gérer les promo
        return $this->getTotalCartPrice();
    }

    /**
     * @param int $paymentMethodFees
     * @param int $shippingFees
     * @return float
     * @desc Please use this method for final price after discounts, payment fees, shipping fees and more...
     */
    public function getTotalPriceComplete(int $paymentMethodFees, int $shippingFees): float
    {
        //TODO : J'aime pas les entity qui ont besoin de param (faudrait les faire passer via le model du CommandTunnel)
        $total = $this->getTotalCartPriceAfterDiscount();

        $total += $paymentMethodFees;

        $total += $shippingFees;

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
     * @return int
     */
    public function getAside(): int
    {
        return $this->cartAside;
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