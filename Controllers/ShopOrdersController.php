<?php

namespace CMW\Controller\Shop;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Shop\ShopOrdersItemsModel;
use CMW\Model\Shop\ShopOrdersModel;


/**
 * Class: @ShopOrdersController
 * @package shop
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ShopOrdersController extends AbstractController
{
    #[Link("/orders", Link::GET, [], "/cmw-admin/shop")]
    public function shopDiscounts(): void
    {
        $inProgressOrders = ShopOrdersModel::getInstance()->getInProgressOrders();
        $errorOrders = ShopOrdersModel::getInstance()->getErrorOrders();
        $finishedOrders = ShopOrdersModel::getInstance()->getFinishedOrders();
        $orderItemsModel = ShopOrdersItemsModel::getInstance();

        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.carts");
        View::createAdminView('Shop', 'orders')
            ->addStyle("Admin/Resources/Vendors/Simple-datatables/style.css","Admin/Resources/Assets/Css/Pages/simple-datatables.css")
            ->addScriptAfter("Admin/Resources/Vendors/Simple-datatables/Umd/simple-datatables.js",
                "Admin/Resources/Assets/Js/Pages/simple-datatables.js")
            ->addVariableList(["inProgressOrders" => $inProgressOrders,"errorOrders" => $errorOrders,"finishedOrders" => $finishedOrders, "orderItemsModel" => $orderItemsModel])
            ->view();
    }
}
