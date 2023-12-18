<?php

namespace CMW\Model\Shop;

use CMW\Entity\Shop\ShopOrdersItemsEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;

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
            $res["shop_order_item_id "],
            $item,
            $order,
            $paymentDiscount,
            $res["shop_order_item_quantity"],
            $res["shop_order_item_status"],
            $res["shop_order_item_price"],
            $res["shop_order_item_created_at"],
            $res["shop_order_item_updated_at"]
        );
    }

    /**
     * @return \CMW\Entity\Shop\ShopOrdersItemsEntity []
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
}