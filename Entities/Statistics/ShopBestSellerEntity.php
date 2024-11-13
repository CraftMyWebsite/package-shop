<?php

namespace CMW\Entity\Shop\Statistics;

use CMW\Entity\Shop\Items\ShopItemEntity;
use CMW\Manager\Package\AbstractEntity;

class ShopBestSellerEntity extends AbstractEntity
{

    private ShopItemEntity $item;
    private int $sales;

    /**
     * @param \CMW\Entity\Shop\Items\ShopItemEntity $item
     * @param int $sales
     */
    public function __construct(ShopItemEntity $item, int $sales)
    {
        $this->item = $item;
        $this->sales = $sales;
    }

    public function getItem(): ShopItemEntity
    {
        return $this->item;
    }

    public function getSales(): int
    {
        return $this->sales;
    }


}
