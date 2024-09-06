<?php

namespace CMW\Entity\Shop\HistoryOrders;

use CMW\Model\Shop\Setting\ShopSettingsModel;

class ShopHistoryOrdersShippingEntity
{
    private int $historyOrderShippingId;
    private ShopHistoryOrdersEntity $historyOrder;
    private ?string $historyOrderShippingName;
    private ?float $historyOrderShippingPrice;

    /**
     * @param int $historyOrderShippingId
     * @param \CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity $historyOrder
     * @param string|null $historyOrderShippingName
     * @param float|null $historyOrderShippingPrice
     */
    public function __construct(int $historyOrderShippingId, ShopHistoryOrdersEntity $historyOrder, ?string $historyOrderShippingName, ?float $historyOrderShippingPrice)
    {
        $this->historyOrderShippingId = $historyOrderShippingId;
        $this->historyOrder = $historyOrder;
        $this->historyOrderShippingName = $historyOrderShippingName;
        $this->historyOrderShippingPrice = $historyOrderShippingPrice;
    }

    public function getId(): int
    {
        return $this->historyOrderShippingId;
    }

    public function getHistoryOrder(): ShopHistoryOrdersEntity
    {
        return $this->historyOrder;
    }

    public function getName(): ?string
    {
        return $this->historyOrderShippingName;
    }

    public function getPrice(): ?float
    {
        return $this->historyOrderShippingPrice;
    }

    /**
     * @return string
     * @desc return the price for views
     */
    public function getPriceFormatted(): string
    {
        $formattedPrice = number_format($this->getPrice(), 2, '.', '');
        $symbol = ShopSettingsModel::getInstance()->getSettingValue('symbol');
        $symbolIsAfter = ShopSettingsModel::getInstance()->getSettingValue('after');
        if ($symbolIsAfter) {
            return $formattedPrice . $symbol;
        } else {
            return $symbol . $formattedPrice;
        }
    }
}
