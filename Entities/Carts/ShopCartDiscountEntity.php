<?php

namespace CMW\Entity\Shop\Carts;


use CMW\Entity\Shop\Discounts\ShopDiscountEntity;

class ShopCartDiscountEntity
{
    private $id;
    private ShopCartItemEntity $cartId;
    private ShopDiscountEntity $discountId;

    /**
     * @param $id
     * @param \CMW\Entity\Shop\Carts\ShopCartItemEntity $cartId
     * @param \CMW\Entity\Shop\Discounts\ShopDiscountEntity $discountId
     */
    public function __construct($id, ShopCartItemEntity $cartId, ShopDiscountEntity $discountId)
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

    public function getCart(): ShopCartItemEntity
    {
        return $this->cartId;
    }

    public function getDiscount(): ShopDiscountEntity
    {
        return $this->discountId;
    }

}