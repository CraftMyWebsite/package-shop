<?php

namespace CMW\Model\Shop;

use CMW\Entity\Shop\ShopOrdersEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Model\Users\UsersModel;


/**
 * Class: @ShopOrdersModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopOrdersModel extends AbstractModel
{
    /**
     * @param int $id
     * @return \CMW\Entity\Shop\ShopOrdersEntity|null
     */
    public function getOrdersById(int $id): ?ShopOrdersEntity
    {
        $sql = "SELECT * FROM cmw_shops_orders WHERE shop_order_id = :shop_order_id";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("shop_order_id" => $id))) {
            return null;
        }

        $res = $res->fetch();

        $user = is_null($res["shop_user_id"]) ? null : UsersModel::getInstance()->getUserById($res["shop_user_id"]);
        $shipping = is_null($res["shops_shipping_id"])? null : ShopShippingModel::getInstance()->getShopShippingById($res["shops_shipping_id"]);
        $deliveryAddress = is_null($res["shop_delivery_user_address_id"])? null : ShopDeliveryUserAddressModel::getInstance()->getShopDeliveryUserAddressById($res["shop_delivery_user_address_id"]);

        return new ShopOrdersEntity(
            $res["shop_order_id"],
            $user,
            $res["shop_order_number"],
            $res["shop_order_status"],
            $shipping,
            $deliveryAddress,
            $res["shop_order_created_at"],
            $res["shop_order_updated_at"]
        );
    }

    /**
     * @return \CMW\Entity\Shop\ShopOrdersEntity []
     */
    public function getOrders(): array
    {

        $sql = "SELECT shop_order_id FROM cmw_shops_orders";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($order = $res->fetch()) {
            $toReturn[] = $this->getOrdersById($order["shop_order_id"]);
        }

        return $toReturn;

    }

    /**
     * @return \CMW\Entity\Shop\ShopOrdersEntity []
     */
    public function getInProgressOrders(): array
    {

        $sql = "SELECT shop_order_id FROM cmw_shops_orders WHERE shop_order_status IN (1, 2, 0);";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($order = $res->fetch()) {
            $toReturn[] = $this->getOrdersById($order["shop_order_id"]);
        }

        return $toReturn;

    }

    /**
     * @return \CMW\Entity\Shop\ShopOrdersEntity []
     */
    public function getFinishedOrders(): array
    {

        $sql = "SELECT shop_order_id FROM cmw_shops_orders WHERE shop_order_status = 3;";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($order = $res->fetch()) {
            $toReturn[] = $this->getOrdersById($order["shop_order_id"]);
        }

        return $toReturn;

    }

    /**
     * @return \CMW\Entity\Shop\ShopOrdersEntity []
     */
    public function getErrorOrders(): array
    {

        $sql = "SELECT shop_order_id FROM cmw_shops_orders WHERE shop_order_status IN (-1, -2);";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($order = $res->fetch()) {
            $toReturn[] = $this->getOrdersById($order["shop_order_id"]);
        }

        return $toReturn;

    }

    /**
     * @param int $userId
     * @param int $itemId
     * @return int
     */
    public function countOrderByUserIdAndItemId(int $userId, int $itemId): int
    {
        $sql = "SELECT SUM(soi.shop_order_item_quantity) AS total_quantity FROM cmw_shops_orders_items soi
                JOIN cmw_shops_orders so ON soi.shop_order_id = so.shop_order_id
                WHERE so.shop_user_id = :shop_user_id AND soi.shop_item_id = :shop_item_id;";

        $data = ["shop_item_id" => $itemId, "shop_user_id" => $userId];

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute($data)) {
            return 0;
        }

        return $res->fetch(0)['total_quantity'];
    }

    public function createOrder(int $userId, int $shippingId, int $deliveryAddress): ?ShopOrdersEntity
    {
        $var = array(
            "shop_user_id" => $userId,
            "shops_shipping_id" => $shippingId,
            "shop_delivery_user_address_id" => $deliveryAddress
        );

        $sql = "INSERT INTO cmw_shops_orders (shop_user_id, shops_shipping_id, shop_delivery_user_address_id) VALUES (:shop_user_id, :shops_shipping_id, :shop_delivery_user_address_id)";

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            $id = $db->lastInsertId();
            $this->generateOrderNumber($id);
            return $this->getOrdersById($id);
        }

        return null;
    }

    public function generateOrderNumber(int $orderId): void
    {
        $number = date("njy"). $orderId;
        $data = ["shop_order_id" => $orderId, "number" => $number];

        $sql = "UPDATE cmw_shops_orders SET shop_order_number = :number WHERE shop_order_id = :shop_order_id";
        $db = DatabaseManager::getInstance();
        $db->prepare($sql)->execute($data);
    }
}