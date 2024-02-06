<?php

namespace CMW\Model\Shop\Order;

use CMW\Entity\Shop\Orders\ShopOrdersItemsEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Model\Shop\Item\ShopItemsModel;
use CMW\Model\Shop\Payment\ShopPaymentDiscountModel;

/**
 * Class: @ShopOrdersItemsModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopOrdersItemsModel extends AbstractModel
{
    private ShopItemsModel $shopItemsModel;
    private ShopOrdersModel $shopOrdersModel;
    private ShopPaymentDiscountModel $shopPaymentDiscountModel;
    public function __construct()
    {
        $this->shopItemsModel = new ShopItemsModel();
        $this->shopOrdersModel = new ShopOrdersModel();
        $this->shopPaymentDiscountModel = new ShopPaymentDiscountModel();
    }

    public function getOrdersItemsById(int $id): ?ShopOrdersItemsEntity
    {
        $sql = "SELECT * FROM cmw_shops_orders_items WHERE shop_order_item_id = :shop_order_item_id";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("shop_order_item_id" => $id))) {
            return null;
        }

        $res = $res->fetch();

        $item = is_null($res["shop_item_id"]) ? null : $this->shopItemsModel->getShopItemsById($res["shop_item_id"]);
        $order = is_null($res["shop_order_id"]) ? null : $this->shopOrdersModel->getOrdersById($res["shop_order_id"]);
        $paymentDiscount = is_null($res["shop_payment_discount_id"]) ? null : $this->shopPaymentDiscountModel->getPaymentDiscountById($res["shop_payment_discount_id"]);

        return new ShopOrdersItemsEntity(
            $res["shop_order_item_id"],
            $item,
            $order,
            $paymentDiscount,
            $res["shop_order_item_quantity"],
            $res["shop_order_item_price"],
            $res["shop_order_item_created_at"],
            $res["shop_order_item_updated_at"]
        );
    }

    /**
     * @return \CMW\Entity\Shop\Orders\ShopOrdersItemsEntity []
     */
    public function getOrdersItems(): array
    {

        $sql = "SELECT shop_order_item_id FROM cmw_shops_orders_items ORDER BY shop_order_item_id DESC";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($orderItem = $res->fetch()) {
            $toReturn[] = $this->getOrdersItemsById($orderItem["shop_order_item_id"]);
        }

        return $toReturn;

    }

    /**
     * @return \CMW\Entity\Shop\Orders\ShopOrdersItemsEntity []
     */
    public function getOrdersItemsByOrderId(int $orderId): array
    {
        $var = array(
            "shop_order_id" => $orderId,
        );

        $sql = "SELECT shop_order_item_id FROM cmw_shops_orders_items WHERE shop_order_id = :shop_order_id";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute($var)) {
            return array();
        }

        $toReturn = array();

        while ($orderItem = $res->fetch()) {
            $toReturn[] = $this->getOrdersItemsById($orderItem["shop_order_item_id"]);
        }

        return $toReturn;

    }

    public function createOrderItems(int $orderId, int $itemId, int $itemQuantity, float $price): ?ShopOrdersItemsEntity
    {

        //TODO : prise en charge des variables et promotions appliqué.

        $var = array(
            "shop_order_id" => $orderId,
            "shop_item_id" => $itemId,
            "shop_order_item_quantity" => $itemQuantity,
            "shop_order_item_price" => $price
        );

        $sql = "INSERT INTO cmw_shops_orders_items (shop_item_id, shop_order_id, shop_order_item_quantity, shop_order_item_price) VALUES (:shop_item_id, :shop_order_id, :shop_order_item_quantity, :shop_order_item_price)";

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            $id = $db->lastInsertId();
            return $this->getOrdersItemsById($id);
        }

        return null;
    }

    public function itemIsOrdered(?int $itemId): bool
    {
        if ($itemId === null) {
            return false;
        }

        $var = ["shop_item_id" => $itemId];

        $sql = "SELECT shop_order_item_id FROM `cmw_shops_orders_items` WHERE shop_item_id = :shop_item_id";

        $db = DatabaseManager::getInstance();
        $res = $db->prepare($sql);

        $res->execute($var);

        return count($res->fetchAll()) === 0;
    }
}