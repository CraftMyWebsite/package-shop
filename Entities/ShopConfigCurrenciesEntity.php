<?php

namespace CMW\Entity\Shop;

class ShopConfigCurrenciesEntity
{
    private string $code;
    private string $name;
    private string $dateAdded;

    /**
     * @param string $code
     * @param string $name
     * @param string $dateAdded
     */
    public function __construct(string $code, string $name, string $dateAdded)
    {
        $this->code = $code;
        $this->name = $name;
        $this->dateAdded = $dateAdded;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDateAdded(): string
    {
        return $this->dateAdded;
    }
    
}