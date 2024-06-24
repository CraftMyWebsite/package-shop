<?php

namespace CMW\Entity\Shop\Deliveries;

use CMW\Controller\Core\CoreController;
use CMW\Model\Shop\Setting\ShopSettingsModel;

class ShopShippingEntity
{
    private int $shippingId;
    private string $shippingName;
    private ?float $shippingPrice;
    private string $shippingCreated;
    private string $shippingUpdated;

    /**
     * @param int $shippingId
     * @param string $shippingName
     * @param float|null $shippingPrice
     * @param string $shippingCreated
     * @param string $shippingUpdated
     */
    public function __construct(int $shippingId, string $shippingName, ?float $shippingPrice, string $shippingCreated, string $shippingUpdated)
    {
        $this->shippingId = $shippingId;
        $this->shippingName = $shippingName;
        $this->shippingPrice = $shippingPrice;
        $this->shippingCreated = $shippingCreated;
        $this->shippingUpdated = $shippingUpdated;
    }

    public function getId(): int
    {
        return $this->shippingId;
    }

    public function getName(): string
    {
        return $this->shippingName;
    }

    public function getPrice(): ?float
    {
        if (is_null($this->shippingPrice)) {
            return 0;
        } else {
            return $this->shippingPrice;
        }
    }

    /**
     * @return string
     * @desc return the price for views
     */
    public function getPriceFormatted(): string
    {
        $formattedPrice = number_format($this->getPrice(), 2, '.', '');
        $symbol = ShopSettingsModel::getInstance()->getSettingValue("symbol");
        $symbolIsAfter = ShopSettingsModel::getInstance()->getSettingValue("after");
        if ($symbolIsAfter) {
            return $formattedPrice . $symbol;
        } else {
            return $symbol . $formattedPrice;
        }
    }

    public function getCreated(): string
    {
        return CoreController::formatDate($this->shippingCreated);
    }

    public function getUpdated(): string
    {
        return CoreController::formatDate($this->shippingUpdated);
    }



}