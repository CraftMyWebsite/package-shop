<?php

namespace CMW\Entity\Shop\Discounts;

use CMW\Entity\Shop\Categories\ShopCategoryEntity;

class ShopDiscountCategoriesEntity
{
    private int $id;
    private ShopDiscountEntity $discount;
    private ShopCategoryEntity $category;

    /**
     * @param int $id
     * @param \CMW\Entity\Shop\Discounts\ShopDiscountEntity $discount
     * @param \CMW\Entity\Shop\Categories\ShopCategoryEntity $category
     */
    public function __construct(int $id, ShopDiscountEntity $discount, ShopCategoryEntity $category)
    {
        $this->id = $id;
        $this->discount = $discount;
        $this->category = $category;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDiscount(): ShopDiscountEntity
    {
        return $this->discount;
    }

    public function getCategory(): ShopCategoryEntity
    {
        return $this->category;
    }


}