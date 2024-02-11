<?php

namespace CMW\Entity\Shop\Carts;


use CMW\Entity\Shop\Discounts\ShopDiscountEntity;

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

}