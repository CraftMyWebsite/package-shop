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
}