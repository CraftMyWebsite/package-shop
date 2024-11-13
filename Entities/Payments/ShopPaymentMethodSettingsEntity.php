<?php

namespace CMW\Entity\Shop\Payments;

use CMW\Manager\Package\AbstractEntity;
use CMW\Manager\Security\EncryptManager;

class ShopPaymentMethodSettingsEntity extends AbstractEntity
{
    private string $key;
    private string $value;

    /**
     * @param string $key
     * @param string $value
     */
    public function __construct(string $key, string $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return EncryptManager::decrypt($this->value);
    }
}
