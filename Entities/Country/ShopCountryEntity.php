<?php

namespace CMW\Entity\Shop\Country;

use CMW\Manager\Package\AbstractEntity;

class ShopCountryEntity extends AbstractEntity
{
    private string $name;
    private string $code;

    /**
     * @param string $name
     * @param string $code
     */
    public function __construct(string $name, string $code)
    {
        $this->name = $name;
        $this->code = $code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCode(): string
    {
        return $this->code;
    }
}
