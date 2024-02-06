<?php

namespace CMW\Controller\Shop\Admin\Order;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Requests\Request;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Shop\Image\ShopImagesModel;
use CMW\Model\Shop\Order\ShopOrdersItemsModel;
use CMW\Model\Shop\Order\ShopOrdersItemsVariantesModel;
use CMW\Model\Shop\Order\ShopOrdersModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use JetBrains\PhpStorm\NoReturn;


/**
 * Class: @ShopOrdersController
 * @package shop
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ShopOrdersController extends AbstractController
{
    #[Link("/orders", Link::GET, [], "/cmw-admin/shop")]
    public function shopOrders(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.orders");

        $inProgressOrders = ShopOrdersModel::getInstance()->getInProgressOrders();
        $errorOrders = ShopOrdersModel::getInstance()->getErrorOrders();
        $finishedOrders = ShopOrdersModel::getInstance()->getFinishedOrders();
        $orderItemsModel = ShopOrdersItemsModel::getInstance();

        View::createAdminView('Shop', 'Orders/orders')
            ->addStyle("Admin/Resources/Vendors/Simple-datatables/style.css","Admin/Resources/Assets/Css/Pages/simple-datatables.css")
            ->addScriptAfter("Admin/Resources/Vendors/Simple-datatables/Umd/simple-datatables.js",
                "Admin/Resources/Assets/Js/Pages/simple-datatables.js")
            ->addVariableList(["inProgressOrders" => $inProgressOrders,"errorOrders" => $errorOrders,"finishedOrders" => $finishedOrders, "orderItemsModel" => $orderItemsModel])
            ->view();
    }

    #[Link("/orders/manage/:orderId", Link::GET, [], "/cmw-admin/shop")]
    public function shopManageOrders(Request $request, int $orderId): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.orders.manage");

        $order = ShopOrdersModel::getInstance()->getOrdersById($orderId);
        $orderItems = ShopOrdersItemsModel::getInstance()->getOrdersItemsByOrderId($orderId);
        $itemsVariantes = ShopOrdersItemsVariantesModel::getInstance();
        $defaultImage = ShopImagesModel::getInstance()->getDefaultImg();

        $orderStatus = $order->getStatusCode();

        if ($orderStatus == 0) {
            View::createAdminView('Shop', 'Orders/Manage/new')
                ->addVariableList(["order" => $order,"orderItems" => $orderItems,"itemsVariantes" => $itemsVariantes,"defaultImage" => $defaultImage])
                ->view();
        }
        if ($orderStatus == 1) {
            View::createAdminView('Shop', 'Orders/Manage/send')
                ->addVariableList(["order" => $order,"orderItems" => $orderItems,"itemsVariantes" => $itemsVariantes,"defaultImage" => $defaultImage])
                ->view();
        }
        if ($orderStatus == 2) {
            View::createAdminView('Shop', 'Orders/Manage/finish')
                ->addVariableList(["order" => $order,"orderItems" => $orderItems,"itemsVariantes" => $itemsVariantes,"defaultImage" => $defaultImage])
                ->view();
        }
        if ($orderStatus == -1) {
            View::createAdminView('Shop', 'Orders/Manage/cancel')
                ->addVariableList(["order" => $order,"orderItems" => $orderItems,"itemsVariantes" => $itemsVariantes,"defaultImage" => $defaultImage])
                ->view();
        }
    }

    #[NoReturn] #[Link("/orders/manage/send/:orderId", Link::POST, [], "/cmw-admin/shop")]
    public function shopManageSendStep(Request $request, int $orderId): void
    {
        ShopOrdersModel::getInstance()->toSendStep($orderId);

        //TODO : Notifier l'utilisateur

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link("/orders/manage/finish/:orderId", Link::POST, [], "/cmw-admin/shop")]
    public function shopManageFinalStep(Request $request, int $orderId): void
    {
        [$shippingLink] = Utils::filterInput("shipping_link");
        ShopOrdersModel::getInstance()->toFinalStep($orderId, ($shippingLink === "" ? null : $shippingLink) );

        //TODO : Notifier l'utilisateur

        Redirect::redirect("cmw-admin/shop/orders");
    }

    #[NoReturn] #[Link("/orders/manage/end/:orderId", Link::POST, [], "/cmw-admin/shop")]
    public function shopManageEndStep(Request $request, int $orderId): void
    {
        ShopOrdersModel::getInstance()->endOrder($orderId);

        //TODO : Notifier l'utilisateur

        Redirect::redirect("cmw-admin/shop/orders");
    }

    #[NoReturn] #[Link("/orders/manage/cancel/:orderId", Link::POST, [], "/cmw-admin/shop")]
    public function shopManageCancelStep(Request $request, int $orderId): void
    {
        ShopOrdersModel::getInstance()->toCancelStep($orderId);

        //TODO : Notifier l'utilisateur

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link("/orders/manage/refunded/:orderId", Link::POST, [], "/cmw-admin/shop")]
    public function shopManageRefundStep(Request $request, int $orderId): void
    {
        ShopOrdersModel::getInstance()->refundStep($orderId);

        //TODO : Notifier l'utilisateur

        Redirect::redirect("cmw-admin/shop/orders");
    }
}
