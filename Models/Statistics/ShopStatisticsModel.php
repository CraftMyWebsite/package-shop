<?php

namespace CMW\Model\Shop\Statistics;

use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;

/**
 * Class @ShopStatisticsModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopStatisticsModel extends AbstractModel
{
    public function countTotalOrdersThisMonth(): int
    {
        $sql = 'SELECT COUNT(*) AS total_orders
            FROM cmw_shop_history_order
            WHERE MONTH(shop_history_order_created_at) = MONTH(CURRENT_DATE())
            AND YEAR(shop_history_order_created_at) = YEAR(CURRENT_DATE());';

        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute()) {
            return 0;
        }

        $res = $req->fetch();

        if (!$res) {
            return 0;
        }

        return $res['total_orders'] ?? 0;
    }

    public function countOrdersStatusThisMonth(int $status): int
    {
        $sql = 'SELECT COUNT(*) AS total_orders_status
            FROM cmw_shop_history_order
            WHERE shop_history_order_status = :status
            AND MONTH(shop_history_order_created_at) = MONTH(CURRENT_DATE())
            AND YEAR(shop_history_order_created_at) = YEAR(CURRENT_DATE());';

        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute(array('status' => $status))) {
            return 0;
        }

        $res = $req->fetch();

        if (!$res) {
            return 0;
        }

        return $res['total_orders_status'] ?? 0;
    }
}
