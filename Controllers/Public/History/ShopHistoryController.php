<?php


namespace CMW\Controller\Shop\Public\History;


use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Shop\ShopOrdersItemsModel;
use CMW\Model\Shop\ShopOrdersItemsVariantesModel;
use CMW\Model\Shop\ShopOrdersModel;
use CMW\Model\Users\UsersModel;


/**
 * Class: @ShopHistoryController
 * @package shop
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ShopHistoryController extends AbstractController
{
    #[Link("/history", Link::GET, [], "/shop")]
    public function publicHistoryView(): void
    {
        $userId = UsersModel::getCurrentUser()?->getId();
        $historyOrders = ShopOrdersModel::getInstance()->getOrdersByUserId($userId);
        $OrderItemsModel = ShopOrdersItemsModel::getInstance();
        $variantItemsModel = ShopOrdersItemsVariantesModel::getInstance();

        $view = new View("Shop", "Users/history");
        $view->addVariableList(["historyOrders" => $historyOrders, "OrderItemsModel" => $OrderItemsModel, "variantItemsModel" => $variantItemsModel]);
        $view->view();
    }
}