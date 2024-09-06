<?php

namespace CMW\Model\Shop\HistoryOrder;

use CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersItemsVariantesEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;

/**
 * Class: @ShopHistoryOrdersItemsVariantesModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopHistoryOrdersItemsVariantesModel extends AbstractModel
{
    /**
     * @param int $id
     * @return ShopHistoryOrdersItemsVariantesEntity | null
     */
    public function getHistoryOrdersItemsVariantById(int $id): ?ShopHistoryOrdersItemsVariantesEntity
    {
        $sql = 'SELECT * FROM cmw_shop_history_order_items_variantes WHERE shop_history_order_items_variantes_id = :shop_history_order_items_variantes_id';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(['shop_history_order_items_variantes_id' => $id])) {
            return null;
        }

        $res = $res->fetch();

        $historyOrderItem = is_null($res['shop_history_order_items_id']) ? null : ShopHistoryOrdersItemsModel::getInstance()->getHistoryOrdersItemsById($res['shop_history_order_items_id']) ?? null;

        return new ShopHistoryOrdersItemsVariantesEntity(
            $res['shop_history_order_items_variantes_id'],
            $historyOrderItem,
            $res['shop_history_order_items_variantes_name'],
            $res['shop_history_order_items_variantes_value']
        );
    }

    public function setVariantToItemInOrder(int $historyOrderItem, string $variantValue, string $variantName): ?ShopHistoryOrdersItemsVariantesEntity
    {
        $data = array(
            'shop_history_order_items_id' => $historyOrderItem,
            'shop_history_order_items_variantes_name' => $variantName,
            'shop_history_order_items_variantes_value' => $variantValue,
        );

        $sql = 'INSERT INTO cmw_shop_history_order_items_variantes (shop_history_order_items_id, shop_history_order_items_variantes_name, shop_history_order_items_variantes_value)
                VALUES (:shop_history_order_items_id, :shop_history_order_items_variantes_name, :shop_history_order_items_variantes_value)';

        if (is_null($historyOrderItem)) {
            return null;
        }

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $id = $db->lastInsertId();
            return $this->getHistoryOrdersItemsVariantById($id);
        }

        return null;
    }

    /**
     * @param int $id
     * @return ShopHistoryOrdersItemsVariantesEntity []
     */
    public function getShopItemVariantValueByOrderItemId(int $id): array
    {
        $sql = 'SELECT shop_history_order_items_variantes_id FROM cmw_shop_history_order_items_variantes WHERE shop_history_order_items_id = :shop_history_order_items_id';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('shop_history_order_items_id' => $id))) {
            return [];
        }

        $toReturn = [];

        while ($variants = $res->fetch()) {
            $toReturn[] = $this->getHistoryOrdersItemsVariantById($variants['shop_history_order_items_variantes_id']);
        }

        return $toReturn;
    }
}
