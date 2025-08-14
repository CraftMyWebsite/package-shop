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
    private ?string $historyOrderDateValidated;
    private ?string $historyOrderDateShipping;
    private ?string $historyOrderDateWithdraw;
    private ?string $historyOrderDateFinished;
    private ?string $historyOrderDateCanceled;
    private ?string $historyOrderDateRefunded;

    /**
     * @param int $historyOrderId
     * @param ?UserEntity $user
     * @param int $orderStatus
     * @param string|null $shippingLink
     * @param string $orderNumber
     * @param string $historyOrderCreated
     * @param string $historyOrderUpdated
     * @param ?string $historyOrderDateValidated
     * @param ?string $historyOrderDateShipping
     * @param ?string $historyOrderDateWithdraw
     * @param ?string $historyOrderDateFinished
     * @param ?string $historyOrderDateCanceled
     * @param ?string $historyOrderDateRefunded
     */
    public function __construct(int $historyOrderId, ?UserEntity $user, int $orderStatus, ?string $shippingLink, string $orderNumber, string $historyOrderCreated, string $historyOrderUpdated,
    ?string $historyOrderDateValidated, ?string $historyOrderDateShipping, ?string $historyOrderDateWithdraw,
    ?string $historyOrderDateFinished, ?string $historyOrderDateCanceled,
    ?string $historyOrderDateRefunded)
    {
        $this->historyOrderId = $historyOrderId;
        $this->user = $user;
        $this->orderStatus = $orderStatus;
        $this->shippingLink = $shippingLink;
        $this->orderNumber = $orderNumber;
        $this->historyOrderCreated = $historyOrderCreated;
        $this->historyOrderUpdated = $historyOrderUpdated;
        $this->historyOrderDateValidated = $historyOrderDateValidated;
        $this->historyOrderDateShipping = $historyOrderDateShipping;
        $this->historyOrderDateWithdraw = $historyOrderDateWithdraw;
        $this->historyOrderDateFinished = $historyOrderDateFinished;
        $this->historyOrderDateCanceled = $historyOrderDateCanceled;
        $this->historyOrderDateRefunded = $historyOrderDateRefunded;
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

    public function getBeautifulStatus(): string
    {
        $status = (int) $this->orderStatus;
        $type = $this->getShippingMethod()?->getShipping()?->getType();
        $autoValidateVirtual = ShopSettingsModel::getInstance()->getSettingValue('autoValidateVirtual');

        // CSS inline
        $css = <<<CSS
<style>
.cmw-stepper{
  display:flex;
  justify-content:center;
  gap:2rem;
  align-items:center;
  list-style:none;
  padding:0;
  margin:0.75rem 0;
}
.cmw-step{
  display:flex;
  flex-direction:column;
  align-items:center;
  position:relative;
  min-width:88px;
}
.cmw-step:not(:last-child)::after{
  content:"";
  position:absolute;
  top:26px;
  right:-1rem;
  width:2rem;
  height:2px;
  background:#e5e7eb;
}
.cmw-step-icon{
  width:42px;
  height:42px;
  border-radius:9999px;
  display:grid;
  place-items:center;
  background:#f3f4f6;
}
.cmw-step-icon i{ font-size:18px; }
.cmw-step-label{
  margin-top:0.5rem;
  font-weight:600;
  font-size:0.78rem; /* plus petit par défaut */
  text-align:center;
  white-space:nowrap;
}

/* done = vert pour icône uniquement, texte normal gris */
.cmw-step.is-done .cmw-step-icon{ background:#dcfce7; }
.cmw-step.is-done .cmw-step-icon i{ color:#16a34a; }
.cmw-step.is-done .cmw-step-label{ font-size:0.68rem; }

/* current = bleu, plus gros texte */
.cmw-step.is-current .cmw-step-icon{
  background:#dbeafe;
  outline:3px solid #bfdbfe;
}
.cmw-step.is-current .cmw-step-icon i{ color:#2563eb; }
.cmw-step.is-current .cmw-step-label{ color:#2563eb; font-size:0.92rem; }

/* next = gris clair */
.cmw-step.is-next .cmw-step-icon{ background:#f3f4f6; }
.cmw-step.is-next .cmw-step-icon i{ color:#9ca3af; }
.cmw-step.is-next .cmw-step-label{ color:#9ca3af; font-size:0.78rem; }

/* si current est la dernière -> vert texte + icône */
.cmw-step.is-current.is-finish .cmw-step-icon{
  background:#dcfce7;
  outline:3px solid #bbf7d0;
}
.cmw-step.is-current.is-finish .cmw-step-icon i{ color:#16a34a; }
.cmw-step.is-current.is-finish .cmw-step-label{ color:#16a34a; font-size:0.92rem; }

@media (max-width:640px){
  .cmw-stepper{ gap:1rem; }
  .cmw-step{ min-width:72px; }
  .cmw-step-icon{ width:38px; height:38px; }
  .cmw-step:not(:last-child)::after{ right:-0.75rem; width:1.5rem; top:24px; }
  .cmw-step-label{ font-size:0.72rem; }
}
.cmw-step-date {
  font-size: 0.72rem;
  color: #6b7280; /* gris-500 */
  margin-top: 0.25rem;
}

</style>
CSS;

        // Annulée / remboursée
        if ($status === -1 || $status === -2) {
            $dates = [
                $this->getCreated(),
                $this->getDateCanceled(),
                $this->getDateRefunded()
            ];
            $steps = [
                ['label' => 'Validation en cours', 'icon' => 'fa-hourglass-half'],
                ['label' => 'Commande annulée',     'icon' => 'fa-ban'],
                ['label' => 'Commande remboursée',  'icon' => 'fa-rotate-left'],
            ];
            $current = $status === -1 ? 1 : 2;
            return $css . $this->renderStepper($steps, $current, $dates);
        }

        // Virtuel uniquement (pas 0, pas 1)
        if ($type !== 0 && $type !== 1) {
            $dates = [
                $this->getCreated(),
                $this->getDateFinished()
            ];
            // Auto-validation activée → Terminé direct
            if ((int) $autoValidateVirtual === 1) {
                $steps = [
                    ['label' => 'Terminé', 'icon' => 'fa-circle-check'],
                ];
                $current = 0; // seul step actif
                return $css . $this->renderStepper($steps, $current, $dates);
            }

            // Sinon, stepper normal 2 étapes
            $steps = [
                ['label' => 'Validation en cours', 'icon' => 'fa-hourglass-half'],
                ['label' => 'Terminé',             'icon' => 'fa-circle-check'],
            ];
            $current = $status <= 0 ? 0 : 1;
            return $css . $this->renderStepper($steps, $current, $dates);
        }

        // Avec envoi
        if ($type === 0) {
            $dates = [
                $this->getCreated(),
                $this->getDateValidated(),
                $this->getDateShipping(),
                $this->getDateFinished()
            ];
            $steps = [
                ['label' => 'Validation en cours', 'icon' => 'fa-hourglass-half'], // 0
                ['label' => 'Validée',             'icon' => 'fa-clipboard-check'], // 1
                ['label' => 'Livraison en cours',  'icon' => 'fa-truck-fast'], // 2
                ['label' => 'Terminé',             'icon' => 'fa-circle-check'], // 3
            ];
            $current = max(0, min(3, $status));
            return $css . $this->renderStepper($steps, $current, $dates);
        }

        // Retrait
        if ($type === 1) {
            $dates = [
                $this->getCreated(),
                $this->getDateValidated(),
                $this->getDateWithdraw(),
                $this->getDateFinished()
            ];
            $steps = [
                ['label' => 'Validation en cours',            'icon' => 'fa-hourglass-half'], // 0
                ['label' => 'Prête au retrait',               'icon' => 'fa-store'], // 1
                ['label' => 'Venez la chercher en centre',    'icon' => 'fa-location-dot'], // 2
                ['label' => 'Terminé',                        'icon' => 'fa-circle-check'], // 3
            ];
            if ($status <= 0)      { $current = 0; }
            elseif ($status == 1)  { $current = 1; }
            elseif ($status == 2)  { $current = 2; }
            else                   { $current = 3; }
            return $css . $this->renderStepper($steps, $current, $dates);
        }

        return ''; // fallback
    }

    private function renderStepper(array $steps, int $current, array $dates = []): string
    {
        $html = '<ol class="cmw-stepper" aria-label="Statut de commande">';
        foreach ($steps as $index => $s) {
            $stateClass = $index < $current ? 'is-done' : ($index === $current ? 'is-current' : 'is-next');
            if ($index === $current && $index === count($steps) - 1) {
                $stateClass .= ' is-finish';
            }

            // On récupère la date si définie
            $dateHtml = '';
            if (!empty($dates[$index])) {
                $dateHtml = '<div class="cmw-step-date">' . htmlspecialchars($dates[$index], ENT_QUOTES, 'UTF-8') . '</div>';
            }

            $html .= sprintf(
                '<li class="cmw-step %s">
                <span class="cmw-step-icon"><i class="fa-solid %s"></i></span>
                <span class="cmw-step-label">%s</span>
                %s
            </li>',
                $stateClass,
                htmlspecialchars($s['icon'], ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($s['label'], ENT_QUOTES, 'UTF-8'),
                $dateHtml
            );
        }
        $html .= '</ol>';
        return $html;
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

    public function getDateValidated(): ?string
    {
        if ($this->historyOrderDateValidated) {
            return Date::formatDate($this->historyOrderDateValidated);
        }
        return null;
    }

    public function getDateShipping(): ?string
    {
        if ($this->historyOrderDateShipping) {
            return Date::formatDate($this->historyOrderDateShipping);
        }
        return null;
    }

    public function getDateWithdraw(): ?string
    {
        if ($this->historyOrderDateWithdraw) {
            return Date::formatDate($this->historyOrderDateWithdraw);
        }
        return null;
    }

    public function getDateFinished(): ?string
    {
        if ($this->historyOrderDateFinished) {
            return Date::formatDate($this->historyOrderDateFinished);
        }
        return null;
    }

    public function getDateCanceled(): ?string
    {
        if ($this->historyOrderDateCanceled) {
            return Date::formatDate($this->historyOrderDateCanceled);
        }
        return null;
    }

    public function getDateRefunded(): ?string
    {
        if ($this->historyOrderDateRefunded) {
            return Date::formatDate($this->historyOrderDateRefunded);
        }
        return null;
    }
}
