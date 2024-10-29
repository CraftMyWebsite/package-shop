<?php

namespace CMW\Controller\Shop\Admin\HistoryOrder;

use CMW\Controller\Shop\Admin\Item\ShopItemsController;
use CMW\Controller\Shop\Admin\Payment\ShopPaymentsController;
use CMW\Controller\Users\UsersController;
use CMW\Entity\Shop\Carts\ShopCartItemEntity;
use CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity;
use CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersItemsEntity;
use CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersPaymentEntity;
use CMW\Entity\Users\UserEntity;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Mail\MailManager;
use CMW\Manager\Notification\NotificationManager;
use CMW\Manager\Notification\NotificationModel;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Shop\Cart\ShopCartDiscountModel;
use CMW\Model\Shop\Cart\ShopCartItemModel;
use CMW\Model\Shop\Cart\ShopCartModel;
use CMW\Model\Shop\Cart\ShopCartVariantesModel;
use CMW\Model\Shop\Command\ShopCommandTunnelModel;
use CMW\Model\Shop\Delivery\ShopDeliveryUserAddressModel;
use CMW\Model\Shop\Discount\ShopDiscountModel;
use CMW\Model\Shop\HistoryOrder\ShopHistoryOrdersDiscountModel;
use CMW\Model\Shop\HistoryOrder\ShopHistoryOrdersItemsModel;
use CMW\Model\Shop\HistoryOrder\ShopHistoryOrdersItemsVariantesModel;
use CMW\Model\Shop\HistoryOrder\ShopHistoryOrdersModel;
use CMW\Model\Shop\HistoryOrder\ShopHistoryOrdersPaymentModel;
use CMW\Model\Shop\HistoryOrder\ShopHistoryOrdersShippingModel;
use CMW\Model\Shop\HistoryOrder\ShopHistoryOrdersUserAddressModel;
use CMW\Model\Shop\Image\ShopImagesModel;
use CMW\Model\Shop\Item\ShopItemsModel;
use CMW\Model\Shop\Item\ShopItemsVirtualMethodModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use CMW\Utils\Website;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class: @ShopHistoryOrdersController
 * @package shop
 * @author Zomblard
 * @version 1.0
 */
class ShopHistoryOrdersController extends AbstractController
{
    #[Link('/orders', Link::GET, [], '/cmw-admin/shop')]
    private function shopOrders(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.orders');

        $inProgressOrders = ShopHistoryOrdersModel::getInstance()->getInProgressOrders();
        $errorOrders = ShopHistoryOrdersModel::getInstance()->getErrorOrders();
        $finishedOrders = ShopHistoryOrdersModel::getInstance()->getFinishedOrders();
        $orderItemsModel = ShopHistoryOrdersModel::getInstance();
        $notificationIsRefused = in_array('Shop', NotificationModel::getInstance()->getRefusedPackages(), true);

        View::createAdminView('Shop', 'Orders/orders')
            ->addStyle('Admin/Resources/Assets/Css/simple-datatables.css')
            ->addScriptAfter('Admin/Resources/Vendors/Simple-datatables/simple-datatables.js',
                'Admin/Resources/Vendors/Simple-datatables/config-datatables.js')
            ->addVariableList(['inProgressOrders' => $inProgressOrders, 'errorOrders' => $errorOrders, 'finishedOrders' => $finishedOrders, 'orderItemsModel' => $orderItemsModel, 'notificationIsRefused' => $notificationIsRefused])
            ->view();
    }

    #[Link('/orders/manage/:orderId', Link::GET, [], '/cmw-admin/shop')]
    private function shopManageOrders(int $orderId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.orders.manage');

        $order = ShopHistoryOrdersModel::getInstance()->getHistoryOrdersById($orderId);

        if (!$order){
            Redirect::errorPage(404);
        }

        $defaultImage = ShopImagesModel::getInstance()->getDefaultImg();

        $orderStatus = $order->getStatusCode();

        if ($orderStatus === 0) {
            View::createAdminView('Shop', 'Orders/Manage/new')
                ->addVariableList(['order' => $order, 'defaultImage' => $defaultImage])
                ->view();
        }
        if ($orderStatus === 1) {
            $order = ShopHistoryOrdersModel::getInstance()->getHistoryOrdersById($orderId);
            $orderOnlyVirtual = $this->handleOrderTypeContent($order->getOrderedItems());
            if ($orderOnlyVirtual) {
                ShopHistoryOrdersModel::getInstance()->endOrder($orderId);
                Flash::send(Alert::SUCCESS, 'Boutique', 'Félicitations commande terminé !');
                // ExecVirtualNeeds :
                $items = ShopHistoryOrdersItemsModel::getInstance()->getHistoryOrdersItemsByHistoryOrderId($orderId);
                foreach ($items as $item) {
                    if ($item->getItem()->getType() === 1) {
                        $virtualItemVarName = ShopItemsVirtualMethodModel::getInstance()->getVirtualItemMethodByItemId($item->getItem()->getId())->getVirtualMethod()->varName();
                        $quantity = $item->getQuantity();
                        for ($i = 0; $i < $quantity; $i++) {
                            ShopItemsController::getInstance()->getVirtualItemsMethodsByVarName($virtualItemVarName)?->execOnBuy($virtualItemVarName, $item->getItem(), $order->getUser());
                        }
                    }
                }
                // TODO Emitter endedOrder
                // TODO Notify
                Redirect::redirect('cmw-admin/shop/orders');
            } else {
                View::createAdminView('Shop', 'Orders/Manage/send')
                    ->addVariableList(['order' => $order, 'defaultImage' => $defaultImage])
                    ->view();
            }
        }
        if ($orderStatus === 2) {
            View::createAdminView('Shop', 'Orders/Manage/finish')
                ->addVariableList(['order' => $order, 'defaultImage' => $defaultImage])
                ->view();
        }
        if ($orderStatus === -1) {
            View::createAdminView('Shop', 'Orders/Manage/cancel')
                ->addVariableList(['order' => $order, 'defaultImage' => $defaultImage])
                ->view();
        }
    }

    #[Link('/orders/view/:orderId', Link::GET, [], '/cmw-admin/shop')]
    private function shopViewOrders(int $orderId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.orders.manage');

        $order = ShopHistoryOrdersModel::getInstance()->getHistoryOrdersById($orderId);
        $defaultImage = ShopImagesModel::getInstance()->getDefaultImg();

        View::createAdminView('Shop', 'Orders/view')
            ->addVariableList(['order' => $order, 'defaultImage' => $defaultImage])
            ->view();
    }

    #[NoReturn]
    #[Link('/orders/manage/send/:orderId', Link::POST, [], '/cmw-admin/shop')]
    private function shopManageSendStep(int $orderId): void
    {
        ShopHistoryOrdersModel::getInstance()->toSendStep($orderId);
        // TODO : Notifier l'utilisateur

        // TODO Emitter sendOrder

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/orders/manage/finish/:orderId', Link::POST, [], '/cmw-admin/shop')]
    private function shopManageFinalStep(int $orderId): void
    {
        [$shippingLink] = Utils::filterInput('shipping_link');
        ShopHistoryOrdersModel::getInstance()->toFinalStep($orderId, ($shippingLink === '' ? null : $shippingLink));

        // TODO : Notifier l'utilisateur

        // TODO Emitter finishedOrder

        Redirect::redirect('cmw-admin/shop/orders');
    }

    #[NoReturn]
    #[Link('/orders/manage/end/:orderId', Link::POST, [], '/cmw-admin/shop')]
    private function shopManageEndStep(int $orderId): void
    {
        ShopHistoryOrdersModel::getInstance()->endOrder($orderId);

        // TODO : Notifier l'utilisateur

        // ExecVirtualNeeds :
        $order = ShopHistoryOrdersModel::getInstance()->getHistoryOrdersById($orderId);
        $items = ShopHistoryOrdersItemsModel::getInstance()->getHistoryOrdersItemsByHistoryOrderId($orderId);
        foreach ($items as $item) {
            if ($item->getItem()->getType() == 1) {
                $virtualItemVarName = ShopItemsVirtualMethodModel::getInstance()->getVirtualItemMethodByItemId($item->getItem()->getId())->getVirtualMethod()->varName();
                $quantity = $item->getQuantity();
                for ($i = 0; $i < $quantity; $i++) {
                    ShopItemsController::getInstance()->getVirtualItemsMethodsByVarName($virtualItemVarName)->execOnBuy($virtualItemVarName, $item->getItem(), $order->getUser());
                }
            }
        }

        // TODO Emitter endedOrder

        Redirect::redirect('cmw-admin/shop/orders');
    }

    #[NoReturn]
    #[Link('/orders/manage/cancel/:orderId', Link::POST, [], '/cmw-admin/shop')]
    private function shopManageCancelStep(int $orderId): void
    {
        ShopHistoryOrdersModel::getInstance()->toCancelStep($orderId);

        // TODO : Notifier l'utilisateur

        // Eitter canceled

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/orders/manage/refunded/:orderId', Link::POST, [], '/cmw-admin/shop')]
    private function shopManageRefundStep(int $orderId): void
    {
        ShopHistoryOrdersModel::getInstance()->refundStep($orderId);

        // TODO : Notifier l'utilisateur

        // Eitter refunded

        Redirect::redirect('cmw-admin/shop/orders');
    }

    public function handleCreateOrder(UserEntity $user): void
    {
        // TODO : Baisser les stock si besoin
        $sessionId = session_id();

        $commandTunnel = ShopCommandTunnelModel::getInstance()->getShopCommandTunnelByUserId($user->getId());
        $order = ShopHistoryOrdersModel::getInstance()->createHistoryOrder($user->getId(), 0);
        $discountModel = ShopDiscountModel::getInstance();

        $paymentMethod = ShopPaymentsController::getInstance()->getPaymentByVarName($commandTunnel->getPaymentName());
        $paymentHistory = ShopHistoryOrdersPaymentModel::getInstance()->addHistoryPaymentOrder($order->getId(), $paymentMethod->name(), $paymentMethod->varName(), $paymentMethod->fees());

        if (!is_null($commandTunnel->getShipping())) {
            $shippingHistory = ShopHistoryOrdersShippingModel::getInstance()->addHistoryShippingOrder($order->getId(), $commandTunnel->getShipping()->getName(), $commandTunnel->getShipping()->getPrice());
        }

        $userAddress = ShopDeliveryUserAddressModel::getInstance()->getShopDeliveryUserAddressById($commandTunnel->getShopDeliveryUserAddress()->getId());
        $userAddressHistory = ShopHistoryOrdersUserAddressModel::getInstance()->addHistoryUserAddressOrder($order->getId(), $userAddress->getLabel(), $user->getMail(), $userAddress->getLastName(), $userAddress->getFirstName(), $userAddress->getLine1(), $userAddress->getLine2(), $userAddress->getCity(), $userAddress->getPostalCode(), $userAddress->getCountry(), $userAddress->getPhone());

        $this->handleCartDiscount($user, $sessionId, $order, $discountModel);

        $cartContent = ShopCartItemModel::getInstance()->getShopCartsItemsByUserId($user->getId(), $sessionId);
        $cartOnlyVirtual = $this->handleCartTypeContent($cartContent);
        foreach ($cartContent as $cartItem) {
            $discountId = $this->handleDefaultAppliedDiscount($cartItem, $discountModel);
            if (!is_null($discountId)) {
                $discountName = ShopDiscountModel::getInstance()->getAllShopDiscountById($discountId)->getName();
            } else {
                $discountName = $cartItem->getDiscount()?->getName() ?? '';
            }
            $orderItem = ShopHistoryOrdersItemsModel::getInstance()->createHistoryOrderItems($cartItem->getItem()->getId(), $order->getId(), $cartItem->getItem()->getName(), $cartItem->getFirstImageItemUrl(), $cartItem->getQuantity(), $cartItem->getItem()->getPrice(), $discountName, $cartItem->getItemTotalPriceBeforeDiscount(), $cartItem->getItemTotalPriceAfterDiscount());
            $itemsVariantes = ShopCartVariantesModel::getInstance()->getShopItemVariantValueByCartId($cartItem->getId());
            if (!empty($itemsVariantes)) {
                foreach ($itemsVariantes as $itemVariantes) {
                    ShopHistoryOrdersItemsVariantesModel::getInstance()->setVariantToItemInOrder($orderItem->getId(), $itemVariantes->getVariantValue()->getValue(), $itemVariantes->getVariantValue()->getVariant()->getName());
                }
            }
            if ($cartOnlyVirtual && ShopSettingsModel::getInstance()->getSettingValue('autoValidateVirtual')) {
                ShopHistoryOrdersModel::getInstance()->endOrder($order->getId());
                $virtualItemVarName = ShopItemsVirtualMethodModel::getInstance()->getVirtualItemMethodByItemId($cartItem->getItem()->getId())->getVirtualMethod()->varName();
                $quantity = $cartItem->getQuantity();
                for ($i = 0; $i < $quantity; $i++) {
                    ShopItemsController::getInstance()->getVirtualItemsMethodsByVarName($virtualItemVarName)->execOnBuy($virtualItemVarName, $cartItem->getItem(), $user);
                }
            }
            if ($cartItem->getItem()->getCurrentStock()) {
                $nextStock = $cartItem->getItem()->getCurrentStock() - $cartItem->getQuantity();
                ShopItemsModel::getInstance()->decreaseStock($cartItem->getItem()->getId(), $nextStock);

                $percentage = ($nextStock / $cartItem->getItem()->getDefaultStock()) * 100;
                $stockAlert = ShopSettingsModel::getInstance()->getSettingValue('stockAlert');

                if ($percentage <= $stockAlert) {
                    NotificationManager::notify('Stock faible', 'Les stock pour ' . $cartItem->getItem()->getName() . ' sont faible ! (' . $nextStock . '/' . $cartItem->getItem()->getDefaultStock() . ')');
                }
            }
        }

        NotificationManager::notify('Nouvelle commande', $user->getPseudo() . ' viens de passer une commande.', 'shop/orders');

        $this->notifyUser($cartContent, $user, $order, $paymentHistory);

        // Eitter newOrder

        ShopCartModel::getInstance()->clearUserCart($user->getId());
        ShopCommandTunnelModel::getInstance()->clearTunnel($user->getId());
    }

    private function handleCartDiscount(UserEntity $user, string $sessionId, ShopHistoryOrdersEntity $order, ShopDiscountModel $discountModel): void
    {
        $cartDiscounts = ShopCartDiscountModel::getInstance()->getCartDiscountByUserId($user->getId(), $sessionId);
        foreach ($cartDiscounts as $cartDiscount) {
            $discountId = $cartDiscount->getDiscount()?->getId();
            $thisDiscount = $discountModel->getShopDiscountById($discountId);
            $currentUses = $thisDiscount->getCurrentUses();
            $currentUses += 1;
            $maxUses = $thisDiscount->getMaxUses();
            if ($currentUses !== null && $maxUses !== null && $maxUses <= $currentUses) {
                $discountModel->updateStatus($discountId, 0);
            }
            $discountModel->addUses($discountId, $currentUses);

            if ($cartDiscount->getDiscount()->getLinked() === 3) {
                ShopHistoryOrdersDiscountModel::getInstance()->addHistoryDiscountOrder($order->getId(), $cartDiscount->getDiscount()->getName(), $cartDiscount->getDiscount()->getPrice(), 0);
            }
        }
    }

    /**
     * @desc Add uses and define status if Max uses reached
     */
    private function handleDefaultAppliedDiscount(ShopCartItemEntity $cartItem, ShopDiscountModel $discountModel): ?int
    {
        if ($cartItem->getItem()->getPriceDiscountDefaultApplied()) {
            $discountId = $cartItem->getItem()->getDiscountEntityApplied()?->getId();
            $currentUses = $cartItem->getItem()->getDiscountEntityApplied()?->getCurrentUses();
            $currentUses += 1;
            $maxUses = $cartItem->getItem()->getDiscountEntityApplied()?->getMaxUses();
            if ($currentUses !== null && $maxUses !== null && $maxUses <= $currentUses) {
                $discountModel->updateStatus($discountId, 0);
            }
            $discountModel->addUses($discountId, $currentUses);
        } else {
            $discountId = $cartItem->getDiscount()?->getId() ?? null;
        }
        return $discountId ?? null;
    }

    /**
     * @param ShopCartItemEntity[] $cartContent
     */
    private function handleCartTypeContent(array $cartContent): bool
    {
        foreach ($cartContent as $item) {
            if ($item->getItem()->getType() === 0) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param ShopHistoryOrdersItemsEntity[] $orderContent
     */
    private function handleOrderTypeContent(array $orderContent): bool
    {
        foreach ($orderContent as $item) {
            if ($item->getItem()->getType() === 0) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param ShopCartItemEntity[] $cartContent
     */
    private function notifyUser(array $cartContent, UserEntity $user, ShopHistoryOrdersEntity $order, ShopHistoryOrdersPaymentEntity $paymentHistory): void
    {
        // TODO : Retravaille cette partie pour mieux afficher les reduction appliqué et tout et aussi verifier si les mails sont bien configurer
        $websiteName = Website::getWebsiteName();
        $orderNumber = $order->getOrderNumber();
        $orderDate = $order->getCreated();
        $paymentMethod = $paymentHistory->getName();
        $historyLink = Website::getUrl() . '/shop/history';

        $priceType = '';
        $itemsHtml = '';
        foreach ($cartContent as $cartItem) {
            $priceType = $cartItem->getItem()->getPriceType();
            $itemName = $cartItem->getItem()->getName();
            $itemQuantity = $cartItem->getQuantity();
            $itemPrice = $cartItem->getItemTotalPriceAfterDiscountFormatted();
            $itemsHtml .= <<<HTML
                    <div class="summary-item">
                        <span class="summary-title">Article :</span> $itemName
                        <br>
                        <span class="summary-title">Quantité :</span> $itemQuantity
                        <br>
                        <span class="summary-title">Prix :</span> $itemPrice
                    </div>
                HTML;
        }

        if ($priceType == 'money') {
            $symbol = ShopSettingsModel::getInstance()->getSettingValue('symbol');
        } else {
            $symbol = ' ' . ShopItemsController::getInstance()->getPriceTypeMethodsByVarName($priceType)->name() . ' ';
        }
        $symbolIsAfter = ShopSettingsModel::getInstance()->getSettingValue('after');
        if ($symbolIsAfter) {
            $total = $order->getOrderTotal() . $symbol;
        } else {
            $total = $symbol . $order->getOrderTotal();
        }

        $htmlTemplate = <<<HTML
            <!DOCTYPE html>
            <html lang="fr">
            <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Récapitulatif de Commande</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 0;
                    background-color: #d2d2d2;
                }
                .container {
                    width: 600px;
                    margin: 20px auto;
                    background: white;
                    padding: 20px;
                    border-radius: 8px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                }
                .header {
                    background-color: #214e7e;
                    color: white;
                    padding: 10px;
                    text-align: center;
                    border-radius: 5px 5px 0 0;
                }
                .summary-item {
                    border-bottom: 1px solid #eee;
                    padding: 10px 0;
                }
                .summary-item:last-child {
                    border-bottom: none;
                }
                .summary-title {
                    font-weight: bold;
                }
                .footer {
                    text-align: center;
                    margin-top: 20px;
                    color: #777;
                }
            </style>
            </head>
            <body>
            <div class="container">
                <div class="header">
                    <h2>Votre commande sur %WEBSITENAME%</h2>
                </div>
                <div class="summary">
                    <div class="summary-item">
                        <span class="summary-title">Numéro de commande :</span> %ORDER%
                    </div>
                    <div class="summary-item">
                        <span class="summary-title">Date de la commande :</span> %DATE%
                    </div>
                    $itemsHtml
                    <div class="summary-item">
                        <span class="summary-title">Total :</span> <b>%TOTAL%</b>
                        <br>
                        <span class="summary-title">Méthode de paiement :</span> %PAYMENT_METHOD%
                        <a href="%HISTORY_LINK%"><p>Consultez mes commandes sur %WEBSITENAME%</p></a>
                    </div>
                </div>
                <div class="footer">
                    Merci pour votre achat !
                </div>
            </div>
            </body>
            </html>
            HTML;

        $body = str_replace(['%WEBSITENAME%', '%ORDER%', '%DATE%', '%TOTAL%', '%PAYMENT_METHOD%', '%HISTORY_LINK%'],
            [$websiteName, $orderNumber, $orderDate, $total, $paymentMethod, $historyLink], $htmlTemplate);
        $object = $websiteName . ' - Récapitulatif de Commande';
        MailManager::getInstance()->sendMail($user->getMail(), $object, $body);
    }
}
