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

        $res = $res->fetch();

        $discount = ShopDiscountModel::getInstance()->getShopDiscountById($res["shop_discount_id"]);
        $cat = ShopCategoriesModel::getInstance()->getShopCategoryById($res["shop_category_id"]);

        return new ShopDiscountCategoriesEntity(
            $res["shop_discount_categories_id"],
            $discount,
            $cat,
        );
    }

    public function getShopDiscountCategoriesDefaultAppliedById(int $id): ?ShopDiscountCategoriesEntity
    {
        $sql = "SELECT cmw_shops_discount_categories.*
                FROM cmw_shops_discount_categories
                INNER JOIN cmw_shops_discount ON cmw_shops_discount_categories.shop_discount_id = cmw_shops_discount.shop_discount_id
                WHERE cmw_shops_discount_categories.shop_discount_categories_id = :shop_discount_categories_id
                AND cmw_shops_discount.shop_discount_status = 1;";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(["shop_discount_categories_id" => $id])) {
            return null;
        }

        $res = $res->fetch();

        $discount = ShopDiscountModel::getInstance()->getShopDiscountDefaultAppliedById($res["shop_discount_id"]);
        $cat = ShopCategoriesModel::getInstance()->getShopCategoryById($res["shop_category_id"]);

        return new ShopDiscountCategoriesEntity(
            $res["shop_discount_categories_id"],
            $discount,
            $cat,
        );
    }

    /**
     * @param int $catId
     * @return \CMW\Entity\Shop\Discounts\ShopDiscountCategoriesEntity []
     */
    public function getShopDiscountCategoriesByCategoryId(int $catId): array
    {
        $sql = "SELECT * FROM cmw_shops_discount_categories WHERE shop_category_id = :shop_category_id";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("shop_category_id" => $catId))) {
            return [];
        }

        $toReturn = [];

        while ($values = $res->fetch()) {
            $toReturn[] = $this->getShopDiscountCategoriesById($values["shop_discount_categories_id"]);
        }

        return $toReturn;
    }

    /**
     * @param int $catId
     * @return \CMW\Entity\Shop\Discounts\ShopDiscountCategoriesEntity []
     */
    public function getShopDiscountCategoriesDefaultAppliedByCategoryId(int $catId): array
    {
        $sql = "SELECT cmw_shops_discount_categories.*
                FROM cmw_shops_discount_categories
                INNER JOIN cmw_shops_discount ON cmw_shops_discount_categories.shop_discount_id = cmw_shops_discount.shop_discount_id
                WHERE cmw_shops_discount_categories.shop_category_id = :shop_category_id
                AND cmw_shops_discount.shop_discount_status = 1";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("shop_category_id" => $catId))) {
            return [];
        }

        $toReturn = [];

        while ($values = $res->fetch()) {
            $toReturn[] = $this->getShopDiscountCategoriesDefaultAppliedById($values["shop_discount_categories_id"]);
        }

        return $toReturn;
    }
}