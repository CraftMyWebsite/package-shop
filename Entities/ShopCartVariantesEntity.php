<?php

namespace CMW\Entity\Shop;

use CMW\Controller\Core\CoreController;
use CMW\Entity\Shop\Items\ShopItemVariantValueEntity;
use CMW\Entity\Users\UserEntity;
use CMW\Manager\Env\EnvManager;
use CMW\Model\Shop\ShopCartsModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Website;

class ShopCartVariantesEntity
{
    private int $cartVariantId;
    private ShopCartEntity $cartId;
    private ShopItemVariantValueEntity $variantValueId;
    private string $cartVariantCreated;
    private string $cartVariantUpdated;

    /**
     * @param int $cartVariantId
     * @param \CMW\Entity\Shop\ShopCartEntity $cartId
     * @param \CMW\Entity\Shop\Items\ShopItemVariantValueEntity $variantValueId
     * @param string $cartVariantCreated
     * @param string $cartVariantUpdated
     */
    public function __construct(int $cartVariantId, ShopCartEntity $cartId, ShopItemVariantValueEntity $variantValueId, string $cartVariantCreated, string $cartVariantUpdated)
    {
        $this->cartVariantId = $cartVariantId;
        $this->cartId = $cartId;
        $this->variantValueId = $variantValueId;
        $this->cartVariantCreated = $cartVariantCreated;
        $this->cartVariantUpdated = $cartVariantUpdated;
    }

    public function getId(): int
    {
        return $this->cartVariantId;
    }

    public function getCart(): ShopCartEntity
    {
        return $this->cartId;
    }

    public function getVariantValue(): ShopItemVariantValueEntity
    {
        return $this->variantValueId;
    }

    public function getCreated(): string
    {
        return CoreController::formatDate($this->cartVariantCreated);
    }

    public function getUpdated(): string
    {
        return CoreController::formatDate($this->cartVariantUpdated);
    }

}