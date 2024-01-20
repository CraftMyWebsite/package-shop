<?php

namespace CMW\Entity\Shop;

use CMW\Controller\Core\CoreController;
use CMW\Entity\Shop\Items\ShopItemVariantValueEntity;
use CMW\Entity\Users\UserEntity;
use CMW\Manager\Env\EnvManager;
use CMW\Model\Shop\ShopCartsModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Website;

class ShopOrdersItemsVariantesEntity
{
    private int $orderVariantId;
    private ShopOrdersItemsEntity $ordersItemsId;
    private ShopItemVariantValueEntity $variantValueId;
    private string $orderVariantCreated;
    private string $orderVariantUpdated;

    /**
     * @param int $orderVariantId
     * @param \CMW\Entity\Shop\ShopOrdersItemsEntity $ordersItemsId
     * @param \CMW\Entity\Shop\Items\ShopItemVariantValueEntity $variantValueId
     * @param string $orderVariantCreated
     * @param string $orderVariantUpdated
     */
    public function __construct(int $orderVariantId, ShopOrdersItemsEntity $ordersItemsId, ShopItemVariantValueEntity $variantValueId, string $orderVariantCreated, string $orderVariantUpdated)
    {
        $this->orderVariantId = $orderVariantId;
        $this->ordersItemsId = $ordersItemsId;
        $this->variantValueId = $variantValueId;
        $this->orderVariantCreated = $orderVariantCreated;
        $this->orderVariantUpdated = $orderVariantUpdated;
    }

    public function getId(): int
    {
        return $this->orderVariantId;
    }

    public function getOrderItem(): ShopOrdersItemsEntity
    {
        return $this->ordersItemsId;
    }

    public function getVariantValue(): ShopItemVariantValueEntity
    {
        return $this->variantValueId;
    }

    public function getCreated(): string
    {
        return CoreController::formatDate($this->orderVariantCreated);
    }

    public function getUpdated(): string
    {
        return CoreController::formatDate($this->orderVariantUpdated);
    }



}