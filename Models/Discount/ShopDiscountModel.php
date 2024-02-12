<?php

namespace CMW\Model\Shop\Discount;

use CMW\Entity\Shop\Discounts\ShopDiscountEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;


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
        $sql = "SELECT * FROM cmw_shops_discount WHERE shop_discount_id = :shop_discount_id";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(["shop_discount_id" => $id])) {
            return null;
        }

        $res = $res->fetch();

        return new ShopDiscountEntity(
            $res["shop_payment_discount_id"],
            $res["shop_discount_name"] ?? null,
            $res["shop_discount_description"] ?? null,
            $res["shop_discount_start_date"] ?? null,
            $res["shop_discount_end_date"] ?? null,
            $res["shop_discount_default_uses"] ?? null,
            $res["shop_discount_uses_left"] ?? null,
            $res["shop_discount_percent"] ?? null,
            $res["shop_discount_price"] ?? null,
            $res["shop_discount_use_multiple_per_users"] ?? null,
            $res["shop_discount_cumulative"] ?? null,
            $res["shop_discount_status"] ?? null,
            $res["shop_discount_code"] ?? null,
            $res["shop_discount_default_active"] ?? null,
            $res["shop_discount_users_need_purchase_before_use"] ?? null,
            $res["shop_discount_created_at"] ?? null,
            $res["shop_discount_updated_at"] ?? null
        );
    }

    /**
     * @return \CMW\Entity\Shop\Discounts\ShopDiscountEntity []
     */
    public function getShopDiscounts(): array
    {

        $sql = "SELECT shop_discount_id FROM cmw_shops_discount";
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

    public function codeExist(string $code): bool
    {
        $sql = "SELECT shop_discount_id FROM cmw_shops_discount WHERE shop_discount_code = :shop_discount_code;";
        $data['shop_discount_code'] = $code;
        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute($data)) {
            return false;
        }

        $res = $req->fetch();

        if (!$res) {
            return false;
        }

        return true;
    }

    /**
     * @return \CMW\Entity\Shop\Discounts\ShopDiscountEntity []
     */
    public function getDefaultActiveDiscountByItemId(int $itemId): array
    {
        //TODO ! avec des check dans les models Items et Categories
    }
}