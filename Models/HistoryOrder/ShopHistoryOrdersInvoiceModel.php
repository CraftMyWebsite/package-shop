<?php

namespace CMW\Model\Shop\HistoryOrder;

use CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersDiscountEntity;
use CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersInvoiceEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;

/**
 * Class: @ShopHistoryOrdersInvoiceModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopHistoryOrdersInvoiceModel extends AbstractModel
{
    /**
     * @param int $historyOrderId
     * @return ShopHistoryOrdersInvoiceEntity|null
     */
    public function getInvoiceByHistoryOrderId(int $historyOrderId): ?ShopHistoryOrdersInvoiceEntity
    {
        $sql = 'SELECT * FROM cmw_shop_history_order_invoice WHERE shop_history_order_id = :shop_history_order_id';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('shop_history_order_id' => $historyOrderId))) {
            return null;
        }

        $res = $res->fetch();

        if (empty($res)) {
            return null;
        }

        $historyOrder = is_null($res['shop_history_order_id']) ? null : ShopHistoryOrdersModel::getInstance()->getHistoryOrdersById($res['shop_history_order_id']);

        return new ShopHistoryOrdersInvoiceEntity(
            $res['shop_history_order_invoice_id'],
            $historyOrder,
            $res['shop_history_order_invoice_link'],
            $res['shop_history_order_invoice_created_at'],
        );
    }

    public function addInvoice(int $historyOrderId, string $invoiceLink): ?ShopHistoryOrdersInvoiceEntity
    {
        $var = array(
            'shop_history_order_id' => $historyOrderId,
            'shop_history_order_invoice_link' => $invoiceLink
        );

        $sql = 'INSERT INTO cmw_shop_history_order_invoice (shop_history_order_id, shop_history_order_invoice_link) VALUES (:shop_history_order_id, :shop_history_order_invoice_link)';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            return $this->getInvoiceByHistoryOrderId($historyOrderId);
        }

        return null;
    }
}
