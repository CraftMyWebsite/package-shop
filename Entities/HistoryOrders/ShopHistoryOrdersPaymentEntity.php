<?php

namespace CMW\Entity\Shop\HistoryOrders;

use CMW\Controller\Shop\Admin\Payment\ShopPaymentsController;
use CMW\Manager\Package\AbstractEntity;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Type\Shop\Const\Payment\PaymentPriceTypeConst;

class ShopHistoryOrdersPaymentEntity extends AbstractEntity
{
    private int $historyOrderPaymentId;
    private ShopHistoryOrdersEntity $historyOrder;
    private ?string $historyOrderPaymentName;
    private ?string $historyOrderPaymentVarName;
    private ?float $historyOrderPaymentFee;

    /**
     * @param int $historyOrderPaymentId
     * @param \CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity $historyOrder
     * @param string|null $historyOrderPaymentName
     * @param float|null $historyOrderPaymentFee
     */
    public function __construct(int $historyOrderPaymentId, ShopHistoryOrdersEntity $historyOrder, ?string $historyOrderPaymentName, ?string $historyOrderPaymentVarName, ?float $historyOrderPaymentFee)
    {
        $this->historyOrderPaymentId = $historyOrderPaymentId;
        $this->historyOrder = $historyOrder;
        $this->historyOrderPaymentName = $historyOrderPaymentName;
        $this->historyOrderPaymentVarName = $historyOrderPaymentVarName;
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

    public function getVarName(): ?string
    {
        return $this->historyOrderPaymentVarName;
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

        $priceType = '';
        foreach ($this->getHistoryOrder()->getOrderedItems() as $orderedItem) {
            $priceType = $orderedItem->getItem()->getPriceType();
            break;
        }

        if ($priceType === PaymentPriceTypeConst::MONEY) {
            $symbol = ShopSettingsModel::getInstance()->getSettingValue('symbol');
        } else {
            // Récupération sécurisée de l'implémentation
            $method = ShopPaymentsController::getInstance()->getPaymentByVarName($priceType);
            if ($method) {
                $symbol = ' ' . $method->faIcon() . ' ';
            } else {
                $symbol = ''; // fallback safe
            }
        }

        $symbolIsAfter = ShopSettingsModel::getInstance()->getSettingValue('after');
        if ($symbolIsAfter) {
            return $formattedPrice . $symbol;
        } else {
            return $symbol . $formattedPrice;
        }
    }
}
