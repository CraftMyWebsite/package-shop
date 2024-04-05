<?php

namespace CMW\Entity\Shop\HistoryOrders;


use CMW\Model\Shop\Setting\ShopSettingsModel;

class ShopHistoryOrdersPaymentEntity {
    private int $historyOrderPaymentId;
    private ShopHistoryOrdersEntity $historyOrder;
    private ?string $historyOrderPaymentName;
    private ?float $historyOrderPaymentFee;

    /**
     * @param int $historyOrderPaymentId
     * @param \CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity $historyOrder
     * @param string|null $historyOrderPaymentName
     * @param float|null $historyOrderPaymentFee
     */
    public function __construct(int $historyOrderPaymentId, ShopHistoryOrdersEntity $historyOrder, ?string $historyOrderPaymentName, ?float $historyOrderPaymentFee)
    {
        $this->historyOrderPaymentId = $historyOrderPaymentId;
        $this->historyOrder = $historyOrder;
        $this->historyOrderPaymentName = $historyOrderPaymentName;
        $this->historyOrderPaymentFee = $historyOrderPaymentFee;
    }

    public function getId(): int
    {
        return $this->historyOrderPaymentId;
    }

    public function getHistoryOrder(): ShopHistoryOrdersEntity
    {
        return $this->historyOrder;
    }

    public function getName(): ?string
    {
        return $this->historyOrderPaymentName;
    }

    public function getFee(): ?float
    {
        return $this->historyOrderPaymentFee;
    }

    /**
     * @return string
     * @desc return the price for views
     */
    public function getFeeFormatted(): string
    {
        $formattedPrice = number_format($this->getFee(), 2, '.', '');
        $symbol = ShopSettingsModel::getInstance()->getSettingValue("symbol");
        $symbolIsAfter = ShopSettingsModel::getInstance()->getSettingValue("after");
        if ($symbolIsAfter) {
            return $formattedPrice .  $symbol;
        } else {
            return $symbol .  $formattedPrice;
        }
    }
}