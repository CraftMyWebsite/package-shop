<?php

namespace CMW\Model\Shop\Discount;

use CMW\Entity\Shop\Discounts\ShopDiscountItemsEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Model\Shop\Item\ShopItemsModel;

/**
 * Class: @ShopDiscountItemsModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopDiscountItemsModel extends AbstractModel
{
    public function getShopDiscountItemsById(int $id): ?ShopDiscountItemsEntity
    {
        $sql = 'SELECT * FROM cmw_shops_discount_items WHERE shop_discount_items_id = :shop_discount_items_id';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(['shop_discount_items_id' => $id])) {
            return null;
        }

        $res = $res->fetch();

        $discount = ShopDiscountModel::getInstance()->getAllShopDiscountById($res['shop_discount_id']);
        $item = ShopItemsModel::getInstance()->getShopItemsById($res['shop_item_id']);

        return new ShopDiscountItemsEntity(
            $res['shop_discount_items_id'],
            $discount,
            $item,
        );
    }

    public function getShopDiscountItemsDefaultAppliedById(int $id): ?ShopDiscountItemsEntity
    {
        $sql = 'SELECT cmw_shops_discount_items.*
                FROM cmw_shops_discount_items
                INNER JOIN cmw_shops_discount ON cmw_shops_discount_items.shop_discount_id = cmw_shops_discount.shop_discount_id
                WHERE cmw_shops_discount_items.shop_discount_items_id = :shop_discount_items_id
                AND cmw_shops_discount.shop_discount_status = 1;';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(['shop_discount_items_id' => $id])) {
            return null;
        }

        $res = $res->fetch();

        $discount = ShopDiscountModel::getInstance()->getShopDiscountDefaultAppliedById($res['shop_discount_id']);
        $item = ShopItemsModel::getInstance()->getShopItemsById($res['shop_item_id']);

        return new ShopDiscountItemsEntity(
            $res['shop_discount_items_id'],
            $discount,
            $item,
        );
    }

    /**
     * @param int $itemId
     * @return \CMW\Entity\Shop\Discounts\ShopDiscountItemsEntity []
     */
    public function getShopDiscountItemsByItemId(int $itemId): array
    {
        $sql = 'SELECT * FROM cmw_shops_discount_items WHERE shop_item_id = :shop_item_id';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('shop_item_id' => $itemId))) {
            return [];
        }

        $toReturn = [];

        while ($values = $res->fetch()) {
            $toReturn[] = $this->getShopDiscountItemsById($values['shop_discount_items_id']);
        }

        return $toReturn;
    }

    /**
     * @param int $discountId
     * @return \CMW\Entity\Shop\Discounts\ShopDiscountItemsEntity []
     */
    public function getShopDiscountItemsByDiscountId(int $discountId): array
    {
        $sql = 'SELECT * FROM cmw_shops_discount_items WHERE shop_discount_id = :shop_discount_id';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('shop_discount_id' => $discountId))) {
            return [];
        }

        $toReturn = [];

        while ($values = $res->fetch()) {
            $toReturn[] = $this->getShopDiscountItemsById($values['shop_discount_items_id']);
        }

        return $toReturn;
    }

    /**
     * @param int $itemId
     * @return \CMW\Entity\Shop\Discounts\ShopDiscountItemsEntity []
     */
    public function getShopDiscountItemsDefaultAppliedByItemId(int $itemId): array
    {
        $sql = 'SELECT cmw_shops_discount_items.*
                FROM cmw_shops_discount_items
                INNER JOIN cmw_shops_discount ON cmw_shops_discount_items.shop_discount_id = cmw_shops_discount.shop_discount_id
                WHERE cmw_shops_discount_items.shop_item_id = :shop_item_id
                AND cmw_shops_discount.shop_discount_status = 1 AND cmw_shops_discount.shop_discount_default_active = 1;';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('shop_item_id' => $itemId))) {
            return [];
        }

        $toReturn = [];

        while ($values = $res->fetch()) {
            $toReturn[] = $this->getShopDiscountItemsDefaultAppliedById($values['shop_discount_items_id']);
        }

        return $toReturn;
    }

    public function addDiscountItem(int $discountId, int $itemId): ?ShopDiscountItemsEntity
    {
        $data = array(
            'shop_discount_id' => $discountId,
            'shop_item_id' => $itemId,
        );

        $sql = 'INSERT INTO cmw_shops_discount_items (shop_discount_id, shop_item_id)
                VALUES (:shop_discount_id, :shop_item_id)';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $id = $db->lastInsertId();
            return $this->getShopDiscountItemsById($id);
        }

        return null;
    }
}
