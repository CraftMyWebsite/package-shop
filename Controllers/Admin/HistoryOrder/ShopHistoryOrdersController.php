<?php

namespace CMW\Controller\Shop\Admin\HistoryOrder;

require_once 'App/Package/Shop/Resources/TCPDF/tcpdf.php';

use CMW\Controller\Shop\Admin\Item\ShopItemsController;
use CMW\Controller\Shop\Admin\Notify\ShopNotifyController;
use CMW\Controller\Shop\Admin\Payment\ShopPaymentsController;
use CMW\Controller\Shop\Admin\Shipping\ShopShippingController;
use CMW\Controller\Users\UsersController;
use CMW\Entity\Shop\Carts\ShopCartItemEntity;
use CMW\Entity\Shop\Enum\Item\ShopItemType;
use CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity;
use CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersItemsEntity;
use CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersPaymentEntity;
use CMW\Entity\Users\UserEntity;
use CMW\Event\Shop\ShopCanceledOrderEvent;
use CMW\Event\Shop\ShopEndOrderEvent;
use CMW\Event\Shop\ShopFinishedOrderEvent;
use CMW\Event\Shop\ShopNewOrderEvent;
use CMW\Event\Shop\ShopRefundedCreditOrderEvent;
use CMW\Event\Shop\ShopRefundedSelfOrderEvent;
use CMW\Event\Shop\ShopSendOrderEvent;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Events\Emitter;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Mail\MailManager;
use CMW\Manager\Notification\NotificationManager;
use CMW\Manager\Notification\NotificationModel;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Core\MailModel;
use CMW\Model\Shop\Cart\ShopCartDiscountModel;
use CMW\Model\Shop\Cart\ShopCartItemModel;
use CMW\Model\Shop\Cart\ShopCartModel;
use CMW\Model\Shop\Cart\ShopCartVariantesModel;
use CMW\Model\Shop\Command\ShopCommandTunnelModel;
use CMW\Model\Shop\Delivery\ShopDeliveryUserAddressModel;
use CMW\Model\Shop\Discount\ShopDiscountModel;
use CMW\Model\Shop\HistoryOrder\ShopHistoryOrdersDiscountModel;
use CMW\Model\Shop\HistoryOrder\ShopHistoryOrdersInvoiceModel;
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
use CMW\Model\Users\UsersModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use CMW\Utils\Website;
use Exception;
use JetBrains\PhpStorm\NoReturn;
use TCPDF;

/**
 * Class: @ShopHistoryOrdersController
 * @package shop
 * @author Zomblard
 * @version 0.0.1
 */
class ShopHistoryOrdersController extends AbstractController
{
    #[Link('/orders/inProgress', Link::GET, [], '/cmw-admin/shop')]
    private function shopCurrentOrders(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.order');

        $inProgressOrders = ShopHistoryOrdersModel::getInstance()->getInProgressOrders();
        $notificationIsRefused = in_array('Shop', NotificationModel::getInstance()->getRefusedPackages(), true);

        View::createAdminView('Shop', 'Orders/ordersInProgress')
            ->addStyle('Admin/Resources/Assets/Css/simple-datatables.css')
            ->addScriptAfter('Admin/Resources/Vendors/Simple-datatables/simple-datatables.js',
                'Admin/Resources/Vendors/Simple-datatables/config-datatables.js')
            ->addVariableList(['inProgressOrders' => $inProgressOrders, 'notificationIsRefused' => $notificationIsRefused])
            ->view();
    }

    #[Link('/orders/ended', Link::GET, [], '/cmw-admin/shop')]
    private function shopEndedOrders(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.order');

        $finishedOrders = ShopHistoryOrdersModel::getInstance()->getFinishedOrders();
        $notificationIsRefused = in_array('Shop', NotificationModel::getInstance()->getRefusedPackages(), true);

        View::createAdminView('Shop', 'Orders/ordersEnded')
            ->addStyle('Admin/Resources/Assets/Css/simple-datatables.css')
            ->addScriptAfter('Admin/Resources/Vendors/Simple-datatables/simple-datatables.js',
                'Admin/Resources/Vendors/Simple-datatables/config-datatables.js')
            ->addVariableList(['finishedOrders' => $finishedOrders, 'notificationIsRefused' => $notificationIsRefused])
            ->view();
    }

    #[Link('/orders/canceled', Link::GET, [], '/cmw-admin/shop')]
    private function shopErrorOrders(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.order');

        $errorOrders = ShopHistoryOrdersModel::getInstance()->getErrorOrders();
        $notificationIsRefused = in_array('Shop', NotificationModel::getInstance()->getRefusedPackages(), true);

        View::createAdminView('Shop', 'Orders/ordersCanceled')
            ->addStyle('Admin/Resources/Assets/Css/simple-datatables.css')
            ->addScriptAfter('Admin/Resources/Vendors/Simple-datatables/simple-datatables.js',
                'Admin/Resources/Vendors/Simple-datatables/config-datatables.js')
            ->addVariableList(['errorOrders' => $errorOrders, 'notificationIsRefused' => $notificationIsRefused])
            ->view();
    }

    #[Link('/orders/inProgress/manage/:orderId', Link::GET, [], '/cmw-admin/shop')]
    private function shopManageOrders(int $orderId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.order.manage');

        $order = ShopHistoryOrdersModel::getInstance()->getHistoryOrdersById($orderId);

        if (!$order){
            Redirect::errorPage(404);
        }

        $defaultImage = ShopImagesModel::getInstance()->getDefaultImg();

        $orderStatus = $order->getStatusCode();

        $reviewEnabled = ShopSettingsModel::getInstance()->getSettingValue('reviews');

        if ($orderStatus === 0) {
            View::createAdminView('Shop', 'Orders/Manage/new')
                ->addVariableList(['reviewEnabled' => $reviewEnabled, 'order' => $order, 'defaultImage' => $defaultImage])
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
                    if ($item->getItem()->getType() === ShopItemType::VIRTUAL) {
                        $virtualItemVarName = ShopItemsVirtualMethodModel::getInstance()->getVirtualItemMethodByItemId($item->getItem()->getId())->getVirtualMethod()->varName();
                        $quantity = $item->getQuantity();
                        for ($i = 0; $i < $quantity; $i++) {
                            ShopItemsController::getInstance()->getVirtualItemsMethodsByVarName($virtualItemVarName)?->execOnBuy($virtualItemVarName, $item->getItem(), $order->getUser());
                        }
                    }
                }
                try {
                    Emitter::send(ShopEndOrderEvent::class, $order);
                } catch (Exception) {
                    error_log('Error while sending ShopEndOrderEvent');
                }

                $htmlMessage =<<<HTML
                <p>Le traitement de votre commande est terminé.</p>
                <p>Commande N°<b>%NUMBER%</b></p>
                <p>Retrouvez l'historique de vos commandes en <a href="%LINK%">cliquant ici.</a></p>
                HTML;
                $finalMessage = str_replace(['%NUMBER%', '%LINK%'], [$order->getOrderNumber(), Website::getUrl() . EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'shop/history'], $htmlMessage);
                ShopNotifyController::getInstance()->notifyUser($order->getUser()->getMail(), "Commande terminé", "Commande terminé", $finalMessage);

                Redirect::redirect('cmw-admin/shop/orders/inProgress');
            } else {
                View::createAdminView('Shop', 'Orders/Manage/send')
                    ->addVariableList(['reviewEnabled' => $reviewEnabled, 'order' => $order, 'defaultImage' => $defaultImage])
                    ->view();
            }
        }
        if ($orderStatus === 2) {
            View::createAdminView('Shop', 'Orders/Manage/finish')
                ->addVariableList(['reviewEnabled' => $reviewEnabled, 'order' => $order, 'defaultImage' => $defaultImage])
                ->view();
        }
        if ($orderStatus === -1) {
            View::createAdminView('Shop', 'Orders/Manage/cancel')
                ->addVariableList(['reviewEnabled' => $reviewEnabled, 'order' => $order, 'defaultImage' => $defaultImage])
                ->view();
        }
    }

    #[Link('/orders/view/:orderId', Link::GET, [], '/cmw-admin/shop')]
    private function shopViewOrders(int $orderId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.order.manage.passed');

        $order = ShopHistoryOrdersModel::getInstance()->getHistoryOrdersById($orderId);
        $defaultImage = ShopImagesModel::getInstance()->getDefaultImg();
        $reviewEnabled = ShopSettingsModel::getInstance()->getSettingValue('reviews');

        View::createAdminView('Shop', 'Orders/view')
            ->addVariableList(['reviewEnabled' => $reviewEnabled, 'order' => $order, 'defaultImage' => $defaultImage])
            ->view();
    }

    #[Link('/orders/canceled/view/:orderId', Link::GET, [], '/cmw-admin/shop')]
    private function shopViewCanceledOrders(int $orderId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.order.manage.passed');

        $order = ShopHistoryOrdersModel::getInstance()->getHistoryOrdersById($orderId);
        $defaultImage = ShopImagesModel::getInstance()->getDefaultImg();
        $reviewEnabled = ShopSettingsModel::getInstance()->getSettingValue('reviews');

        View::createAdminView('Shop', 'Orders/view')
            ->addVariableList(['reviewEnabled' => $reviewEnabled, 'order' => $order, 'defaultImage' => $defaultImage])
            ->view();
    }

    #[Link('/orders/ended/view/:orderId', Link::GET, [], '/cmw-admin/shop')]
    private function shopViewEndedOrders(int $orderId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.order.manage.passed');

        $order = ShopHistoryOrdersModel::getInstance()->getHistoryOrdersById($orderId);
        $defaultImage = ShopImagesModel::getInstance()->getDefaultImg();
        $reviewEnabled = ShopSettingsModel::getInstance()->getSettingValue('reviews');

        View::createAdminView('Shop', 'Orders/view')
            ->addVariableList(['reviewEnabled' => $reviewEnabled, 'order' => $order, 'defaultImage' => $defaultImage])
            ->view();
    }

    #[NoReturn]
    #[Link('/orders/:orderId/reviewReminder/:itemId/:userId', Link::GET, [], '/cmw-admin/shop')]
    private function shopViewOrdersRelanceReviews(int $orderId, int $itemId, int $userId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.order.manage.passed.rating');

        $user = UsersModel::getInstance()?->getUserById($userId);
        $item = ShopItemsModel::getInstance()?->getShopItemsById($itemId);

        if (is_null($item) || is_null($user)) {
            Flash::send(Alert::ERROR, 'Relance avis', 'Article ou Utilisateur introuvable !');
            Redirect::redirectPreviousRoute();
        }

        $url = $item->getItemLink();
        $message = LangManager::translate('shop.views.elements.global.reviewReminder.message-default');
        $footer1 = LangManager::translate('shop.views.elements.global.reviewReminder.footer-1-default');
        $footer2 = LangManager::translate('shop.views.elements.global.reviewReminder.footer-2-default');

        $htmlTemplate = <<<HTML
            <div class="gift-card">
              <p>%MESSAGE%</p>
              <div class="code"><a href="%ITEM_URL%">%ITEM_NAME%</a></div><br>
              <p>%FOOTER_1%<br>
              <a href="%ITEM_URL%">%FOOTER_2%</a></p>
            </div>
            HTML;

        $body = str_replace(['%MESSAGE%',  '%ITEM_URL%', '%ITEM_NAME%', '%FOOTER_1%', '%FOOTER_2%',],
            [$message, $url, $item->getName(), $footer1, $footer2], $htmlTemplate);
        if (MailModel::getInstance()->getConfig() !== null && MailModel::getInstance()->getConfig()->isEnable()) {
            Flash::send(Alert::SUCCESS, 'Relance avis', $user->getPseudo() . ' à reçu une relance par mail !');
            ShopNotifyController::getInstance()->notifyUser($user->getMail(), "Votre avis nous intéresse !", "Votre avis nous intéresse !", $body);
        } else {
            Flash::send(Alert::ERROR, 'Relance avis','Nous n\'avons pas réussi à envoyer le mail au client !');
        }
        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/orders/inProgress/manage/send/:orderId', Link::POST, [], '/cmw-admin/shop')]
    private function shopManageSendStep(int $orderId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.order.manage.ready');
        // Exec shipping method :
        $thisOrder = ShopHistoryOrdersModel::getInstance()->getHistoryOrdersById($orderId);

        $orderOnlyVirtual = $this->handleOrderTypeContent($thisOrder->getOrderedItems());
        if (!$orderOnlyVirtual) {
            $shippingMethodVarName = $thisOrder->getShippingMethod()?->getShipping()?->getShippingMethod()?->varName();
            $items = $thisOrder->getOrderedItems();
            $userEntity = $thisOrder->getUser();
            ShopShippingController::getInstance()->getShippingMethodsByVarName($shippingMethodVarName)?->execAfterCommandValidatedByAdmin($shippingMethodVarName,$items,$userEntity, $thisOrder);
        }


        if (!$orderOnlyVirtual) {
            if ($thisOrder->getShippingMethod()?->getShipping()->getType() === 0) {
                ShopHistoryOrdersModel::getInstance()->toSendStep($orderId);
            } else {
                ShopHistoryOrdersModel::getInstance()->toFinalStep($orderId, null);
            }
        } else {
            ShopHistoryOrdersModel::getInstance()->toSendStep($orderId);
        }

        try {
            Emitter::send(ShopSendOrderEvent::class, $thisOrder);
        } catch (Exception) {
            error_log('Error while sending ShopSendOrderEvent');
        }

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/orders/inProgress/manage/finish/:orderId', Link::POST, [], '/cmw-admin/shop')]
    private function shopManageFinalStep(int $orderId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.order.manage.shipping');

        [$shippingLink] = Utils::filterInput('shipping_link');
        ShopHistoryOrdersModel::getInstance()->toFinalStep($orderId, ($shippingLink === '' ? null : $shippingLink));

        $order = ShopHistoryOrdersModel::getInstance()->getHistoryOrdersById($orderId);
        $orderAddress = ShopHistoryOrdersUserAddressModel::getInstance()->getHistoryOrdersUserAddressByHistoryOrderId($orderId);
        $receiver = $orderAddress->getUserFirstName() . ' ' . $orderAddress->getUserLastName();
        $line = $orderAddress->getUserLine1();
        $PC = $orderAddress->getUserPostalCode();
        $city = $orderAddress->getUserCity();
        $country = $orderAddress->getUserFormattedCountry();
        $shipping = $shippingLink !== '' ? '<h3><a href="' . $shippingLink . '">Suivre le colis</a></h3>' : '';


        $htmlMessage =<<<HTML
                <p>Votre commande N°<b>%NUMBER%</b> vient d'être expediée !</p>
                <p>Vous la recevrez dans quelque jours à l'adresse :</p>
                <h2>%RECEIVER% <br> %LINE% <br> %PC% %CITY% <br> %COUNTRY%</h2>
                %SHIPPING%
                <p>Retrouvez l'historique de vos commandes en <a href="%LINK%">cliquant ici.</a></p>
                HTML;
        $finalMessage = str_replace(['%NUMBER%', '%LINK%', '%LINE%', '%PC%', '%CITY%', '%COUNTRY%', '%RECEIVER%', '%SHIPPING%'],
            [$order->getOrderNumber(), Website::getUrl() . EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'shop/history', $line, $PC, $city, $country, $receiver, $shipping], $htmlMessage);
        ShopNotifyController::getInstance()->notifyUser($order->getUser()->getMail(), "Commande en chemin !", "Commande en chemin !", $finalMessage);

        try {
            Emitter::send(ShopFinishedOrderEvent::class, $orderId);
        } catch (Exception) {
            error_log('Error while sending ShopSendOrderEvent');
        }

        Redirect::redirect('cmw-admin/shop/orders/inProgress');
    }

    #[NoReturn]
    #[Link('/orders/inProgress/manage/end/:orderId', Link::POST, [], '/cmw-admin/shop')]
    private function shopManageEndStep(int $orderId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.order.manage.endSuccess');

        ShopHistoryOrdersModel::getInstance()->endOrder($orderId);

        // ExecVirtualNeeds :
        $order = ShopHistoryOrdersModel::getInstance()->getHistoryOrdersById($orderId);
        $items = ShopHistoryOrdersItemsModel::getInstance()->getHistoryOrdersItemsByHistoryOrderId($orderId);
        foreach ($items as $item) {
            if ($item->getItem()->getType() === ShopItemType::VIRTUAL) {
                $virtualItemVarName = ShopItemsVirtualMethodModel::getInstance()->getVirtualItemMethodByItemId($item->getItem()->getId())->getVirtualMethod()->varName();
                $quantity = $item->getQuantity();
                for ($i = 0; $i < $quantity; $i++) {
                    ShopItemsController::getInstance()->getVirtualItemsMethodsByVarName($virtualItemVarName)->execOnBuy($virtualItemVarName, $item->getItem(), $order->getUser());
                }
            }
        }

        $htmlMessage =<<<HTML
        <p>Le traitement de votre commande est terminé.</p>
        <p>Commande N°<b>%NUMBER%</b></p>
        <p>Retrouvez l'historique de vos commandes en <a href="%LINK%">cliquant ici.</a></p>
        HTML;
        $finalMessage = str_replace(['%NUMBER%', '%LINK%'], [$order->getOrderNumber(), Website::getUrl() . EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'shop/history'], $htmlMessage);
        ShopNotifyController::getInstance()->notifyUser($order->getUser()->getMail(), "Commande terminé", "Commande terminé", $finalMessage);

        try {
            Emitter::send(ShopEndOrderEvent::class, $order);
        } catch (Exception) {
            error_log('Error while sending ShopEndOrderEvent');
        }

        Redirect::redirect('cmw-admin/shop/orders/inProgress');
    }

    #[NoReturn]
    #[Link('/orders/inProgress/manage/cancel/:orderId', Link::POST, [], '/cmw-admin/shop')]
    private function shopManageCancelStep(int $orderId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.order.manage.unrealizable');

        ShopHistoryOrdersModel::getInstance()->toCancelStep($orderId);

        $order = ShopHistoryOrdersModel::getInstance()->getHistoryOrdersById($orderId);
        $htmlMessage =<<<HTML
        <p>La commande N°<b>%NUMBER%</b> est irréalisable.</p>
        <p>Vous recevrez un remboursement ou un avoir dans les prochains jours</p>
        <p>Retrouvez l'historique de vos commandes en <a href="%LINK%">cliquant ici.</a></p>
        HTML;
        $finalMessage = str_replace(['%NUMBER%', '%LINK%'], [$order->getOrderNumber(), Website::getUrl() . EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'shop/history'], $htmlMessage);
        ShopNotifyController::getInstance()->notifyUser($order->getUser()->getMail(), "Commande irréalisable", "Commande irréalisable", $finalMessage);

        try {
            Emitter::send(ShopCanceledOrderEvent::class, $orderId);
        } catch (Exception) {
            error_log('Error while sending ShopCanceledOrderEvent');
        }

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/orders/inProgress/manage/endFailed/:orderId', Link::POST, [], '/cmw-admin/shop')]
    private function shopManageEndFailedStep(int $orderId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.order.manage.endFailed');

        ShopHistoryOrdersModel::getInstance()->refundStep($orderId);

        $order = ShopHistoryOrdersModel::getInstance()->getHistoryOrdersById($orderId);
        $htmlMessage =<<<HTML
        <p>La commande N°<b>%NUMBER%</b> irréalisable est traité.</p>
        <p>Vous avez reçu votre remboursement</p>
        <p>Veuillez nous excusez pour la gêne occasionné.</p>
        <p>Retrouvez l'historique de vos commandes en <a href="%LINK%">cliquant ici.</a></p>
        HTML;
        $finalMessage = str_replace(['%NUMBER%', '%LINK%'], [$order->getOrderNumber(), Website::getUrl() . EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'shop/history'], $htmlMessage);
        ShopNotifyController::getInstance()->notifyUser($order->getUser()->getMail(), "Commande irréalisable traité", "Commande irréalisable traité", $finalMessage);

        try {
            Emitter::send(ShopRefundedSelfOrderEvent::class, $orderId);
        } catch (Exception) {
            error_log('Error while sending ShopRefundedSelfOrderEvent');
        }

        Redirect::redirect('cmw-admin/shop/orders/inProgress');
    }

    #[NoReturn]
    #[Link('/orders/inProgress/manage/refunded/:orderId', Link::POST, [], '/cmw-admin/shop')]
    private function shopManageRefundStep(int $orderId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.order.manage.refund');

        [$name] = Utils::filterInput('name');

        $order = ShopHistoryOrdersModel::getInstance()->getHistoryOrdersById($orderId);
        $orderPrice = $order->getOrderTotal();
        $user = $order->getUser();
        $code = Utils::generateRandomNumber('6') . '_' . $order->getOrderNumber();

        $message = LangManager::translate('shop.views.elements.global.creditLauncher.message-default');
        $value = (LangManager::translate('shop.views.elements.global.creditLauncher.message-value-default')) . ' ' . $order->getOrderTotalFormatted();
        $footer1 = LangManager::translate('shop.views.elements.global.creditLauncher.footer-1-default');
        $shopUrl = Website::getUrl() . EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'shop';
        $footer2 = LangManager::translate('shop.views.elements.global.creditLauncher.footer-2-default') . Website::getWebsiteName();

        $htmlTemplate = <<<HTML
              <p>%MESSAGE%</p>
              <p><strong>%VALUE%</strong></p>
              <div style="text-align: center; font-family: monospace; font-size: 15px; background-color: #f4f4f4; color: #222; padding: 10px; border-radius: 5px; margin: 10px auto; max-width: 90%;">
                  %CODE%
              </div>
              <br>
              <p>%FOOTER_1%<br>
              <a href="%SHOP_URL%">%FOOTER_2%</a></p>
            HTML;

        $body = str_replace(['%MESSAGE%', '%VALUE%', '%CODE%', '%FOOTER_1%', '%SHOP_URL%', '%FOOTER_2%'],
            [$message, $value, $code, $footer1, $shopUrl , $footer2], $htmlTemplate);

        if (MailModel::getInstance()->getConfig() !== null && MailModel::getInstance()->getConfig()->isEnable()) {
            $discount = ShopDiscountModel::getInstance()->createDiscount($name,4,null,null,1,0,null,$orderPrice,0,1,0,$code,0,0,0);
            if ($discount) {
                ShopHistoryOrdersModel::getInstance()->refundStep($orderId);
                ShopNotifyController::getInstance()->notifyUser($user->getMail(), "Votre avoir pour la commande N°" . $order->getOrderNumber(), "Avoir pour la commande N°" . $order->getOrderNumber(), $body);
                Flash::send(Alert::SUCCESS, 'Avoir', $user->getPseudo() . ' à reçu son avoir par mail !');
            }
        } else {
            Flash::send(Alert::ERROR, 'Avoir','Nous n\'avons pas réussi à envoyer le mail au client ! aucun avoir n\'a été créer');
        }

        try {
            Emitter::send(ShopRefundedCreditOrderEvent::class, $orderId);
        } catch (Exception) {
            error_log('Error while sending ShopRefundedCreditOrderEvent');
        }

        Redirect::redirect('cmw-admin/shop/orders/inProgress');
    }

    public function handleCreateOrder(UserEntity $user): void
    {
        $sessionId = session_id();

        $commandTunnel = ShopCommandTunnelModel::getInstance()->getShopCommandTunnelByUserId($user->getId());
        $order = ShopHistoryOrdersModel::getInstance()->createHistoryOrder($user->getId(), 0);
        $discountModel = ShopDiscountModel::getInstance();

        $paymentMethod = ShopPaymentsController::getInstance()->getPaymentByVarName($commandTunnel->getPaymentName());
        $paymentHistory = ShopHistoryOrdersPaymentModel::getInstance()->addHistoryPaymentOrder($order->getId(), $paymentMethod->name(), $paymentMethod->varName(), $paymentMethod->fees());

        if (!is_null($commandTunnel->getShipping())) {
            $shippingHistory = ShopHistoryOrdersShippingModel::getInstance()->addHistoryShippingOrder($order->getId(), $commandTunnel->getShipping()->getId(), $commandTunnel->getShipping()->getName(), $commandTunnel->getShipping()->getPrice());
        }

        $userAddress = null;
        if (!is_null($commandTunnel->getShopDeliveryUserAddress())) {
            $userAddress = ShopDeliveryUserAddressModel::getInstance()->getShopDeliveryUserAddressById(
                $commandTunnel->getShopDeliveryUserAddress()->getId()
            );

            ShopHistoryOrdersUserAddressModel::getInstance()->addHistoryUserAddressOrder(
                $order->getId(),
                $userAddress->getLabel(),
                $user->getMail(),
                $userAddress->getLastName(),
                $userAddress->getFirstName(),
                $userAddress->getLine1(),
                $userAddress->getLine2(),
                $userAddress->getCity(),
                $userAddress->getPostalCode(),
                $userAddress->getCountry(),
                $userAddress->getPhone()
            );
        }

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
            if ($cartItem->getItem()?->getCurrentStock()) {
                $nextStock = $cartItem->getItem()?->getCurrentStock() - $cartItem->getQuantity();
                ShopItemsModel::getInstance()->decreaseStock($cartItem->getItem()?->getId(), $nextStock);

                $percentage = ($nextStock / $cartItem->getItem()->getDefaultStock()) * 100;
                $stockAlert = ShopSettingsModel::getInstance()->getSettingValue('stockAlert');

                if ($percentage <= $stockAlert) {
                    NotificationManager::notify('Stock faible', 'Les stock pour ' . $cartItem->getItem()->getName() . ' sont faible ! (' . $nextStock . '/' . $cartItem->getItem()->getDefaultStock() . ')');
                }
            }
        }

        NotificationManager::notify('Nouvelle commande', $user->getPseudo() . ' viens de passer une commande.', 'shop/orders');

        $varName = 'invoice';
        if (ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_use', $varName) === "1") {
            $invoiceLink = $this->createPDF($cartContent, $order, $paymentHistory);
        }

        $this->notifyUser($cartContent, $user, $order, $paymentHistory, $invoiceLink ?? null);

        try {
            Emitter::send(ShopNewOrderEvent::class, $order);
        } catch (Exception) {
            error_log('Error while sending ShopNewOrderEvent');
        }

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

            if ($cartDiscount->getDiscount()->getLinked() === 3 || $cartDiscount->getDiscount()->getLinked() === 4) {
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
            if ($item->getItem()->getType() === ShopItemType::PHYSICAL) {
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
            if ( $item->getItem()->getType() === ShopItemType::PHYSICAL) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param ShopCartItemEntity[] $cartContent
     */
    private function notifyUser(array $cartContent, UserEntity $user, ShopHistoryOrdersEntity $order, ShopHistoryOrdersPaymentEntity $paymentHistory, ?string $invoiceLink): void
    {
        $orderNumber = $order->getOrderNumber();
        $orderDate = $order->getCreated();
        $paymentMethod = $paymentHistory->getName();
        $historyLink = Website::getUrl() . 'shop/history';

        $priceType = '';
        $itemsHtml = '';
        $itemsHtml .= '<table cellpadding="6" cellspacing="0" width="100%">';
        $itemsHtml .= '<thead>
                            <tr>
                                <th></th>
                                <th>Article</th>
                                <th>Quantité</th>
                                <th>Prix</th>
                            </tr>
                        </thead>
                        <tbody>';

        foreach ($cartContent as $cartItem) {
            $priceType = $cartItem->getItem()->getPriceType();
            $itemName = $cartItem->getItem()->getName();
            if ($cartItem->getFirstImageItemUrl() !== '/Public/Uploads/Shop/0') {
                $itemImage = Website::getUrl() . $cartItem->getFirstImageItemUrl();
            } else {
                $itemImage = Website::getUrl() . ShopImagesModel::getInstance()->getDefaultImg();
            }
            $itemQuantity = $cartItem->getQuantity();
            $itemPrice = $cartItem->getItemTotalPriceAfterDiscountFormatted();

            $itemsHtml .= <<<HTML
            <tr>
                <td><img src="$itemImage" width="40px"></td>
                <td><b>$itemName</b></td>
                <td>$itemQuantity</td>
                <td>$itemPrice</td>
            </tr>
        HTML;
        }

        $itemsHtml .= '</tbody></table>';


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
                    <div>
                        <span>Numéro de commande :</span> <b>N°%ORDER%</b>
                    </div>
                    <div>
                        <span>Date de la commande :</span> <b>%DATE%</b>
                    </div>
                    <div style="margin-top: 4px; margin-bottom: 4px">
                        $itemsHtml
                    </div>
                    <div>
                        <span>Total :</span> <b>%TOTAL%</b>
                        <br>
                        <span>Méthode de paiement :</span> %PAYMENT_METHOD%
                        <br>
                        <a href="%HISTORY_LINK%">Consultez mes commandes</a>
                        %INVOICE_SECTION%
                    </div>
            HTML;
        $invoiceSection = $invoiceLink !== null ? '<br><a href="' . $invoiceLink . '">Télécharger votre facture</a>' : '';
        $body = str_replace(['%ORDER%', '%DATE%', '%TOTAL%', '%PAYMENT_METHOD%', '%HISTORY_LINK%', '%INVOICE_SECTION%'],
            [$orderNumber, $orderDate, $total, $paymentMethod, $historyLink, $invoiceSection], $htmlTemplate);

        ShopNotifyController::getInstance()->notifyUser($order->getUser()->getMail(), "Récapitulatif de Commande", "Récapitulatif de Commande", $body);
    }

    /**
     * @throws \Random\RandomException
     */
    private function createPDF(array $cartContent, ShopHistoryOrdersEntity $order, ShopHistoryOrdersPaymentEntity $paymentHistory) : string
    {
        $paymentMethod = $paymentHistory->getName();
        $priceType = '';
        foreach ($cartContent as $cartItem) {
            $priceType = $cartItem->getItem()->getPriceType();
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

        $invoiceOrder = $this->generatePDF($order, $paymentMethod, $total, $cartContent);
        $invoiceLink = Website::getUrl() . 'shop/orders/download/' . $invoiceOrder;
        ShopHistoryOrdersInvoiceModel::getInstance()->addInvoice($order->getId(), $invoiceLink);

        return $invoiceLink;
    }

    /**
     *
     * @param string $orderNumber
     * @param string $orderDate
     * @param string $paymentMethod
     * @param string $total
     * @param ShopCartItemEntity[] $cartContent
     * @return string le chemin de telechargement
     * @throws \Random\RandomException
     */
    private function generatePDF(ShopHistoryOrdersEntity $order, string $paymentMethod, string $total, array $cartContent): string
    {
        $addressUser = $order->getUserAddressMethod()?->getUserFirstName() . " " . $order->getUserAddressMethod()?->getUserLastName();
        $address = $order->getUserAddressMethod()?->getUserLine1();
        $addressPcCity = $order->getUserAddressMethod()?->getUserPostalCode() . " " . $order->getUserAddressMethod()?->getUserCity();
        $addressCountry = $order->getUserAddressMethod()?->getUserFormattedCountry();
        $orderNumber = $order->getOrderNumber();
        $orderDate = $order->getCreated();
        $websiteName = Website::getWebsiteName();
        $paymentFee = $order->getPaymentMethod()?->getFeeFormatted() ?? 'N/A';
        $shippingFee = $order->getShippingMethod()?->getPriceFormatted() ?? 'N/A';
        $shippingName = $order->getShippingMethod()?->getName() ?? 'N/A';
        $cartTotalDiscount = $order->getAppliedCartDiscountTotalPriceFormatted() ? '- ' . $order->getAppliedCartDiscountTotalPriceFormatted() : '';

        $varName = 'invoice';
        $logo = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_logo', $varName) ?? Website::getUrl()."App/Package/Shop/Views/Settings/Images/default.png";
        $companyAddress = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_address', $varName) ?? "N/A";
        $companyAddressPC = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_address_pc', $varName) ?? "N/A";
        $companyAddressCity = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_address_city', $varName) ?? "N/A";
        $companyAddressCountry = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_address_country', $varName) ?? "N/A";
        $footerText = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_footer_text', $varName) ?? LangManager::translate('shop.views.elements.global.invoice.footer-default');

        $itemsHtml = '';
        foreach ($cartContent as $cartItem) {
            $itemName = htmlspecialchars($cartItem->getItem()->getName());
            $itemQuantity = $cartItem->getQuantity();
            $itemPrice = $cartItem->getItem()?->getPriceFormatted();
            $itemDiscount = $cartItem->getDiscountFormatted() ?? '';
            $itemSubtotal = $cartItem->getItemTotalPriceAfterDiscountFormatted() ?? $cartItem->getItemTotalPriceFormatted();

            $itemsHtml .= "<tr>
            <td>$itemName</td>
            <td>$itemQuantity</td>
            <td>$itemPrice</td>
            <td>$itemDiscount</td>
            <td>$itemSubtotal</td>
        </tr>";
        }

        $pdfDir = EnvManager::getInstance()->getValue('DIR') . 'Public/Uploads/Shop/Invoices';
        if (!file_exists($pdfDir) && !mkdir($pdfDir, 0777, true) && !is_dir($pdfDir)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $pdfDir));
        }

        $randomNumber = $this->generateRandomString();

        $pdfPath = $pdfDir . "/facture_" . $randomNumber . "_" . $orderNumber . ".pdf";

        $pdf = new TCPDF();
        $pdf->SetCreator($websiteName);
        $pdf->SetAuthor($websiteName);
        $pdf->SetTitle( LangManager::translate('shop.views.elements.global.invoice.name') . " $orderNumber");
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->AddPage();

        $html = '
<style>
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .section {
            margin-top: 20px;
        }

        .section h3 {
            margin: 0 0 10px;
            font-size: 1.1em;
        }

        .info-table,
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 5px;
        }

        .info-table td:nth-child(2) {
            text-align: right;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .items-table th {
            background-color: #f9f9f9;
        }

        .totals {
            margin-top: 20px;
            float: right;
            text-align: right;
        }

        .totals td {
            padding: 5px 0;
        }

        .footer {
            margin-top: 40px;
            font-size: 0.9em;
            color: #666;
        }
    </style>
        <div class="facture">
        <table class="info-table">
            <tr>
                <td>
                    <img class="logo" src="'.$logo.'" width="120px" alt="Logo">
                    <br>
                    <span><b>'.$websiteName. '</b><br>
                    '.$companyAddress.'<br>
                    '.$companyAddressPC.' '.$companyAddressCity.'<br>
                    '.$companyAddressCountry.'<br>
                    </span>
                </td>
                <td align="right">
                    <h2 style="color: #c16374">'. LangManager::translate('shop.views.elements.global.invoice.title') .' N° ' .$orderNumber.'</h2>
                    <div class="section">
                        <h5>'. LangManager::translate('shop.views.elements.global.invoice.address-invoice') .'</h5>
                        <p>
                            '.$addressUser.'<br>
                            '.$address.'<br>
                            '.$addressPcCity.'<br>
                            '.$addressCountry.'
                        </p>
                    </div>
                </td>
            </tr>
        </table>
            

            <div class="section">
                <table class="info-table">
                <thead>
                        <tr>
                            <th>'. LangManager::translate('shop.views.elements.global.invoice.shipping-invoice') .'</th>
                            <th align="center">'. LangManager::translate('shop.views.elements.global.invoice.date-invoice') .'</th>
                            <th align="right">'. LangManager::translate('shop.views.elements.global.invoice.payment-invoice') .'</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>'.$shippingName.'</td>
                            <td align="center">'.$orderDate.'</td>
                            <td align="right">'.$paymentMethod.'</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="section">
                <table class="items-table">
                    <thead>
                        <tr>
                            <th><strong>'. LangManager::translate('shop.views.elements.global.invoice.item-invoice') .'</strong></th>
                            <th><strong>'. LangManager::translate('shop.views.elements.global.invoice.quantity-invoice') .'</strong></th>
                            <th><strong>'. LangManager::translate('shop.views.elements.global.invoice.unit-price-invoice') .'</strong></th>
                            <th><strong>'. LangManager::translate('shop.views.elements.global.invoice.item-discount-invoice') .'</strong></th>
                            <th><strong>'. LangManager::translate('shop.views.elements.global.invoice.st-invoice') .'</strong></th>
                        </tr>
                    </thead>
                    <tbody>
                        '.$itemsHtml.'
                    </tbody>
                </table>
            </div>

            <div class="totals">
                <table>
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>'. LangManager::translate('shop.views.elements.global.invoice.shipping-cost-invoice') .'</td>
                            <td>'.$shippingFee.'</td>
                        </tr>
                        <tr>
                            <td>'. LangManager::translate('shop.views.elements.global.invoice.payment-fee-invoice') .'</td>
                            <td>'.$paymentFee.'</td>
                        </tr>
                        <tr>
                            <td>'. LangManager::translate('shop.views.elements.global.invoice.total-discount-invoice') .'</td>
                            <td>'.$cartTotalDiscount.'</td>
                        </tr>
                        <tr>
                            <td><strong>'. LangManager::translate('shop.views.elements.global.invoice.total-invoice') .'</strong></td>
                            <td><strong>'.$total.'</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="footer">
                <p>'.$footerText.'</p>
            </div>
        </div>';

        $pdf->writeHTML($html, true, false, true, false, '');

        $pdf->Output($pdfPath, 'F');

        return "facture_" . $randomNumber . "_" . $orderNumber;
    }


    /**
     * @throws \Random\RandomException
     */
    function generateRandomString($minLength = 20, $maxLength = 30): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $length = random_int($minLength, $maxLength); // Longueur aléatoire entre $minLength et $maxLength
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $randomString;
    }

}
