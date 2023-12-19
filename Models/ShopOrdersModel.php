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
    private UsersModel $userModel;
    public function __construct()
    {
        $this->userModel = new UsersModel();
    }

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

        $user = is_null($res["shop_user_id"]) ? null : $this->userModel->getUserById($res["shop_user_id"]);

        return new ShopOrdersEntity(
            $res["shop_order_id"],
            $user,
            $res["shop_order_created_at"],
            $res["shop_order_updated_at"]
        );
    }

    /**
     * @return \CMW\Entity\Shop\ShopOrdersEntity []
     */
    public function getOrders(): array
    {

        $sql = "SELECT shop_order_id FROM cmw_shops_orders ORDER BY shop_item_id DESC";
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
        $sql = "SELECT COUNT(*) AS user_count_ordered_item
                FROM cmw_shops_orders o
                JOIN cmw_shops_orders_items i ON o.shop_order_id = i.shop_order_id
                WHERE o.shop_user_id = :shop_user_id
                AND i.shop_item_id = :shop_item_id;";

        $data = ["shop_item_id" => $itemId, "shop_user_id" => $userId];

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute($data)) {
            return 0;
        }

        return $res->fetch(0)['user_count_ordered_item'];
    }
}