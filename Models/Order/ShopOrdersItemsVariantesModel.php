<?php

namespace CMW\Model\Shop\Order;

use CMW\Entity\Shop\Orders\ShopOrdersItemsVariantesEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Model\Shop\Item\ShopItemVariantValueModel;

/**
 * Class: @ShopOrdersItemsVariantesModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopOrdersItemsVariantesModel extends AbstractModel
{
    private ShopOrdersItemsModel $orderItemModel;
    private ShopItemVariantValueModel $itemVariantModel;

    public function __construct()
    {
        $this->orderItemModel = new ShopOrdersItemsModel();
        $this->itemVariantModel = new ShopItemVariantValueModel();
    }

    /**
     * @param int $id
     * @return \CMW\Entity\Shop\ShopOrdersItemsVariantesEntity | null
     */
    public function getOrdersItemsVariantById(int $id): ?ShopOrdersItemsVariantesEntity
    {
        $sql = "SELECT * FROM cmw_shops_orders_items_variantes WHERE shop_orders_items_variantes_id = :shop_orders_items_variantes_id";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(["shop_orders_items_variantes_id" => $id])) {
            return null;
        }

        $res = $res->fetch();

        $orderItem = is_null($res["shop_order_item_id"]) ? null : $this->orderItemModel->getOrdersItemsById($res["shop_order_item_id"]);
        $itemVariantValue = $this->itemVariantModel->getShopItemVariantValueById($res["shop_variants_values_id"]);

        return new ShopOrdersItemsVariantesEntity(
            $res["shop_orders_items_variantes_id"],
            $orderItem,
            $itemVariantValue,
            $res["shop_order_items_variantes_created_at"],
            $res["shop_order_items_variantes_updated_at"]
        );
    }

    public function setVariantToItemInOrder(int $orderId, int $variantValueId): ?ShopOrdersItemsVariantesEntity
    {
        $data = array(
            "shop_order_item_id" => $orderId,
            "shop_variants_values_id" => $variantValueId,
        );

        $sql = "INSERT INTO cmw_shops_orders_items_variantes (shop_order_item_id, shop_variants_values_id)
                VALUES (:shop_order_item_id, :shop_variants_values_id)";

        if (is_null($orderId)) {
            return null;
        }

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        $req->execute($data);

        return null;
    }

    /**
     * @param int $id
     * @return \CMW\Entity\Shop\Carts\ShopCartVariantesEntity []
     */
    public function getShopItemVariantValueByOrderItemId(int $id): array
    {
        $sql = "SELECT * FROM cmw_shops_orders_items_variantes WHERE shop_order_item_id = :shop_order_item_id";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("shop_order_item_id" => $id))) {
            return [];
        }

        $toReturn = [];

        while ($variants = $res->fetch()) {
            $toReturn[] = $this->getOrdersItemsVariantById($variants["shop_orders_items_variantes_id"]);
        }

        return $toReturn;
    }
}