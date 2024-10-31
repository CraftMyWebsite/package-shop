<?php

namespace CMW\Model\Shop\HistoryOrder;

use CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersDiscountEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;

/**
 * Class: @ShopHistoryOrdersDiscountModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopHistoryOrdersDiscountModel extends AbstractModel
{
    /**
     * @param int $id
     * @return ShopHistoryOrdersDiscountEntity|null
     */
    public function getHistoryOrdersDiscountById(int $id): ?ShopHistoryOrdersDiscountEntity
    {
        $sql = 'SELECT * FROM cmw_shop_history_order_discount WHERE shop_history_order_id = :shop_history_order_id';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('shop_history_order_id' => $id))) {
            return null;
        }

        $res = $res->fetch();

        if (empty($res)) {
            return null;
        }

        $historyOrder = is_null($res['shop_history_order_id']) ? null : ShopHistoryOrdersModel::getInstance()->getHistoryOrdersById($res['shop_history_order_id']) ?? null;

        return new ShopHistoryOrdersDiscountEntity(
            $res['shop_history_order_discount_id'],
            $historyOrder,
            $res['shop_history_order_discount_name'] ?? null,
            $res['shop_history_order_discount_price'] ?? null,
            $res['shop_history_order_discount_percent'] ?? null
        );
    }

    /**
     * @return ?ShopHistoryOrdersDiscountEntity []
     */
    public function getHistoryOrdersDiscountByHistoryOrderId(int $orderId): ?array
    {
        $var = array(
            'shop_history_order_id' => $orderId,
        );

        $sql = 'SELECT shop_history_order_id FROM cmw_shop_history_order_discount WHERE shop_history_order_id = :shop_history_order_id';
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute($var)) {
            return array();
        }

        $toReturn = array();

        while ($orderItem = $res->fetch()) {
            $toReturn[] = $this->getHistoryOrdersDiscountById($orderItem['shop_history_order_id']);
        }

        return $toReturn;
    }

    public function addHistoryDiscountOrder(int $orderId, string $discountName, float $discountPrice, int $discountPercent): ?ShopHistoryOrdersDiscountEntity
    {
        $var = array(
            'shop_history_order_id' => $orderId,
            'shop_history_order_discount_name' => $discountName,
            'shop_history_order_discount_price' => $discountPrice,
            'shop_history_order_discount_percent' => $discountPercent
        );

        $sql = 'INSERT INTO cmw_shop_history_order_discount (shop_history_order_id, shop_history_order_discount_name, shop_history_order_discount_price, shop_history_order_discount_percent) VALUES (:shop_history_order_id, :shop_history_order_discount_name, :shop_history_order_discount_price, :shop_history_order_discount_percent)';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            return $this->getHistoryOrdersDiscountById($orderId);
        }

        return null;
    }
}
