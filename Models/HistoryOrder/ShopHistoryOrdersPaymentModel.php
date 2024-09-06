<?php

namespace CMW\Model\Shop\HistoryOrder;

use CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity;
use CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersPaymentEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;

/**
 * Class: @ShopHistoryOrdersPaymentModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopHistoryOrdersPaymentModel extends AbstractModel
{
    /**
     * @param int $id
     * @return ShopHistoryOrdersEntity|null
     */
    public function getHistoryOrdersPaymentByHistoryOrderId(int $id): ?ShopHistoryOrdersPaymentEntity
    {
        $sql = 'SELECT * FROM cmw_shop_history_order_payment WHERE shop_history_order_id = :shop_history_order_id';

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

        return new ShopHistoryOrdersPaymentEntity(
            $res['shop_history_order_payment_id'],
            $historyOrder,
            $res['shop_history_order_payment_name'] ?? null,
            $res['shop_history_order_payment_var_name'] ?? null,
            $res['shop_history_order_payment_fee'] ?? null
        );
    }

    public function addHistoryPaymentOrder(int $orderId, string $PaymentName, string $PaymentVarName, float $PaymentFee): ?ShopHistoryOrdersPaymentEntity
    {
        $var = array(
            'shop_history_order_id' => $orderId,
            'shop_history_order_payment_name' => $PaymentName,
            'shop_history_order_payment_var_name' => $PaymentVarName,
            'shop_history_order_payment_fee' => $PaymentFee
        );

        $sql = 'INSERT INTO cmw_shop_history_order_payment (shop_history_order_id, shop_history_order_payment_name, shop_history_order_payment_var_name, shop_history_order_payment_fee) VALUES (:shop_history_order_id, :shop_history_order_payment_name, :shop_history_order_payment_var_name, :shop_history_order_payment_fee)';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            return $this->getHistoryOrdersPaymentByHistoryOrderId($orderId);
        }

        return null;
    }
}
