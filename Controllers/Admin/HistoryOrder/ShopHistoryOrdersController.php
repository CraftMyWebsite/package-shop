<?php

namespace CMW\Controller\Shop\Admin\HistoryOrder;

use CMW\Controller\Shop\Admin\Item\ShopItemsController;
use CMW\Controller\Shop\Admin\Payment\ShopPaymentsController;
use CMW\Controller\Shop\Admin\Shipping\ShopShippingController;
use CMW\Controller\Users\UsersController;
use CMW\Entity\Shop\Carts\ShopCartItemEntity;
use CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity;
use CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersItemsEntity;
use CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersPaymentEntity;
use CMW\Entity\Users\UserEntity;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
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
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.order');

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

    #[NoReturn]
    #[Link('/orders/view/:orderId/reviewReminder/:itemId/:userId', Link::GET, [], '/cmw-admin/shop')]
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

        $varName = 'review_reminder';
        $websiteName = Website::getWebsiteName();
        $object = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_global') ?? $websiteName . ' - Votre avis nous intéresse !';
        $titre = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_title_mail') ?? 'Votre avis nous intéresse !';
        $message = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_text_mail') ?? "Vous avez récemment commander un article sur notre boutique.";
        $footer1 = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_footer_1_mail') ?? 'Nous aimerions savoir ce que vous pensez de cet article';
        $footer2 = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_footer_2_mail') ?? "Rendez-vous sur la boutique pour partager votre avis";

        $htmlTemplate = <<<HTML
            <html>
            <head>
            <style>
              .gift-card {
                font-family: Arial, sans-serif;
                max-width: 600px;
                margin: 20px auto;
                padding: 20px;
                background-color: %CARDBG%;
                border: 1px solid #ddd;
                border-radius: 10px;
                text-align: center;
              }

              .gift-card h1 {
                color: %TITLECOLOR%;
              }

              .gift-card p {
                color: %TEXTCOLOR%;
              }

              .code {
                font-size: 18px;
                color: %CODETEXT%;
                margin: 20px 0;
                padding: 10px;
                background-color: %CODEBG%;
                border-radius: 5px;
                display: inline-block;
              }
            </style>
            </head>
            <body style="background-color: %MAINBG%">

            <div class="gift-card">
              <h1>%TITRE%</h1>
              <p>%MESSAGE%</p>
              <div class="code"><a href="%ITEM_URL%">%ITEM_NAME%</a></div><br>
              <p>%FOOTER_1%<br>
              <a href="%ITEM_URL%">%FOOTER_2%</a></p>
            </div>
            </body>
            </html>
            HTML;

        $cardBG = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_card_color') ?? '#f8f9fa';
        $titleColor = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_color_title') ?? '#2f2f2f';
        $textColor = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_color_p') ?? '#656565';
        $codeText = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_code_color') ?? '#007bff';
        $codeBG = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_code_bg_color') ?? '#e9ecef';
        $mainBG = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_body_color') ?? '#ffffff';

        $body = str_replace(['%TITRE%', '%MESSAGE%',  '%ITEM_URL%', '%ITEM_NAME%', '%FOOTER_1%', '%FOOTER_2%',
            '%MAINBG%', '%CODEBG%', '%CODETEXT%', '%TEXTCOLOR%', '%TITLECOLOR%', '%CARDBG%'],
            [$titre, $message, $url, $item->getName(), $footer1, $footer2, $mainBG, $codeBG, $codeText, $textColor, $titleColor, $cardBG], $htmlTemplate);
        if (MailModel::getInstance()->getConfig() !== null && MailModel::getInstance()->getConfig()->isEnable()) {
            MailManager::getInstance()->sendMail($user->getMail(), $object, $body);
            Flash::send(Alert::SUCCESS, 'Relance avis', $user->getPseudo() . ' à reçu une relance par mail !');
        } else {
            Flash::send(Alert::ERROR, 'Relance avis','Nous n\'avons pas réussi à envoyer le mail au client !');
        }
        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/orders/manage/send/:orderId', Link::POST, [], '/cmw-admin/shop')]
    private function shopManageSendStep(int $orderId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.order.manage.ready');
        // Exec shipping method :
        $thisOrder = ShopHistoryOrdersModel::getInstance()->getHistoryOrdersById($orderId);

        $orderOnlyVirtual = $this->handleOrderTypeContent($thisOrder->getOrderedItems());
        if (!$orderOnlyVirtual) {
            $shippingMethodVarName = $thisOrder->getShippingMethod()->getShipping()->getShippingMethod()->varName();
            $items = $thisOrder->getOrderedItems();
            $userEntity = $thisOrder->getUser();
            ShopShippingController::getInstance()->getShippingMethodsByVarName($shippingMethodVarName)->execAfterCommandValidatedByAdmin($shippingMethodVarName,$items,$userEntity, $thisOrder);
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


        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/orders/manage/finish/:orderId', Link::POST, [], '/cmw-admin/shop')]
    private function shopManageFinalStep(int $orderId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.order.manage.shipping');

        [$shippingLink] = Utils::filterInput('shipping_link');
        ShopHistoryOrdersModel::getInstance()->toFinalStep($orderId, ($shippingLink === '' ? null : $shippingLink));

        // TODO : Notifier l'utilisateur

        try {
            Emitter::send(ShopFinishedOrderEvent::class, $orderId);
        } catch (Exception) {
            error_log('Error while sending ShopSendOrderEvent');
        }

        Redirect::redirect('cmw-admin/shop/orders');
    }

    #[NoReturn]
    #[Link('/orders/manage/end/:orderId', Link::POST, [], '/cmw-admin/shop')]
    private function shopManageEndStep(int $orderId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.order.manage.endSuccess');

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
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.order.manage.unrealizable');

        ShopHistoryOrdersModel::getInstance()->toCancelStep($orderId);

        // TODO : Notifier l'utilisateur

        // Eitter canceled

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/orders/manage/endFailed/:orderId', Link::POST, [], '/cmw-admin/shop')]
    private function shopManageEndFailedStep(int $orderId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.order.manage.endFailed');

        ShopHistoryOrdersModel::getInstance()->refundStep($orderId);

        try {
            Emitter::send(ShopRefundedSelfOrderEvent::class, $orderId);
        } catch (Exception) {
            error_log('Error while sending ShopRefundedSelfOrderEvent');
        }

        Redirect::redirect('cmw-admin/shop/orders');
    }

    #[NoReturn]
    #[Link('/orders/manage/refunded/:orderId', Link::POST, [], '/cmw-admin/shop')]
    private function shopManageRefundStep(int $orderId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.order.manage.refund');

        [$name] = Utils::filterInput('name');

        $varName = 'credit_launcher';

        $order = ShopHistoryOrdersModel::getInstance()->getHistoryOrdersById($orderId);
        $orderPrice = $order->getOrderTotal();
        $user = $order->getUser();
        $object = (ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_global') ?? Website::getWebsiteName().' - Votre avoir pour la commande') . ' ' . $order->getOrderNumber();
        $code = Utils::generateRandomNumber('6') . '_' . $order->getOrderNumber();

        $titre = (ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_title_mail') ?? 'Avoir pour') . ' ' . $order->getOrderNumber();
        $message = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_text_mail') ?? "Vous venez de recevoir un avoir suite à l'annulation d'une commande non réalisable";
        $value = (ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_text_mail_value') ?? "Ce code à une valeur total de") . ' ' . $order->getOrderTotalFormatted();
        $footer1 = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_footer_1_mail') ?? 'Vous pouvez utiliser cet avoir sur toute la boutique !';
        $shopUrl = Website::getUrl() . EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'shop';
        $footer2 = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_footer_2_mail') ?? "Rendez-vous sur la boutique " . Website::getWebsiteName();

        $htmlTemplate = <<<HTML
            <html>
            <head>
            <style>
              .gift-card {
                font-family: Arial, sans-serif;
                max-width: 600px;
                margin: 20px auto;
                padding: 20px;
                background-color: %CARDBG%;
                border: 1px solid #ddd;
                border-radius: 10px;
                text-align: center;
              }

              .gift-card h1 {
                color: %TITLECOLOR%;
              }

              .gift-card p {
                color: %TEXTCOLOR%;
              }

              .code {
                font-size: 18px;
                color: %CODETEXT%;
                margin: 20px 0;
                padding: 10px;
                background-color: %CODEBG%;
                border-radius: 5px;
                display: inline-block;
              }
            </style>
            </head>
            <body style="background-color: %MAINBG%">

            <div class="gift-card">
              <h1>%TITRE%</h1>
              <p>%MESSAGE%</p>
              <p><strong>%VALUE%</strong></p>
              <div class="code">%CODE%</div><br>
              <p>%FOOTER_1%<br>
              <a href="%SHOP_URL%">%FOOTER_2%</a></p>
            </div>
            </body>
            </html>
            HTML;

        $cardBG = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_card_color') ?? '#f8f9fa';
        $titleColor = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_color_title') ?? '#2f2f2f';
        $textColor = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_color_p') ?? '#656565';
        $codeText = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_code_color') ?? '#007bff';
        $codeBG = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_code_bg_color') ?? '#e9ecef';
        $mainBG = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_body_color') ?? '#ffffff';

        $body = str_replace(['%TITRE%', '%MESSAGE%', '%VALUE%', '%CODE%', '%FOOTER_1%', '%SHOP_URL%', '%FOOTER_2%',
            '%MAINBG%', '%CODEBG%', '%CODETEXT%', '%TEXTCOLOR%', '%TITLECOLOR%', '%CARDBG%'],
            [$titre, $message, $value, $code, $footer1, $shopUrl , $footer2, $mainBG, $codeBG, $codeText, $textColor, $titleColor, $cardBG], $htmlTemplate);


        if (MailModel::getInstance()->getConfig() !== null && MailModel::getInstance()->getConfig()->isEnable()) {
            $discount = ShopDiscountModel::getInstance()->createDiscount($name,4,null,null,1,0,null,$orderPrice,0,1,0,$code,0,0,0);
            if ($discount) {
                ShopHistoryOrdersModel::getInstance()->refundStep($orderId);
                MailManager::getInstance()->sendMail($user->getMail(), $object, $body);
                Flash::send(Alert::SUCCESS, 'Avoir', $user->getPseudo() . ' à reçu son avoir par mail !');
            }
        } else {
            Flash::send(Alert::ERROR, 'Avoir','Nous n\'avons pas réussi à envoyer le mail au client ! aucun avoir n\'a été créer');
        }

        // Emitter refunded

        Redirect::redirect('cmw-admin/shop/orders');
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
            if ( $item->getItem()->getType() === 0) {
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

        if (MailModel::getInstance()->getConfig() !== null && MailModel::getInstance()->getConfig()->isEnable()) {
            MailManager::getInstance()->sendMail($user->getMail(), $object, $body);
        } else {
            Flash::send(Alert::WARNING, 'Commande','Nous n\'avons pas réussi à vous envoyer le mail de recap !');
        }
    }
}
