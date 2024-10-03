<?php

namespace CMW\Entity\Shop\HistoryOrders;

use CMW\Utils\Date;
use CMW\Entity\Users\UserEntity;

class ShopHistoryOrdersAfterSalesMessagesEntity
{
    private int $id;
    private ShopHistoryOrdersAfterSalesEntity $afterSales;
    private string $message;
    private UserEntity $author;
    private string $created;
    private string $updated;

    /**
     * @param int $id
     * @param \CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersAfterSalesEntity $afterSales
     * @param string $message
     * @param \CMW\Entity\Users\UserEntity $author
     * @param string $created
     * @param string $updated
     */
    public function __construct(int $id, ShopHistoryOrdersAfterSalesEntity $afterSales, string $message, UserEntity $author, string $created, string $updated)
    {
        $this->id = $id;
        $this->afterSales = $afterSales;
        $this->message = $message;
        $this->author = $author;
        $this->created = $created;
        $this->updated = $updated;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAfterSales(): ShopHistoryOrdersAfterSalesEntity
    {
        return $this->afterSales;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getAuthor(): UserEntity
    {
        return $this->author;
    }

    public function getCreated(): string
    {
        return Date::formatDate($this->created);
    }

    public function getUpdated(): string
    {
        return Date::formatDate($this->updated);
    }
}
