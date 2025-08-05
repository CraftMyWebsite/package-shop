<?php

namespace CMW\Entity\Shop\Items;

use CMW\Controller\Users\UsersSessionsController;
use CMW\Entity\Shop\Enum\Item\ShopItemType;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractEntity;
use CMW\Utils\Date;
use CMW\Controller\Shop\Admin\Payment\ShopPaymentsController;
use CMW\Entity\Shop\Categories\ShopCategoryEntity;
use CMW\Entity\Shop\Discounts\ShopDiscountEntity;
use CMW\Manager\Env\EnvManager;
use CMW\Model\Shop\Cart\ShopCartItemModel;
use CMW\Model\Shop\Discount\ShopDiscountCategoriesModel;
use CMW\Model\Shop\Discount\ShopDiscountItemsModel;
use CMW\Model\Shop\Discount\ShopDiscountModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Utils\Website;

class ShopItemEntity extends AbstractEntity
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
    private string $itemPriceType;
    private ?int $itemByOrderLimit;
    private ?int $itemGlobalLimit;
    private ?int $itemUserLimit;
    private int $itemDraft;
    private string $itemCreated;
    private string $itemUpdated;
    private int $itemArchived;
    private int $itemArchivedReason;

    public function __construct(int $itemId, ?ShopCategoryEntity $category, ?string $itemName, string $itemDescription, string $itemShortDescription, string $itemSlug, ?int $itemImage, int $itemType, ?int $itemDefaultStock, ?int $itemCurrentStock, ?float $itemPrice, string $itemPriceType, ?int $itemByOrderLimit, ?int $itemGlobalLimit, ?int $itemUserLimit, int $itemDraft, string $itemCreated, string $itemUpdated, int $itemArchived, int $itemArchivedReason)
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
        $this->itemPriceType = $itemPriceType;
        $this->itemByOrderLimit = $itemByOrderLimit;
        $this->itemGlobalLimit = $itemGlobalLimit;
        $this->itemUserLimit = $itemUserLimit;
        $this->itemDraft = $itemDraft;
        $this->itemCreated = $itemCreated;
        $this->itemUpdated = $itemUpdated;
        $this->itemArchived = $itemArchived;
        $this->itemArchivedReason = $itemArchivedReason;
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
     * @return ShopItemType
     */
    public function getType(): ShopItemType
    {
        return ShopItemType::fromDb($this->itemType);
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
    public function getAdminFormattedStock(): ?string
    {
        if (is_null($this->getDefaultStock())) {
            return LangManager::translate('shop.entities.item.unlimited');
        } else {
            $currentStock = $this->itemCurrentStock;
            $defaultStock = $this->itemDefaultStock;
            $percentage = ($currentStock / $defaultStock) * 100;
            $stockAlert = ShopSettingsModel::getInstance()->getSettingValue('stockAlert');

            if ($percentage <= $stockAlert) {
                return "<b style='color: red '><i class='fa-solid fa-circle-exclamation'></i> {$currentStock} / {$defaultStock}</b>";
            } else {
                return "{$currentStock} / {$defaultStock}";
            }
        }
    }

    /**
     * @return ?string
     */
    public function getPublicFormattedStock(): ?string
    {
        $showPublicStock = ShopSettingsModel::getInstance()->getSettingValue('showPublicStock');
        if ($showPublicStock) {
            if (is_null($this->getDefaultStock())) {
                return LangManager::translate('shop.entities.item.in-stock');
            }

            $currentStock = $this->itemCurrentStock;
            $defaultStock = $this->itemDefaultStock;
            $percentage = ($currentStock / $defaultStock) * 100;
            $stockAlert = ShopSettingsModel::getInstance()->getSettingValue('stockAlert');

            if ($currentStock === 0) {
                return LangManager::translate('shop.entities.item.sold-out');
            }
            if ($percentage <= $stockAlert) {
                return "<b style='color: red '>{$currentStock}</b> " . LangManager::translate('shop.entities.item.out-soon');
            }

            return "<b style='color: #0ab312'>{$currentStock}</b>";
        }

        return 'N/A';
    }

    /**
     * @return ?float
     * @desc return the price for this item as float but not included the discount us also getPriceDiscountDefaultApplied() for public view
     */
    public function getPrice(): ?float
    {
        return $this->itemPrice;
    }

    public function getPriceType(): string
    {
        return $this->itemPriceType;
    }

    /**
     * @return string
     * @desc return the price for views
     */
    public function getPriceFormatted(): string
    {
        $formattedPrice = number_format($this->itemPrice, 2, '.', '');
        if ($this->getPriceType() == 'money') {
            $symbol = ShopSettingsModel::getInstance()->getSettingValue('symbol');
        } else {
            $symbol = ' ' . ShopPaymentsController::getInstance()->getPaymentByVarName($this->getPriceType())->faIcon() . ' ';
        }
        $symbolIsAfter = ShopSettingsModel::getInstance()->getSettingValue('after');
        if ($symbolIsAfter) {
            return $formattedPrice . $symbol;
        } else {
            return $symbol . $formattedPrice;
        }
    }

    /**
     * @return ?float
     * @desc return the price impacted by the discount as float if discount is not applied for this item he returns nothing
     */
    public function getPriceDiscountDefaultApplied(): ?float
    {
        $basePrice = $this->getPrice();
        $discount = 0;
        $allDiscounts = ShopDiscountModel::getInstance()->getShopDiscountsDefaultAppliedForAll();
        $discountCategories = ShopDiscountCategoriesModel::getInstance()->getShopDiscountCategoriesDefaultAppliedByCategoryId($this->getCategory()->getId());
        $discountItems = ShopDiscountItemsModel::getInstance()->getShopDiscountItemsDefaultAppliedByItemId($this->getId());

        if ($this->getPriceType() == 'money') {
            // all
            if (!empty($allDiscounts)) {
                foreach ($allDiscounts as $allDiscount) {
                    if ($allDiscount->getLinked() == 0) {
                        if ($allDiscount->getPrice()) {
                            $discount = $allDiscount->getPrice();
                        }
                        if ($allDiscount->getPercentage()) {
                            $discount = ($basePrice * $allDiscount->getPercentage()) / 100;
                        }
                    }
                    // prevent negative price
                    if ($basePrice - $discount <= 0) {
                        return null;
                    } else {
                        return number_format($basePrice - $discount, 2, '.', '');
                    }
                }
            }
            // cats
            if (!empty($discountCategories)) {
                foreach ($discountCategories as $discountCategory) {
                    if ($discountCategory->getDiscount()->getLinked() == 2) {
                        if ($discountCategory->getDiscount()->getPrice()) {
                            $discount = $discountCategory->getDiscount()->getPrice();
                        }
                        if ($discountCategory->getDiscount()->getPercentage()) {
                            $discount = ($basePrice * $discountCategory->getDiscount()->getPercentage()) / 100;
                        }
                    }
                    // prevent negative price
                    if ($basePrice - $discount <= 0) {
                        return null;
                    } else {
                        return number_format($basePrice - $discount, 2, '.', '');
                    }
                }
            }
            // items
            if (!empty($discountItems)) {
                foreach ($discountItems as $discountItem) {
                    if ($discountItem->getDiscount()->getLinked() == 1) {
                        if ($discountItem->getDiscount()->getPrice()) {
                            $discount = $discountItem->getDiscount()->getPrice();
                        }
                        if ($discountItem->getDiscount()->getPercentage()) {
                            $discount = ($basePrice * $discountItem->getDiscount()->getPercentage()) / 100;
                        }
                    }
                    // prevent negative price
                    if ($basePrice - $discount <= 0) {
                        return null;
                    } else {
                        return number_format($basePrice - $discount, 2, '.', '');
                    }
                }
            }
        }
        return null;
    }

    /**
     * @return string
     * @desc return the price for views
     */
    public function getPriceDiscountDefaultAppliedFormatted(): string
    {
        $formattedPrice = number_format($this->getPriceDiscountDefaultApplied(), 2, '.', '');
        $symbol = ShopSettingsModel::getInstance()->getSettingValue('symbol');
        $symbolIsAfter = ShopSettingsModel::getInstance()->getSettingValue('after');
        if ($symbolIsAfter) {
            return $formattedPrice . $symbol;
        } else {
            return $symbol . $formattedPrice;
        }
    }

    /**
     * @return ?\CMW\Entity\Shop\Discounts\ShopDiscountEntity
     * @desc return the ShopDiscountItemsEntity if a discount is applied for this item, otherwise return nothing
     */
    public function getDiscountEntityApplied(): \CMW\Entity\Shop\Discounts\ShopDiscountEntity
    {
        $basePrice = $this->getPrice();
        $bestDiscountValue = 0;
        $bestDiscountEntity = null;

        // Get all discounts applicable to all items
        $allDiscounts = ShopDiscountModel::getInstance()->getShopDiscountsDefaultAppliedForAll();
        foreach ($allDiscounts as $discount) {
            if ($discount->getLinked() == 0) {  // Check if the discount is applicable to all items
                $currentDiscountValue = $this->calculateDiscount($basePrice, $discount);
                if ($currentDiscountValue > $bestDiscountValue) {
                    $bestDiscountValue = $currentDiscountValue;
                    $bestDiscountEntity = $discount;  // Store the discount entity
                }
            }
        }

        // Get discounts applicable to categories
        $discountCategories = ShopDiscountCategoriesModel::getInstance()->getShopDiscountCategoriesDefaultAppliedByCategoryId($this->getCategory()->getId());
        foreach ($discountCategories as $discountCategory) {
            $discount = $discountCategory->getDiscount();
            if ($discount->getLinked() == 2) {  // Check if the discount is applicable to categories
                $currentDiscountValue = $this->calculateDiscount($basePrice, $discount);
                if ($currentDiscountValue > $bestDiscountValue) {
                    $bestDiscountValue = $currentDiscountValue;
                    $bestDiscountEntity = $discount;  // Store the discount entity
                }
            }
        }

        // Get discounts applicable to specific items
        $discountItems = ShopDiscountItemsModel::getInstance()->getShopDiscountItemsDefaultAppliedByItemId($this->getId());
        foreach ($discountItems as $discountItem) {
            $discount = $discountItem->getDiscount();
            if ($discount->getLinked() == 1) {  // Check if the discount is applicable to specific items
                $currentDiscountValue = $this->calculateDiscount($basePrice, $discount);
                if ($currentDiscountValue > $bestDiscountValue) {
                    $bestDiscountValue = $currentDiscountValue;
                    $bestDiscountEntity = $discount;  // Store the discount entity
                }
            }
        }

        return $bestDiscountEntity;
    }

    /**
     * Helper method to calculate the discount value.
     * @param float $basePrice
     * @param ShopDiscountEntity $discount
     * @return float The discount value.
     */
    private function calculateDiscount(float $basePrice, ShopDiscountEntity $discount): float
    {
        $discountValue = 0;
        if ($discount->getPrice()) {
            $discountValue = $discount->getPrice();
        } elseif ($discount->getPercentage()) {
            $discountValue = ($basePrice * $discount->getPercentage()) / 100;
        }
        return $discountValue;
    }

    /**
     * @return ?string
     * @desc return the discount applied on the item with the percentage or the amount
     */
    public function getDiscountImpactDefaultApplied(): ?string
    {
        $basePrice = $this->getPrice();
        $discount = 0;
        $discountFormatted = '';
        $allDiscounts = ShopDiscountModel::getInstance()->getShopDiscountsDefaultAppliedForAll();
        $discountCategories = ShopDiscountCategoriesModel::getInstance()->getShopDiscountCategoriesDefaultAppliedByCategoryId($this->category->getId());
        $discountItems = ShopDiscountItemsModel::getInstance()->getShopDiscountItemsDefaultAppliedByItemId($this->getId());

        if ($this->getPriceType() == 'money') {
            $symbol = ShopSettingsModel::getInstance()->getSettingValue('symbol');

            $symbol = ShopSettingsModel::getInstance()->getSettingValue('symbol');
            $symbolIsAfter = ShopSettingsModel::getInstance()->getSettingValue('after');

            // all
            if (!empty($allDiscounts)) {
                foreach ($allDiscounts as $allDiscount) {
                    if ($allDiscount->getDiscountQuantityImpacted() == 1) {
                        if ($allDiscount->getPrice()) {
                            $discount = $allDiscount->getPrice();
                            if ($symbolIsAfter) {
                                $discountFormatted = '- ' . $allDiscount->getPrice() . $symbol;
                            } else {
                                $discountFormatted = '- ' . $symbol . $allDiscount->getPrice();
                            }
                        }
                        if ($allDiscount->getPercentage()) {
                            $discount = ($basePrice * $allDiscount->getPercentage()) / 100;
                            $discountFormatted = '-' . $allDiscount->getPercentage() . ' %';
                        }
                    } else {
                        if ($allDiscount->getPrice()) {
                            $discount = $allDiscount->getPrice();
                            if ($symbolIsAfter) {
                                $discountFormatted = '- ' . $allDiscount->getPrice() . $symbol . LangManager::translate('shop.entities.item.first');
                            } else {
                                $discountFormatted = '- ' . $symbol . $allDiscount->getPrice() . LangManager::translate('shop.entities.item.first');
                            }
                        }
                        if ($allDiscount->getPercentage()) {
                            $discount = ($basePrice * $allDiscount->getPercentage()) / 100;
                            $discountFormatted = '-' . $allDiscount->getPercentage() . ' % ' . LangManager::translate('shop.entities.item.first');
                        }
                    }
                    // prevent negative price
                    if ($basePrice - $discount <= 0) {
                        return null;
                    } else {
                        return $discountFormatted;
                    }
                }
            }
            // cats
            if (!empty($discountCategories)) {
                foreach ($discountCategories as $discountCategory) {
                    if ($discountCategory->getDiscount()->getDiscountQuantityImpacted() == 1) {
                        if ($discountCategory->getDiscount()->getLinked() == 2) {
                            if ($discountCategory->getDiscount()->getPrice()) {
                                $discount = $discountCategory->getDiscount()->getPrice();
                                $discountFormatted = '-' . $discountCategory->getDiscount()->getPriceFormatted();
                            }
                            if ($discountCategory->getDiscount()->getPercentage()) {
                                $discount = ($basePrice * $discountCategory->getDiscount()->getPercentage()) / 100;
                                $discountFormatted = '-' . $discountCategory->getDiscount()->getPercentage() . ' %';
                            }
                        }
                    } else {
                        if ($discountCategory->getDiscount()->getPrice()) {
                            $discount = $discountCategory->getDiscount()->getPrice();
                            $discountFormatted = '-' . $discountCategory->getDiscount()->getPriceFormatted() . LangManager::translate('shop.entities.item.first');
                        }
                        if ($discountCategory->getDiscount()->getPercentage()) {
                            $discount = ($basePrice * $discountCategory->getDiscount()->getPercentage()) / 100;
                            $discountFormatted = '-' . $discountCategory->getDiscount()->getPercentage() . ' % ' . LangManager::translate('shop.entities.item.first');
                        }
                    }
                    // prevent negative price
                    if ($basePrice - $discount <= 0) {
                        return null;
                    } else {
                        return $discountFormatted;
                    }
                }
            }
            // items
            if (!empty($discountItems)) {
                foreach ($discountItems as $discountItem) {
                    if ($discountItem->getDiscount()->getLinked() == 1) {
                        if ($discountItem->getDiscount()->getDiscountQuantityImpacted() == 1) {
                            if ($discountItem->getDiscount()->getPrice()) {
                                $discount = $discountItem->getDiscount()->getPrice();
                                $discountFormatted = '-' . $discountItem->getDiscount()->getPriceFormatted();
                            }
                            if ($discountItem->getDiscount()->getPercentage()) {
                                $discount = ($basePrice * $discountItem->getDiscount()->getPercentage()) / 100;
                                $discountFormatted = '-' . $discountItem->getDiscount()->getPercentage() . ' %';
                            }
                        } else {
                            if ($discountItem->getDiscount()->getPrice()) {
                                $discount = $discountItem->getDiscount()->getPrice();
                                $discountFormatted = '-' . $discountItem->getDiscount()->getPriceFormatted() . LangManager::translate('shop.entities.item.first');
                            }
                            if ($discountItem->getDiscount()->getPercentage()) {
                                $discount = ($basePrice * $discountItem->getDiscount()->getPercentage()) / 100;
                                $discountFormatted = '-' . $discountItem->getDiscount()->getPercentage() . ' % ' . LangManager::translate('shop.entities.item.first');
                            }
                        }
                    }
                    // prevent negative price
                    if ($basePrice - $discount <= 0) {
                        return null;
                    } else {
                        return $discountFormatted;
                    }
                }
            }
        }
        return null;
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
     * @return int
     */
    public function isDraft(): int
    {
        return $this->itemDraft;
    }

    /**
     * @return string
     */
    public function getCreated(): string
    {
        return Date::formatDate($this->itemCreated);
    }

    /**
     * @return string
     */
    public function getUpdate(): string
    {
        return Date::formatDate($this->itemUpdated);
    }

    /**
     * @return int
     */
    public function getArchived(): int
    {
        return $this->itemArchived;
    }

    /**
     * @return string
     */
    public function getArchivedReason(): string
    {
        if ($this->itemArchivedReason == 0) {
            return LangManager::translate('shop.entities.item.unarchived');
        }
        if ($this->itemArchivedReason == 1) {
            return LangManager::translate('shop.entities.item.in-cart');
        }
        if ($this->itemArchivedReason == 2) {
            return LangManager::translate('shop.entities.item.commanded');
        }
    }

    /**
     * @return string
     */
    public function getItemLink(): string
    {
        $catSlug = $this->getCategory()?->getSlug();
        if ($catSlug) {
            return Website::getProtocol() . '://' . $_SERVER['SERVER_NAME'] . EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . "shop/cat/$catSlug/item/$this->itemSlug";
        } else {
            return "#";
        }
    }

    /**
     * @return string
     */
    public function getAddToCartLink(): string
    {
        return Website::getProtocol() . '://' . $_SERVER['SERVER_NAME'] . EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . "shop/add_to_cart/$this->itemId";
    }

    /*
     * ++ Cool features
     */

    /**
     * @return string
     * @desc perfect if you retrieve the quantities in the cart from the item page
     */
    public function getQuantityInCart(): string
    {
        return ShopCartItemModel::getInstance()->getQuantity($this->itemId, UsersSessionsController::getInstance()->getCurrentUser()?->getId(), session_id());
    }

    /**
     * @return string
     * @desc perfect if you want to manage quantities in the cart from the item page
     */
    public function getIncreaseQuantityCartLink(): string
    {
        return Website::getProtocol() . '://' . $_SERVER['SERVER_NAME'] . EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . "shop/cart/increase_quantity/$this->itemId";
    }

    /**
     * @return string
     * @desc perfect if you want to manage quantities in the cart from the item page
     */
    public function getDecreaseQuantityCartLink(): string
    {
        return Website::getProtocol() . '://' . $_SERVER['SERVER_NAME'] . EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . "shop/cart/decrease_quantity/$this->itemId";
    }

    /**
     * @return string
     * @desc perfect if you want to manage quantities in the cart from the item page
     */
    public function getRemoveCartLink(): string
    {
        return Website::getProtocol() . '://' . $_SERVER['SERVER_NAME'] . EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . "shop/cart/remove/$this->itemId";
    }
}
