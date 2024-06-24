<?php

namespace CMW\Entity\Shop\Carts;


use CMW\Entity\Shop\Discounts\ShopDiscountEntity;
use CMW\Manager\Env\EnvManager;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Utils\Website;

class ShopCartDiscountEntity
{
    private $id;
    private ShopCartEntity $cartId;
    private ShopDiscountEntity $discountId;

    /**
     * @param $id
     * @param \CMW\Entity\Shop\Carts\ShopCartEntity $cartId
     * @param \CMW\Entity\Shop\Discounts\ShopDiscountEntity $discountId
     */
    public function __construct($id, ShopCartEntity $cartId, ShopDiscountEntity $discountId)
    {
        $this->id = $id;
        $this->cartId = $cartId;
        $this->discountId = $discountId;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function getCart(): ShopCartEntity
    {
        return $this->cartId;
    }

    public function getDiscount(): ShopDiscountEntity
    {
        return $this->discountId;
    }

    /**
     * @return ?string
     */
    public function getDiscountFormatted(): ?string
    {
        if ($this->discountId->getPrice()) {
            $symbol = ShopSettingsModel::getInstance()->getSettingValue("symbol");
            $symbolIsAfter = ShopSettingsModel::getInstance()->getSettingValue("after");
            if ($symbolIsAfter) {
                return "- " . $this->discountId->getPrice() . $symbol;
            } else {
                return "- " . $symbol . $this->discountId->getPrice();
            }
        }
        if ($this->discountId->getPercentage()) {
            return "- " . $this->discountId->getPercentage() . "%";
        }
        return null;
    }

    /**
     * @return string
     */
    public function getRemoveLink(): string
    {
        $discountId = $this->getDiscount()->getId();
        return Website::getProtocol() . "://" . $_SERVER["SERVER_NAME"] . EnvManager::getInstance()->getValue("PATH_SUBFOLDER") . "shop/cart/discount/remove/$discountId";
    }


}