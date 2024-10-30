<?php

namespace CMW\Model\Shop\HistoryOrder;

use CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity;
use CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersPaymentEntity;
use CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersShippingEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Model\Shop\Shipping\ShopShippingModel;

/**
 * Class: @ShopHistoryOrdersShippingModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopHistoryOrdersShippingModel extends AbstractModel
{
    /**
     * @param int $id
     * @return ShopHistoryOrdersEntity|null
     */
    public function getHistoryOrdersShippingByHistoryOrderId(int $id): ?ShopHistoryOrdersShippingEntity
    {
        $sql = 'SELECT * FROM cmw_shop_history_order_shipping WHERE shop_history_order_id = :shop_history_order_id';

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
        $shipping = is_null($res['shops_shipping_id']) ? null : ShopShippingModel::getInstance()->getShopShippingById($res['shops_shipping_id']) ?? null;

        return new ShopHistoryOrdersShippingEntity(
            $res['shop_history_order_shipping_id'],
            $historyOrder,
            $shipping,
            $res['shop_history_order_shipping_name'] ?? null,
            $res['shop_history_order_shipping_price'] ?? null
        );
    }

    public function addHistoryShippingOrder(int $orderId, int $shippingId, string $shippingName, float $shippingPrice): ?ShopHistoryOrdersShippingEntity
    {
        $var = array(
            'shop_history_order_id' => $orderId,
            'shops_shipping_id' => $shippingId,
            'shop_history_order_shipping_name' => $shippingName,
            'shop_history_order_shipping_price' => $shippingPrice
        );

        $sql = 'INSERT INTO cmw_shop_history_order_shipping (shop_history_order_id, shops_shipping_id, shop_history_order_shipping_name, shop_history_order_shipping_price) VALUES (:shop_history_order_id, :shops_shipping_id, :shop_history_order_shipping_name, :shop_history_order_shipping_price)';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            return $this->getHistoryOrdersShippingByHistoryOrderId($orderId);
        }

        return null;
    }
}
