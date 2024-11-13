<?php

namespace CMW\Entity\Shop\Reviews;

use CMW\Manager\Package\AbstractEntity;

class ShopRatingPercentageEntity extends AbstractEntity
{
    private int $rating;
    private float $percentage;

    public function __construct($rating, $percentage)
    {
        $this->rating = $rating;
        $this->percentage = $percentage;
    }

    public function getRating(): int
    {
        return $this->rating;
    }

    public function getPercentage(): float
    {
        return round($this->percentage);
    }
}
