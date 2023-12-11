<?php

namespace CMW\Entity\Shop;

class ShopSubCategoryEntity
{
    private ShopCategoryEntity $subCategory;
    private int $depth;

    /**
     * @param ShopCategoryEntity $subCategory
     * @param int $depth
     */
    public function __construct(ShopCategoryEntity $subCategory, int $depth)
    {
        $this->subCategory = $subCategory;
        $this->depth = $depth;
    }

    public function getSubCategory(): ShopCategoryEntity
    {
        return $this->subCategory;
    }

    public function getDepth(): int
    {
        return $this->depth;
    }
}