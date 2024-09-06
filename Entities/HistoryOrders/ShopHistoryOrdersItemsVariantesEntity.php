<?php

namespace CMW\Entity\Shop\HistoryOrders;

use CMW\Controller\Core\CoreController;
use CMW\Entity\Shop\Items\ShopItemVariantValueEntity;

class ShopHistoryOrdersItemsVariantesEntity
{
    private int $historyOrderItemVariantesId;
    private ShopHistoryOrdersItemsEntity $historyOrderItems;
    private string $historyOrderItemVariantesName;
    private string $historyOrderItemVariantesValue;

    /**
     * @param int $historyOrderItemVariantesId
     * @param \CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersItemsEntity $historyOrderItems
     * @param string $historyOrderItemVariantesName
     * @param string $historyOrderItemVariantesValue
     */
    public function __construct(int $historyOrderItemVariantesId, ShopHistoryOrdersItemsEntity $historyOrderItems, string $historyOrderItemVariantesName, string $historyOrderItemVariantesValue)
    {
        $this->historyOrderItemVariantesId = $historyOrderItemVariantesId;
        $this->historyOrderItems = $historyOrderItems;
        $this->historyOrderItemVariantesName = $historyOrderItemVariantesName;
        $this->historyOrderItemVariantesValue = $historyOrderItemVariantesValue;
    }

    public function getId(): int
    {
        return $this->historyOrderItemVariantesId;
    }

    public function getHistoryOrderItems(): ShopHistoryOrdersItemsEntity
    {
        return $this->historyOrderItems;
    }

    public function getName(): string
    {
        return $this->historyOrderItemVariantesName;
    }

    public function getValue(): string
    {
        return $this->historyOrderItemVariantesValue;
    }
}
