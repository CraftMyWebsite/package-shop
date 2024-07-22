<?php

namespace CMW\Model\Shop\HistoryOrder;

use CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersAfterSalesMessagesEntity;
use CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersDiscountEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Model\Users\UsersModel;

/**
 * Class: @ShopHistoryOrdersAfterSalesMessagesModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopHistoryOrdersAfterSalesMessagesModel extends AbstractModel
{
    /**
     * @param int $id
     * @return ShopHistoryOrdersAfterSalesMessagesEntity|null
     */
    public function getHistoryOrdersAfterSalesMessageById(int $id): ?ShopHistoryOrdersAfterSalesMessagesEntity
    {
        $sql = "SELECT * FROM cmw_shop_history_order_afterSales_message WHERE shop_history_order_afterSales_message_id = :shop_history_order_afterSales_message_id";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("shop_history_order_afterSales_message_id" => $id))) {
            return null;
        }

        $res = $res->fetch();

        if (empty($res)) {
            return null;
        }

        $afterSales = is_null($res["shop_history_order_afterSales_id"]) ? null : ShopHistoryOrdersAfterSalesModel::getInstance()->getHistoryOrdersAfterSalesById($res["shop_history_order_afterSales_id"]);
        $user = is_null($res["shop_history_order_afterSales_message_author"]) ? null : UsersModel::getInstance()->getUserById($res["shop_history_order_afterSales_message_author"]);

        return new ShopHistoryOrdersAfterSalesMessagesEntity(
            $res["shop_history_order_afterSales_message_id"],
            $afterSales,
            $res["shop_history_order_afterSales_message"] ?? null,
            $user,
            $res["shop_history_order_afterSales_message_created_at"],
            $res["shop_history_order_afterSales_message_updated_at"]
        );
    }

    /**
     * @return ?ShopHistoryOrdersAfterSalesMessagesEntity []
     */
    public function getHistoryOrdersAfterSalesMessageByAfterSalesId(int $afterSalesId): ?array
    {
        $var = array(
            "shop_history_order_afterSales_id" => $afterSalesId,
        );

        $sql = "SELECT shop_history_order_afterSales_message_id FROM cmw_shop_history_order_afterSales_message WHERE shop_history_order_afterSales_id = :shop_history_order_afterSales_id";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute($var)) {
            return array();
        }

        $toReturn = array();

        while ($orderItem = $res->fetch()) {
            $toReturn[] = $this->getHistoryOrdersAfterSalesMessageById($orderItem["shop_history_order_afterSales_message_id"]);
        }

        return $toReturn;

    }

    public function addResponse(int $afterSalesId, string $message, int $author): ?ShopHistoryOrdersAfterSalesMessagesEntity
    {
        $var = array(
            "shop_history_order_afterSales_id" => $afterSalesId,
            "shop_history_order_afterSales_message" => $message,
            "shop_history_order_afterSales_message_author" => $author
        );

        $sql = "INSERT INTO cmw_shop_history_order_afterSales_message (shop_history_order_afterSales_id, shop_history_order_afterSales_message, shop_history_order_afterSales_message_author) VALUES (:shop_history_order_afterSales_id, :shop_history_order_afterSales_message, :shop_history_order_afterSales_message_author)";

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            $id = $db->lastInsertId();
            return $this->getHistoryOrdersAfterSalesMessageById($id);
        }

        return null;
    }
}