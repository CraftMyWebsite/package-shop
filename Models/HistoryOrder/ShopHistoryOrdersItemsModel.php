<?php

namespace CMW\Model\Shop\HistoryOrder;


use CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersItemsEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Model\Shop\Item\ShopItemsModel;

/**
 * Class: @ShopHistoryOrdersItemsModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopHistoryOrdersItemsModel extends AbstractModel
{

    public function getHistoryOrdersItemsById(int $id): ?ShopHistoryOrdersItemsEntity
    {
        $sql = "SELECT * FROM cmw_shop_history_order_items WHERE shop_history_order_items_id = :shop_history_order_items_id";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("shop_history_order_items_id" => $id))) {
            return null;
        }

        $res = $res->fetch();

        $item = is_null($res["item_id"]) ? null : ShopItemsModel::getInstance()?->getShopItemsById($res["item_id"]) ?? null;
        $historyOrder = is_null($res["shop_history_order_id"]) ? null : ShopHistoryOrdersModel::getInstance()->getHistoryOrdersById($res["shop_history_order_id"]) ?? null;

        return new ShopHistoryOrdersItemsEntity(
            $res["shop_history_order_items_id"],
            $item,
            $historyOrder,
            $res["shop_history_order_items_name"] ?? null,
            $res["shop_history_order_items_img"] ?? null,
            $res["shop_history_order_items_quantity"] ?? null,
            $res["shop_history_order_items_price"] ?? null,
            $res["shop_history_order_items_discount_name"] ?? null,
            $res["shop_history_order_items_total_price_before_discount"] ?? null,
            $res["shop_history_order_items_total_price_after_discount"] ?? null
        );
    }

    /**
     * @return ShopHistoryOrdersItemsEntity []
     */
    public function getHistoryOrdersItems(): array
    {

        $sql = "SELECT shop_history_order_items_id FROM cmw_shop_history_order_items ORDER BY shop_history_order_items_id DESC";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($orderItem = $res->fetch()) {
            $toReturn[] = $this->getHistoryOrdersItemsById($orderItem["shop_history_order_items_id"]);
        }

        return $toReturn;

    }

    /**
     * @return ShopHistoryOrdersItemsEntity []
     */
    public function getHistoryOrdersItemsByHistoryOrderId(int $orderId): array
    {
        $var = array(
            "shop_history_order_id" => $orderId,
        );

        $sql = "SELECT shop_history_order_items_id FROM cmw_shop_history_order_items WHERE shop_history_order_id = :shop_history_order_id";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute($var)) {
            return array();
        }

        $toReturn = array();

        while ($orderItem = $res->fetch()) {
            $toReturn[] = $this->getHistoryOrdersItemsById($orderItem["shop_history_order_items_id"]);
        }

        return $toReturn;

    }

    public function createHistoryOrderItems(int $itemId, int $orderId, string $itemName, string $itemFirstImg, int $cartQuantity, float $itemPrice, string $discountName, ?float $totalBeforeDiscount, ?float $totalAfterDiscount): ?ShopHistoryOrdersItemsEntity
    {
        $var = array(
            "item_id" => $itemId,
            "shop_history_order_id" => $orderId,
            "shop_history_order_items_name" => $itemName,
            "shop_history_order_items_img" => $itemFirstImg,
            "shop_history_order_items_quantity" => $cartQuantity,
            "shop_history_order_items_price" => $itemPrice,
            "shop_history_order_items_discount_name" => $discountName,
            "shop_history_order_items_total_price_before_discount" => $totalBeforeDiscount,
            "shop_history_order_items_total_price_after_discount" => $totalAfterDiscount
        );

        $sql = "INSERT INTO cmw_shop_history_order_items (item_id, shop_history_order_id, shop_history_order_items_name, shop_history_order_items_img, shop_history_order_items_quantity,shop_history_order_items_price, shop_history_order_items_discount_name, shop_history_order_items_total_price_before_discount,shop_history_order_items_total_price_after_discount) VALUES (:item_id, :shop_history_order_id, :shop_history_order_items_name, :shop_history_order_items_img, :shop_history_order_items_quantity, :shop_history_order_items_price, :shop_history_order_items_discount_name, :shop_history_order_items_total_price_before_discount, :shop_history_order_items_total_price_after_discount)";

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            $id = $db->lastInsertId();
            return $this->getHistoryOrdersItemsById($id);
        }

        return null;
    }

    public function itemIsOrdered(?int $itemId): bool
    {
        if ($itemId === null) {
            return false;
        }

        $var = ["item_id" => $itemId];

        $sql = "SELECT shop_history_order_items_id FROM `cmw_shop_history_order_items` WHERE item_id = :item_id";

        $db = DatabaseManager::getInstance();
        $res = $db->prepare($sql);

        $res->execute($var);

        return count($res->fetchAll()) === 0;
    }
}