<?php

namespace CMW\Controller\Shop\Admin\Statistics;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Shop\Statistics\ShopStatisticsModel;

/**
 * Class: @ShopStatisticsController
 * @package shop
 * @author Zomb
 * @version 1.0
 */
class ShopStatisticsController extends AbstractController
{
    #[Link('/statistics', Link::GET, [], '/cmw-admin/shop')]
    public function shopStatistics(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.statistics');

        $numberOrderThisMonth = ShopStatisticsModel::getInstance()->countTotalOrdersThisMonth();
        $refundedOrderThisMonth = ShopStatisticsModel::getInstance()->countOrdersStatusThisMonth(-2);
        $canceledOrderThisMonth = ShopStatisticsModel::getInstance()->countOrdersStatusThisMonth(-1);
        $newOrderThisMonth = ShopStatisticsModel::getInstance()->countOrdersStatusThisMonth(0);
        $waitShippingOrderThisMonth = ShopStatisticsModel::getInstance()->countOrdersStatusThisMonth(1);
        $shippingOrderThisMonth = ShopStatisticsModel::getInstance()->countOrdersStatusThisMonth(2);
        $finishedOrderThisMonth = ShopStatisticsModel::getInstance()->countOrdersStatusThisMonth(3);

        // todo: total canceled refunded ....

        View::createAdminView('Shop', 'Statistics/main')
            ->addVariableList(['numberOrderThisMonth' => $numberOrderThisMonth, 'refundedOrderThisMonth' => $refundedOrderThisMonth])
            ->view();
    }
}
