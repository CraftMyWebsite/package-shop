<?php

namespace CMW\Entity\Shop\HistoryOrders;


class ShopHistoryOrdersDiscountEntity {
    private int $historyOrderDiscountId;
    private ShopHistoryOrdersEntity $historyOrder;
    private ?string $historyOrderDiscountName;
    private ?float $historyOrderDiscountPrice;
    private ?int $historyOrderDiscountPercent;

    /**
     * @param int $historyOrderDiscountId
     * @param \CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity $historyOrder
     * @param string|null $historyOrderDiscountName
     * @param float|null $historyOrderDiscountPrice
     * @param int|null $historyOrderDiscountPercent
     */
    public function __construct(int $historyOrderDiscountId, ShopHistoryOrdersEntity $historyOrder, ?string $historyOrderDiscountName, ?float $historyOrderDiscountPrice, ?int $historyOrderDiscountPercent)
    {
        $this->historyOrderDiscountId = $historyOrderDiscountId;
        $this->historyOrder = $historyOrder;
        $this->historyOrderDiscountName = $historyOrderDiscountName;
        $this->historyOrderDiscountPrice = $historyOrderDiscountPrice;
        $this->historyOrderDiscountPercent = $historyOrderDiscountPercent;
    }

    public function getId(): int
    {
        return $this->historyOrderDiscountId;
    }

    public function getHistoryOrder(): ShopHistoryOrdersEntity
    {
        return $this->historyOrder;
    }

    public function getName(): ?string
    {
        return $this->historyOrderDiscountName;
    }

    public function getPrice(): ?float
    {
        return $this->historyOrderDiscountPrice;
    }

    public function getPercent(): ?int
    {
        return $this->historyOrderDiscountPercent;
    }



}