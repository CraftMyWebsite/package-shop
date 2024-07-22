<?php

namespace CMW\Model\Shop\HistoryOrder;

use CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersAfterSalesEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Model\Users\UsersModel;

/**
 * Class: @ShopHistoryOrdersAfterSalesModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopHistoryOrdersAfterSalesModel extends AbstractModel
{
    /**
     * @param int $id
     * @return ShopHistoryOrdersAfterSalesEntity|null
     */
    public function getHistoryOrdersAfterSalesById(int $id): ?ShopHistoryOrdersAfterSalesEntity
    {
        $sql = "SELECT * FROM cmw_shop_history_order_afterSales WHERE shop_history_order_afterSales_id = :shop_history_order_afterSales_id";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("shop_history_order_afterSales_id" => $id))) {
            return null;
        }

        $res = $res->fetch();

        if (empty($res)) {
            return null;
        }

        $historyOrder = is_null($res["shop_history_order_id"]) ? null : ShopHistoryOrdersModel::getInstance()->getHistoryOrdersById($res["shop_history_order_id"]) ?? null;
        $user = is_null($res["shop_history_order_afterSales_author"]) ? null : UsersModel::getInstance()->getUserById($res["shop_history_order_afterSales_author"]);

        return new ShopHistoryOrdersAfterSalesEntity(
            $res["shop_history_order_afterSales_id"],
            $user,
            $res["shop_history_order_afterSales_reason"] ?? null,
            $res["shop_history_order_afterSales_status"] ?? null,
            $historyOrder,
            $res["shop_history_order_afterSales_created_at"] ?? null,
            $res["shop_history_order_afterSales_updated_at"] ?? null,
        );
    }

    /**
     * @return ?ShopHistoryOrdersAfterSalesEntity []
     */
    public function getHistoryOrdersAfterSales(): ?array
    {
        $sql = "SELECT shop_history_order_afterSales_id FROM cmw_shop_history_order_afterSales";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($orderItem = $res->fetch()) {
            $toReturn[] = $this->getHistoryOrdersAfterSalesById($orderItem["shop_history_order_afterSales_id"]);
        }

        return $toReturn;

    }

    /**
     * @return ?ShopHistoryOrdersAfterSalesEntity []
     */
    public function getHistoryOrdersAfterSalesByOrderId(int $orderId): ?array
    {
        $sql = "SELECT shop_history_order_afterSales_id FROM cmw_shop_history_order_afterSales WHERE shop_history_order_id = :shop_history_order_id";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("shop_history_order_id" => $orderId))) {
            return array();
        }

        $toReturn = array();

        while ($orderItem = $res->fetch()) {
            $toReturn[] = $this->getHistoryOrdersAfterSalesById($orderItem["shop_history_order_afterSales_id"]);
        }

        return $toReturn;

    }

    public function changeStatus(int $id, int $status): void
    {
        $data = array(
            "shop_history_order_afterSales_status" => $status,
            "shop_history_order_afterSales_id" => $id,
        );

        $sql = "UPDATE cmw_shop_history_order_afterSales SET shop_history_order_afterSales_status = :shop_history_order_afterSales_status WHERE shop_history_order_afterSales_id = :shop_history_order_afterSales_id";

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        $req->execute($data);
    }
}