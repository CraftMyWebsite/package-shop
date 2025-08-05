<?php

namespace CMW\Entity\Shop\HistoryOrders;

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractEntity;
use CMW\Model\Shop\HistoryOrder\ShopHistoryOrdersInvoiceModel;
use CMW\Type\Shop\Const\Payment\PaymentPriceTypeConst;
use CMW\Utils\Date;
use CMW\Controller\Shop\Admin\Payment\ShopPaymentsController;
use CMW\Entity\Users\UserEntity;
use CMW\Model\Shop\HistoryOrder\ShopHistoryOrdersDiscountModel;
use CMW\Model\Shop\HistoryOrder\ShopHistoryOrdersItemsModel;
use CMW\Model\Shop\HistoryOrder\ShopHistoryOrdersItemsVariantesModel;
use CMW\Model\Shop\HistoryOrder\ShopHistoryOrdersPaymentModel;
use CMW\Model\Shop\HistoryOrder\ShopHistoryOrdersShippingModel;
use CMW\Model\Shop\HistoryOrder\ShopHistoryOrdersUserAddressModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;

class ShopHistoryOrdersEntity extends AbstractEntity
{
    private int $historyOrderId;
    private ?UserEntity $user;
    private int $orderStatus;
    private ?string $shippingLink;
    private string $orderNumber;
    private string $historyOrderCreated;
    private string $historyOrderUpdated;

    /**
     * @param int $historyOrderId
     * @param ?UserEntity $user
     * @param int $orderStatus
     * @param string|null $shippingLink
     * @param string $orderNumber
     * @param string $historyOrderCreated
     * @param string $historyOrderUpdated
     */
    public function __construct(int $historyOrderId, ?UserEntity $user, int $orderStatus, ?string $shippingLink, string $orderNumber, string $historyOrderCreated, string $historyOrderUpdated)
    {
        $this->historyOrderId = $historyOrderId;
        $this->user = $user;
        $this->orderStatus = $orderStatus;
        $this->shippingLink = $shippingLink;
        $this->orderNumber = $orderNumber;
        $this->historyOrderCreated = $historyOrderCreated;
        $this->historyOrderUpdated = $historyOrderUpdated;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->historyOrderId;
    }

    public function getUser(): ?UserEntity
    {
        return $this->user;
    }

    public function getOrderNumber(): string
    {
        return $this->orderNumber;
    }

    public function getAdminStatus(): string
    {
        if ($this->orderStatus == -2) {
            return LangManager::translate('shop.entities.historyOrder.refunded');
        }
        if ($this->orderStatus == -1) {
            return LangManager::translate('shop.entities.historyOrder.refund-wait');
        }
        if ($this->orderStatus == 0) {
            return LangManager::translate('shop.entities.historyOrder.new-command');
        }
        if ($this->orderStatus == 1) {
            return LangManager::translate('shop.entities.historyOrder.waiting');
        }
        if ($this->orderStatus == 2) {
            if ($this->getShippingMethod()->getShipping()->getType() == 0) {
                return LangManager::translate('shop.entities.historyOrder.shipping');
            } else {
                return LangManager::translate('shop.entities.historyOrder.withdraw');
            }
        }
        if ($this->orderStatus == 3) {
            return LangManager::translate('shop.entities.historyOrder.ended');
        }
    }

    public function getPublicStatus(): string
    {
        if ($this->orderStatus == -2) {
            return LangManager::translate('shop.entities.historyOrder.refunded-1');
        }
        if ($this->orderStatus == -1) {
            return LangManager::translate('shop.entities.historyOrder.refund-wait-1');
        }
        if ($this->orderStatus == 0) {
            return LangManager::translate('shop.entities.historyOrder.preparing');
        }
        if ($this->orderStatus == 1) {
            if ($this->getShippingMethod()->getShipping()->getType() == 0) {
                return LangManager::translate('shop.entities.historyOrder.ready');
            } else {
                return LangManager::translate('shop.entities.historyOrder.ready-withdraw');
            }
        }
        if ($this->orderStatus == 2) {
            if ($this->getShippingMethod()->getShipping()->getType() == 0) {
                return LangManager::translate('shop.entities.historyOrder.shipping-progress');
            } else {
                return LangManager::translate('shop.entities.historyOrder.ready-withdraw');
            }
        }
        if ($this->orderStatus == 3) {
            return LangManager::translate('shop.entities.historyOrder.ended-1');
        }
    }

    /**
     * @return string
     * @desc return the order status code as integer. Between -2 and 3 | -2 = refunded | -1 = canceled | 0 = new order | 1 = ready to send | 2 = delivery in progress | 3 = finished
     */
    public function getStatusCode(): int
    {
        return $this->orderStatus;
    }

    public function getShippingLink(): ?string
    {
        return $this->shippingLink;
    }

    public function getCreated(): string
    {
        return Date::formatDate($this->historyOrderCreated);
    }

    public function getUpdated(): string
    {
        return Date::formatDate($this->historyOrderUpdated);
    }

    public function getOrderTotal(): float
    {
        $total = 0;
        $shippingFee = ShopHistoryOrdersShippingModel::getInstance()?->getHistoryOrdersShippingByHistoryOrderId($this->getId())?->getPrice() ?? 0;
        $paymentFee = ShopHistoryOrdersPaymentModel::getInstance()->getHistoryOrdersPaymentByHistoryOrderId($this->historyOrderId)->getFee();
        $giftCards = $this->getAppliedCartDiscount();
        $OrderItemsModel = ShopHistoryOrdersItemsModel::getInstance();
        foreach ($OrderItemsModel->getHistoryOrdersItemsByHistoryOrderId($this->getId()) as $orderItem) {
            $total += $orderItem->getTotalPriceAfterDiscount();
        }
        if (!empty($giftCards)) {
            foreach ($giftCards as $giftCard) {
                $total -= $giftCard->getPrice();
            }
        }

        $total += $shippingFee;
        $total += $paymentFee;
        return $total;
    }

    /**
     * @return string
     * @desc return the price for views
     */
    public function getOrderTotalFormatted(): string
    {
        $formattedPrice = number_format($this->getOrderTotal(), 2, '.', '');

        $priceType = '';
        foreach ($this->getOrderedItems() as $orderedItem) {
            $priceType = $orderedItem->getItem()->getPriceType();
            break;
        }

        if ($priceType === PaymentPriceTypeConst::MONEY) {
            $symbol = ShopSettingsModel::getInstance()->getSettingValue('symbol');
        } else {
            $symbol = ' ' . ShopPaymentsController::getInstance()->getPaymentByVarName($this->getPaymentMethod()->getVarName())->faIcon() . ' ';
        }
        $symbolIsAfter = ShopSettingsModel::getInstance()->getSettingValue('after');
        if ($symbolIsAfter) {
            return $formattedPrice . $symbol;
        } else {
            return $symbol . $formattedPrice;
        }
    }

    /**
     * @return ?ShopHistoryOrdersItemsEntity []
     */
    public function getOrderedItems(): ?array
    {
        $return = ShopHistoryOrdersItemsModel::getInstance()->getHistoryOrdersItemsByHistoryOrderId($this->getId());
        return $return ?? null;
    }

    /**
     * @return ?ShopHistoryOrdersItemsVariantesEntity []
     */
    public function getOrderedItemsVariantes($orderedItemId): ?array
    {
        $return = ShopHistoryOrdersItemsVariantesModel::getInstance()->getShopItemVariantValueByOrderItemId($orderedItemId);
        return $return ?? null;
    }

    /**
     * @return ?ShopHistoryOrdersShippingEntity
     */
    public function getShippingMethod(): ?ShopHistoryOrdersShippingEntity
    {
        $return = ShopHistoryOrdersShippingModel::getInstance()?->getHistoryOrdersShippingByHistoryOrderId($this->getId());
        return $return ?? null;
    }

    /**
     * @return ?ShopHistoryOrdersUserAddressEntity
     */
    public function getUserAddressMethod(): ?ShopHistoryOrdersUserAddressEntity
    {
        $return = ShopHistoryOrdersUserAddressModel::getInstance()->getHistoryOrdersUserAddressByHistoryOrderId($this->getId());
        return $return ?? null;
    }

    /**
     * @return ?ShopHistoryOrdersPaymentEntity
     */
    public function getPaymentMethod(): ?ShopHistoryOrdersPaymentEntity
    {
        $return = ShopHistoryOrdersPaymentModel::getInstance()->getHistoryOrdersPaymentByHistoryOrderId($this->getId());
        return $return ?? null;
    }

    /**
     * @return ?ShopHistoryOrdersDiscountEntity []
     */
    public function getAppliedCartDiscount(): ?array
    {
        $return = ShopHistoryOrdersDiscountModel::getInstance()->getHistoryOrdersDiscountByHistoryOrderId($this->getId());
        return $return ?? null;
    }

    /**
     * @return ?float
     */
    public function getAppliedCartDiscountTotalPrice(): ?float
    {
        $giftCards = $this->getAppliedCartDiscount();
        $total = 0;
        foreach ($giftCards as $giftCard) {
            $total += $giftCard->getPrice();
        }
        return $total;
    }

    /**
     * @return string
     * @desc return the price for views
     */
    public function getAppliedCartDiscountTotalPriceFormatted(): string
    {
        $formattedPrice = number_format($this->getAppliedCartDiscountTotalPrice(), 2, '.', '');
        $symbol = ShopSettingsModel::getInstance()->getSettingValue('symbol');
        $symbolIsAfter = ShopSettingsModel::getInstance()->getSettingValue('after');
        if ($symbolIsAfter) {
            return $formattedPrice . $symbol;
        } else {
            return $symbol . $formattedPrice;
        }
    }

    public function getInvoiceLink(): ?string
    {
        $invoiceModel = ShopHistoryOrdersInvoiceModel::getInstance()->getInvoiceByHistoryOrderId($this->historyOrderId);
        if (!is_null($invoiceModel)) {
            return $invoiceModel->getInvoiceLink();
        }
        return null;
    }
}
