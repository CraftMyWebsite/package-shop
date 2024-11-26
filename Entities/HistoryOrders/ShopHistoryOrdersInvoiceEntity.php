<?php

namespace CMW\Entity\Shop\HistoryOrders;

use CMW\Manager\Package\AbstractEntity;
use CMW\Utils\Date;

class ShopHistoryOrdersInvoiceEntity extends AbstractEntity
{
    private int $historyOrderInvoiceId;
    private ShopHistoryOrdersEntity $historyOrder;
    private string $historyOrderInvoiceLink;
    private string $historyOrderInvoiceCreated;

    /**
     * @param int $historyOrderInvoiceId
     * @param \CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity $historyOrder
     * @param string $historyOrderInvoiceLink
     * @param string $historyOrderInvoiceCreated
     */
    public function __construct(int $historyOrderInvoiceId, ShopHistoryOrdersEntity $historyOrder, string $historyOrderInvoiceLink, string $historyOrderInvoiceCreated)
    {
        $this->historyOrderInvoiceId = $historyOrderInvoiceId;
        $this->historyOrder = $historyOrder;
        $this->historyOrderInvoiceLink = $historyOrderInvoiceLink;
        $this->historyOrderInvoiceCreated = $historyOrderInvoiceCreated;
    }

    public function getId(): int
    {
        return $this->historyOrderInvoiceId;
    }

    public function getHistoryOrder(): ShopHistoryOrdersEntity
    {
        return $this->historyOrder;
    }

    public function getInvoiceLink(): string
    {
        return $this->historyOrderInvoiceLink;
    }

    public function getCreated(): string
    {
        return Date::formatDate($this->historyOrderInvoiceCreated);
    }
}
