<?php

namespace CMW\Model\Shop\Discount;

use CMW\Entity\Shop\Discounts\ShopDiscountCategoriesEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Model\Shop\Category\ShopCategoriesModel;


/**
 * Class: @ShopDiscountCategoriesModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopDiscountCategoriesModel extends AbstractModel
{
    public function getShopDiscountCategoriesById(int $id): ?ShopDiscountCategoriesEntity
    {
        $sql = "SELECT * FROM cmw_shops_discount_categories WHERE shop_discount_categories_id = :shop_discount_categories_id";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(["shop_discount_categories_id" => $id])) {
            return null;
        }

        $discount = ShopDiscountModel::getInstance()->getShopDiscountById($res["shop_discount_id"]);
        $cat = ShopCategoriesModel::getInstance()->getShopCategoryById($res["shop_category_id"]);

        $res = $res->fetch();

        return new ShopDiscountCategoriesEntity(
            $res["shop_discount_categories_id"],
            $discount,
            $cat,
        );
    }
}