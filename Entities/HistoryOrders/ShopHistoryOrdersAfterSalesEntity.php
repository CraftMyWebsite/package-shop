<?php

namespace CMW\Entity\Shop\HistoryOrders;

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractEntity;
use CMW\Utils\Date;
use CMW\Entity\Users\UserEntity;
use CMW\Utils\Website;

class ShopHistoryOrdersAfterSalesEntity extends AbstractEntity
{
    private int $id;
    private UserEntity $author;
    private int $reason;
    private int $status;
    private ShopHistoryOrdersEntity $order;
    private string $created;
    private string $updated;

    /**
     * @param int $id
     * @param \CMW\Entity\Users\UserEntity $author
     * @param int $reason
     * @param int $status
     * @param \CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity $order
     * @param string $created
     * @param string $updated
     */
    public function __construct(int $id, UserEntity $author, int $reason, int $status, ShopHistoryOrdersEntity $order, string $created, string $updated)
    {
        $this->id = $id;
        $this->author = $author;
        $this->reason = $reason;
        $this->status = $status;
        $this->order = $order;
        $this->created = $created;
        $this->updated = $updated;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAuthor(): UserEntity
    {
        return $this->author;
    }

    public function getReason(): int
    {
        return $this->reason;
    }

    public function getFormattedReason(): string
    {
        if ($this->reason === 0) {
            return LangManager::translate('shop.entities.afterSales.edit');
        }
        if ($this->reason === 1) {
            return LangManager::translate('shop.entities.afterSales.error');
        }
        if ($this->reason === 2) {
            return LangManager::translate('shop.entities.afterSales.defective');
        }
        if ($this->reason === 3) {
            return LangManager::translate('shop.entities.afterSales.damaged');
        }
        if ($this->reason === 4) {
            return LangManager::translate('shop.entities.afterSales.missing');
        }
        if ($this->reason === 5) {
            return LangManager::translate('shop.entities.afterSales.delay');
        }
        if ($this->reason === 6) {
            return LangManager::translate('shop.entities.afterSales.receipt');
        }
        if ($this->reason === 7) {
            return LangManager::translate('shop.entities.afterSales.size');
        }
        if ($this->reason === 8) {
            return LangManager::translate('shop.entities.afterSales.other');
        }
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getFormattedStatus(): string
    {
        if ($this->status === 0) {
            return "<i class='fa-solid fa-spinner fa-spin' style='color: #1159d4;'></i>" . LangManager::translate('shop.entities.afterSales.waiting');
        }
        if ($this->status === 1) {
            return "<i class='fa-solid fa-spinner fa-spin-pulse' style='color: #1bbba9;'></i>" . LangManager::translate('shop.entities.afterSales.response');
        }

        return "<i class='fa-regular fa-circle-check' style='color: #15d518;'></i>" . LangManager::translate('shop.entities.afterSales.close');
    }

    public function getOrder(): ShopHistoryOrdersEntity
    {
        return $this->order;
    }

    public function getCreated(): string
    {
        return Date::formatDate($this->created);
    }

    public function getUpdated(): string
    {
        return Date::formatDate($this->updated);
    }

    /**
     * @return string
     */
    public function getCloseUrl(): string
    {
        return Website::getProtocol() . '://' . $_SERVER['SERVER_NAME'] . EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . "shop/history/$this->support_slug";
    }
}
