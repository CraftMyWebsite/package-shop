<?php

namespace CMW\Entity\Shop\HistoryOrders;

use CMW\Controller\Shop\Admin\Payment\ShopPaymentsController;
use CMW\Entity\Shop\Const\Payment\PaymentMethodConst;
use CMW\Entity\Shop\Items\ShopItemEntity;
use CMW\Manager\Package\AbstractEntity;
use CMW\Model\Shop\Setting\ShopSettingsModel;

class ShopHistoryOrdersItemsEntity extends AbstractEntity
{
    private int $historyOrderItemId;
    private ShopItemEntity $item;
    private ShopHistoryOrdersEntity $historyOrder;
    private ?string $historyOrderItemName;
    private ?string $historyOrderItemImg;
    private ?int $historyOrderItemQuantity;
    private ?float $historyOrderItemPrice;
    private ?string $historyOrderItemDiscountName;
    private ?float $historyOrderItemTotalPriceBeforeDiscount;
    private ?float $historyOrderItemTotalPriceAfterDiscount;

    /**
     * @param int $historyOrderItemId
     * @param ShopItemEntity $item
     * @param \CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity $historyOrder
     * @param string|null $historyOrderItemName
     * @param string|null $historyOrderItemImg
     * @param int|null $historyOrderItemQuantity
     * @param float|null $historyOrderItemPrice
     * @param string|null $historyOrderItemDiscountName
     * @param float|null $historyOrderItemTotalPriceBeforeDiscount
     * @param float|null $historyOrderItemTotalPriceAfterDiscount
     */
    public function __construct(int $historyOrderItemId, ShopItemEntity $item, ShopHistoryOrdersEntity $historyOrder, ?string $historyOrderItemName, ?string $historyOrderItemImg, ?int $historyOrderItemQuantity, ?float $historyOrderItemPrice, ?string $historyOrderItemDiscountName, ?float $historyOrderItemTotalPriceBeforeDiscount, ?float $historyOrderItemTotalPriceAfterDiscount)
    {
        $this->historyOrderItemId = $historyOrderItemId;
        $this->item = $item;
        $this->historyOrder = $historyOrder;
        $this->historyOrderItemName = $historyOrderItemName;
        $this->historyOrderItemImg = $historyOrderItemImg;
        $this->historyOrderItemQuantity = $historyOrderItemQuantity;
        $this->historyOrderItemPrice = $historyOrderItemPrice;
        $this->historyOrderItemDiscountName = $historyOrderItemDiscountName;
        $this->historyOrderItemTotalPriceBeforeDiscount = $historyOrderItemTotalPriceBeforeDiscount;
        $this->historyOrderItemTotalPriceAfterDiscount = $historyOrderItemTotalPriceAfterDiscount;
    }

    public function getId(): int
    {
        return $this->historyOrderItemId;
    }

    public function getItem(): ShopItemEntity
    {
        return $this->item;
    }

    public function getHistoryOrder(): ShopHistoryOrdersEntity
    {
        return $this->historyOrder;
    }

    public function getName(): ?string
    {
        return $this->historyOrderItemName;
    }

    public function getFirstImg(): ?string
    {
        return $this->historyOrderItemImg;
    }

    public function getQuantity(): ?int
    {
        return $this->historyOrderItemQuantity;
    }

    public function getPrice(): ?float
    {
        return $this->historyOrderItemPrice;
    }

    /**
     * @return string
     * @desc return the price for views
     */
    public function getPriceFormatted(): string
    {
        $formattedPrice = number_format($this->getPrice(), 2, '.', '');
        if ($this->getHistoryOrder()->getPaymentMethod()->getVarName() == 'money') {
            $symbol = ShopSettingsModel::getInstance()->getSettingValue('symbol');
        } else {
            $symbol = ' ' . ShopPaymentsController::getInstance()->getPaymentByVarName($this->getHistoryOrder()->getPaymentMethod()->getVarName())->faIcon() . ' ';
        }
        $symbolIsAfter = ShopSettingsModel::getInstance()->getSettingValue('after');
        if ($symbolIsAfter) {
            return $formattedPrice . $symbol;
        } else {
            return $symbol . $formattedPrice;
        }
    }

    public function getDiscountName(): ?string
    {
        return $this->historyOrderItemDiscountName;
    }

    public function getPriceDiscountImpact(): ?float
    {
        return $this->getTotalPriceBeforeDiscount() - $this->getTotalPriceAfterDiscount();
    }

    /**
     * @return string
     * @desc return the price for views
     */
    public function getPriceDiscountImpactFormatted(): string
    {
        $formattedPrice = number_format($this->getPriceDiscountImpact(), 2, '.', '');
        $symbol = $this->getPaymentSymbol();
        $symbolIsAfter = ShopSettingsModel::getInstance()->getSettingValue('after');
        if ($symbolIsAfter) {
            return $formattedPrice . $symbol;
        } else {
            return $symbol . $formattedPrice;
        }
    }

    public function getTotalPriceBeforeDiscount(): ?float
    {
        return $this->historyOrderItemTotalPriceBeforeDiscount;
    }

    /**
     * @return string
     * @desc return the price for views
     */
    public function getTotalPriceBeforeDiscountFormatted(): string
    {
        $formattedPrice = number_format($this->getTotalPriceBeforeDiscount(), 2, '.', '');
        if ($this->getItem()->getPriceType() == 'money') {
            $symbol = ShopSettingsModel::getInstance()->getSettingValue('symbol');
        } else {
            $symbol = ' ' . ShopPaymentsController::getInstance()->getPaymentByVarName($this->getHistoryOrder()->getPaymentMethod()->getVarName())->faIcon() . ' ';
        }
        $symbolIsAfter = ShopSettingsModel::getInstance()->getSettingValue('after');
        if ($symbolIsAfter) {
            return $formattedPrice . $symbol;
        } else {
            return $symbol . $formattedPrice;
        }
    }

    public function getTotalPriceAfterDiscount(): ?float
    {
        return $this->historyOrderItemTotalPriceAfterDiscount;
    }

    /**
     * @return string
     * @desc return the price for views
     */
    public function getTotalPriceAfterDiscountFormatted(): string
    {
        $formattedPrice = number_format($this->getTotalPriceAfterDiscount(), 2, '.', '');
        $symbol = $this->getPaymentSymbol();
        $symbolIsAfter = ShopSettingsModel::getInstance()->getSettingValue('after');
        if ($symbolIsAfter) {
            return $formattedPrice . $symbol;
        } else {
            return $symbol . $formattedPrice;
        }
    }

    private function getPaymentSymbol(): string
    {
        $paymentMethod = $this->getHistoryOrder()->getPaymentMethod()->getVarName();

        if ($paymentMethod !== PaymentMethodConst::FREE) {
            return ShopSettingsModel::getInstance()->getSettingValue('symbol');
        }

        return ' ' . ShopPaymentsController::getInstance()->getPaymentByVarName($paymentMethod)->faIcon() . ' ';
    }

}
