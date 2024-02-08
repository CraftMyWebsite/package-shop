<?php

namespace CMW\Model\Shop\Discount;

use CMW\Entity\Shop\Discounts\ShopDiscountEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Model\Shop\Category\ShopCategoriesModel;
use CMW\Model\Shop\Item\ShopItemsModel;


/**
 * Class: @ShopDiscountModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopDiscountModel extends AbstractModel
{
    public function getShopDiscountById(int $id): ?ShopDiscountEntity
    {
        $sql = "SELECT * FROM cmw_shops_payment_discount WHERE shop_payment_discount_id = :shop_payment_discount_id";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(["shop_payment_discount_id" => $id])) {
            return null;
        }

        $res = $res->fetch();

        $item = is_null($res["shop_item_id"]) ? null : ShopItemsModel::getInstance()->getShopItemsById($res["shop_item_id"]);
        $cat = is_null($res["shop_category_id"]) ? null : ShopCategoriesModel::getInstance()->getShopCategoryById($res["shop_category_id"]);

        return new ShopDiscountEntity(
            $res["shop_payment_discount_id"],
            $res["shop_payment_discount_name"] ?? null,
            $res["shop_payment_discount_description"] ?? null,
            $res["shop_payment_discount_start_date"] ?? null,
            $res["shop_payment_discount_end_date"] ?? null,
            $res["shop_payment_discount_default_uses"] ?? null,
            $res["shop_payment_discount_uses_left"] ?? null,
            $res["shop_payment_discount_percent"] ?? null,
            $res["shop_payment_discount_price"] ?? null,
            $res["shop_payment_discount_use_multiple_per_users"] ?? null,
            $res["shop_payment_discount_cumulative"] ?? null,
            $res["shop_payment_discount_status"] ?? null,
            $item,
            $cat,
            $res["shop_payment_discount_code"] ?? null,
            $res["shop_payment_discount_default_active"] ?? null,
            $res["shop_payment_discount_users_need_purchase_before_use"] ?? null,
            $res["shop_payment_discount_created_at"] ?? null,
            $res["shop_payment_discount_updated_at"] ?? null
        );
    }

    /**
     * @return \CMW\Entity\Shop\Discounts\ShopDiscountEntity []
     */
    public function getShopDiscounts(): array
    {

        $sql = "SELECT shop_payment_discount_id FROM cmw_shops_payment_discount";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return [];
        }

        $toReturn = [];

        while ($discount = $res->fetch()) {
            $toReturn[] = $this->getShopDiscountById($discount["shop_payment_discount_id"]);
        }

        return $toReturn;

    }
}