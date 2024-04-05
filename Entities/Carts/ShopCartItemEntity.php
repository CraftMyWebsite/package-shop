<?php

namespace CMW\Entity\Shop\Carts;

use CMW\Controller\Core\CoreController;
use CMW\Entity\Shop\Discounts\ShopDiscountEntity;
use CMW\Entity\Shop\Items\ShopItemEntity;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Model\Shop\Cart\ShopCartDiscountModel;
use CMW\Model\Shop\Cart\ShopCartItemModel;
use CMW\Model\Shop\Command\ShopCommandTunnelModel;
use CMW\Model\Shop\Delivery\ShopShippingModel;
use CMW\Model\Shop\Discount\ShopDiscountCategoriesModel;
use CMW\Model\Shop\Discount\ShopDiscountItemsModel;
use CMW\Model\Shop\Discount\ShopDiscountModel;
use CMW\Model\Shop\Image\ShopImagesModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Website;

class ShopCartItemEntity
{

    private int $id;
    private ShopCartEntity $cart;
    private ?ShopItemEntity $item;
    private ?ShopDiscountEntity $discount;
    private int $cartQuantity;
    private string $cartCreated;
    private string $cartUpdated;
    private int $cartAside;


    public function __construct(int $id, ShopCartEntity $cart, ?ShopItemEntity $item, ?ShopDiscountEntity $discount, int $cartQuantity, string $cartCreated, string $cartUpdated, int $cartAside)
    {
        $this->id = $id;
        $this->cart = $cart;
        $this->item = $item;
        $this->discount = $discount;
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
     * @return ?\CMW\Entity\Shop\Discounts\ShopDiscountEntity
     */
    public function getDiscount(): ?ShopDiscountEntity
    {
        return $this->discount;
    }

    /**
     * @return ?string
     */
    public function getDiscountFormatted(): ?string
    {
        $symbol = ShopSettingsModel::getInstance()->getSettingValue("symbol");
        $symbolIsAfter = ShopSettingsModel::getInstance()->getSettingValue("after");

        if (!is_null($this->discount)) {
            if ($this->discount->getDiscountQuantityImpacted() == 1) {
                if ($this->discount->getPrice()) {
                    if ($symbolIsAfter) {
                        return "- " . $this->discount->getPrice() . $symbol;
                    } else {
                        return "- " . $symbol . $this->discount->getPrice();
                    }
                }
                if ($this->discount->getPercentage()) {
                    return "- " . $this->discount->getPercentage() . "%";
                }
            } else {
                if ($this->discount->getPrice()) {
                    if ($symbolIsAfter) {
                        return "- " . $this->discount->getPrice() . $symbol . " sur le 1er article";
                    } else {
                        return "- " . $symbol . $this->discount->getPrice() . " sur le 1er article";
                    }
                }
                if ($this->discount->getPercentage()) {
                    return "- " . $this->discount->getPercentage() . "% sur le 1er article";
                }
            }
        }
        return null;
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
     * @desc Use for count the total price of one item in the cart with default applied discount
     */
    public function getItemTotalPrice(): float
    {
        $allDiscounts = ShopDiscountModel::getInstance()->getShopDiscountsDefaultAppliedForAll();
        $discountCategories = ShopDiscountCategoriesModel::getInstance()->getShopDiscountCategoriesDefaultAppliedByCategoryId($this->getItem()->getCategory()->getId());
        $discountItems = ShopDiscountItemsModel::getInstance()->getShopDiscountItemsDefaultAppliedByItemId($this->getItem()->getId());
        $quantityImpacted = 0;
        if (!empty($allDiscounts)) {
            foreach ($allDiscounts as $allDiscount) {
                if ($allDiscount->getDiscountQuantityImpacted() == 1) {
                    $quantityImpacted = 1;
                }
            }
        }
        if (!empty($discountCategories)) {
            foreach ($discountCategories as $discountCategory) {
                if ($discountCategory->getDiscount()->getDiscountQuantityImpacted() == 1) {
                    $quantityImpacted = 1;
                }
            }
        }
        if (!empty($discountItems)) {
            foreach ($discountItems as $discountItem) {
                if ($discountItem->getDiscount()->getDiscountQuantityImpacted() == 1) {
                    $quantityImpacted = 1;
                }
            }
        }

        if ($this->item->getPriceDiscountDefaultApplied()) {
            if ($quantityImpacted == 0) {
                $itemPrice = $this->item->getPriceDiscountDefaultApplied() + ($this->cartQuantity-1) * $this->item->getPrice();
            } else {
                $itemPrice = $this->cartQuantity * $this->item->getPriceDiscountDefaultApplied();
            }
        } else {
            $itemPrice = $this->cartQuantity * $this->item->getPrice();
        }

        return $itemPrice;
    }

    /**
     * @return string
     * @desc return the price for views
     */
    public function getItemTotalPriceFormatted(): string
    {
        $formattedPrice = number_format($this->getItemTotalPrice(), 2, '.', '');
        $symbol = ShopSettingsModel::getInstance()->getSettingValue("symbol");
        $symbolIsAfter = ShopSettingsModel::getInstance()->getSettingValue("after");
        if ($symbolIsAfter) {
            return $formattedPrice .  $symbol;
        } else {
            return $symbol .  $formattedPrice;
        }
    }

    /**
     * @return float
     * @desc Use for count the total price of one item in the cart
     */
    public function getItemTotalPriceBeforeDiscount(): float
    {
        return $this->cartQuantity * $this->item->getPrice();
    }

    /**
     * @return float
     * @desc Use for count the total price of one item in the cart
     */
    public function getItemTotalPriceAfterDiscount(): float
    {
        $basePrice = $this->getItemTotalPrice();
        $discount = 0;
        if (!is_null($this->discount)) {
            if ($this->discount->getDiscountQuantityImpacted() == 1) {
                if ($this->discount->getPrice()) {
                    $discount = $this->discount->getPrice() * $this->cartQuantity;
                }
                if ($this->discount->getPercentage()) {
                    $discount = ($basePrice*$this->discount->getPercentage())/100;
                }
            } else {
                if ($this->discount->getPrice()) {
                    $discount = $this->discount->getPrice();
                }
                if ($this->discount->getPercentage()) {
                        $discount = ($this->getItem()->getPrice()*$this->discount->getPercentage()/100);
                }
            }
            return number_format($basePrice - $discount, 2, '.', '');
        }
        return $basePrice;
    }

    /**
     * @return string
     * @desc return the price for views
     */
    public function getItemTotalPriceAfterDiscountFormatted(): string
    {
        $formattedPrice = number_format($this->getItemTotalPriceAfterDiscount(), 2, '.', '');
        $symbol = ShopSettingsModel::getInstance()->getSettingValue("symbol");
        $symbolIsAfter = ShopSettingsModel::getInstance()->getSettingValue("after");
        if ($symbolIsAfter) {
            return $formattedPrice .  $symbol;
        } else {
            return $symbol .  $formattedPrice;
        }
    }

    /**
     * @return float
     * @desc use for count the total price of all items in the cart (Is never the final price)
     */
    public function getTotalCartPriceBeforeDiscount(): float
    {
        $cartContents = ShopCartItemModel::getInstance()->getShopCartsItemsByUserId(UsersModel::getCurrentUser()?->getId(), session_id());

        $total = 0;
        foreach ($cartContents as $cartContent) {
            $total += $cartContent->getItemTotalPrice();
        }
        return $total;
    }

    /**
     * @return string
     * @desc return the price for views
     */
    public function getTotalCartPriceBeforeDiscountFormatted(): string
    {
        $formattedPrice = number_format($this->getTotalCartPriceBeforeDiscount(), 2, '.', '');
        $symbol = ShopSettingsModel::getInstance()->getSettingValue("symbol");
        $symbolIsAfter = ShopSettingsModel::getInstance()->getSettingValue("after");
        if ($symbolIsAfter) {
            return $formattedPrice . $symbol;
        } else {
            return $symbol . $formattedPrice;
        }
    }

    /**
     * @return float
     * @desc use for count the total price of all items in the cart including discount (Is always the final CART view price but never the final price)
     */
    public function getTotalCartPriceAfterDiscount(): float
    {
        $cartContents = ShopCartItemModel::getInstance()->getShopCartsItemsByUserId(UsersModel::getCurrentUser()?->getId(), session_id());
        $cartDiscounts = ShopCartDiscountModel::getInstance()->getCartDiscountByUserId(UsersModel::getCurrentUser()?->getId(), session_id());

        $totalCartDiscountPrice = 0;
        foreach ($cartDiscounts as $cartDiscount) {
            if ($cartDiscount->getDiscount()->getLinked() == 3) {
                $totalCartDiscountPrice += $cartDiscount->getDiscount()->getPrice();
            }
        }

        $total = 0;
        foreach ($cartContents as $cartContent) {
            $total += $cartContent->getItemTotalPriceAfterDiscount();
        }
        $total -= $totalCartDiscountPrice;

        return $total;
    }

    /**
     * @return string
     * @desc return the price for views
     */
    public function getTotalCartPriceAfterDiscountFormatted(): string
    {
        $formattedPrice = number_format($this->getTotalCartPriceAfterDiscount(), 2, '.', '');
        $symbol = ShopSettingsModel::getInstance()->getSettingValue("symbol");
        $symbolIsAfter = ShopSettingsModel::getInstance()->getSettingValue("after");
        if ($symbolIsAfter) {
            return $formattedPrice .  $symbol;
        } else {
            return $symbol .  $formattedPrice;
        }
    }

    /**
     * @return float
     * @desc Use for count the total final price including all discounts, payment fees, shipping fees and more... (Is always the final price used in payment interface)
     */
    public function getTotalPriceComplete(): float
    {
        $commandTunnel = ShopCommandTunnelModel::getInstance()->getShopCommandTunnelByUserId(UsersModel::getCurrentUser()?->getId());

        $shipping = $commandTunnel->getShipping();
        $shippingFees = $shipping !== null ? $shipping->getPrice() : 0;

        $total = $this->getTotalCartPriceAfterDiscount();

        $total += $shippingFees;

        return number_format($total, 2, '.', '');
    }

    /**
     * @return string
     * @desc return the price for views
     */
    public function getTotalPriceCompleteFormatted(): string
    {
        $formattedPrice = number_format($this->getTotalPriceComplete(), 2, '.', '');
        $symbol = ShopSettingsModel::getInstance()->getSettingValue("symbol");
        $symbolIsAfter = ShopSettingsModel::getInstance()->getSettingValue("after");
        if ($symbolIsAfter) {
            return $formattedPrice . $symbol;
        } else {
            return $symbol . $formattedPrice;
        }
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