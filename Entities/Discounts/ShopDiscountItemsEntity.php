<?php

namespace CMW\Entity\Shop\Discounts;

use CMW\Entity\Shop\Items\ShopItemEntity;
use CMW\Manager\Package\AbstractEntity;

class ShopDiscountItemsEntity extends AbstractEntity
{
    private int $id;
    private ShopDiscountEntity $discount;
    private ShopItemEntity $item;

    /**
     * @param int $id
     * @param \CMW\Entity\Shop\Discounts\ShopDiscountEntity $discount
     * @param \CMW\Entity\Shop\Items\ShopItemEntity $item
     */
    public function __construct(int $id, ShopDiscountEntity $discount, ShopItemEntity $item)
    {
        $this->id = $id;
        $this->discount = $discount;
        $this->item = $item;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDiscount(): ShopDiscountEntity
    {
        return $this->discount;
    }

    public function getItem(): ShopItemEntity
    {
        return $this->item;
    }
}
