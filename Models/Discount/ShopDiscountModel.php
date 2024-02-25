<?php

namespace CMW\Model\Shop\Discount;

use CMW\Entity\Shop\Discounts\ShopDiscountEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Model\Shop\Item\ShopItemsModel;


/**
 * Class: @ShopDiscountModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopDiscountModel extends AbstractModel
{
    public function getAllShopDiscountById(int $id): ?ShopDiscountEntity
    {
        $sql = "SELECT * FROM cmw_shops_discount WHERE shop_discount_id = :shop_discount_id";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(["shop_discount_id" => $id])) {
            return null;
        }

        $res = $res->fetch();

        return new ShopDiscountEntity(
            $res["shop_discount_id"],
            $res["shop_discount_name"] ?? null,
            $res["shop_discount_description"] ?? null,
            $res["shop_discount_linked"],
            $res["shop_discount_start_date"] ?? null,
            $res["shop_discount_end_date"] ?? null,
            $res["shop_discount_default_uses"] ?? null,
            $res["shop_discount_uses_left"] ?? null,
            $res["shop_discount_percent"] ?? null,
            $res["shop_discount_price"] ?? null,
            $res["shop_discount_use_multiple_per_users"] ?? null,
            $res["shop_discount_status"] ?? null,
            $res["shop_discount_test"] ?? null,
            $res["shop_discount_code"] ?? null,
            $res["shop_discount_default_active"] ?? null,
            $res["shop_discount_users_need_purchase_before_use"] ?? null,
            $res["shop_discount_quantity_impacted"] ?? null,
            $res["shop_discount_created_at"] ?? null,
            $res["shop_discount_updated_at"] ?? null
        );
    }

    public function getShopDiscountById(int $id): ?ShopDiscountEntity
    {
        $sql = "SELECT * FROM cmw_shops_discount WHERE shop_discount_id = :shop_discount_id AND shop_discount_default_active = 0";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(["shop_discount_id" => $id])) {
            return null;
        }

        $res = $res->fetch();

        return new ShopDiscountEntity(
            $res["shop_discount_id"],
            $res["shop_discount_name"] ?? null,
            $res["shop_discount_description"] ?? null,
            $res["shop_discount_linked"],
            $res["shop_discount_start_date"] ?? null,
            $res["shop_discount_end_date"] ?? null,
            $res["shop_discount_default_uses"] ?? null,
            $res["shop_discount_uses_left"] ?? null,
            $res["shop_discount_percent"] ?? null,
            $res["shop_discount_price"] ?? null,
            $res["shop_discount_use_multiple_per_users"] ?? null,
            $res["shop_discount_status"] ?? null,
            $res["shop_discount_test"] ?? null,
            $res["shop_discount_code"] ?? null,
            $res["shop_discount_default_active"] ?? null,
            $res["shop_discount_users_need_purchase_before_use"] ?? null,
            $res["shop_discount_quantity_impacted"] ?? null,
            $res["shop_discount_created_at"] ?? null,
            $res["shop_discount_updated_at"] ?? null
        );
    }

    public function getShopDiscountDefaultAppliedById(int $id): ?ShopDiscountEntity
    {
        $sql = "SELECT * FROM cmw_shops_discount WHERE shop_discount_id = :shop_discount_id AND shop_discount_default_active = 1 AND shop_discount_status = 1";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(["shop_discount_id" => $id])) {
            return null;
        }

        $res = $res->fetch();

        return new ShopDiscountEntity(
            $res["shop_discount_id"],
            $res["shop_discount_name"] ?? null,
            $res["shop_discount_description"] ?? null,
            $res["shop_discount_linked"],
            $res["shop_discount_start_date"] ?? null,
            $res["shop_discount_end_date"] ?? null,
            $res["shop_discount_default_uses"] ?? null,
            $res["shop_discount_uses_left"] ?? null,
            $res["shop_discount_percent"] ?? null,
            $res["shop_discount_price"] ?? null,
            $res["shop_discount_use_multiple_per_users"] ?? null,
            $res["shop_discount_status"] ?? null,
            $res["shop_discount_test"] ?? null,
            $res["shop_discount_code"] ?? null,
            $res["shop_discount_default_active"] ?? null,
            $res["shop_discount_users_need_purchase_before_use"] ?? null,
            $res["shop_discount_quantity_impacted"] ?? null,
            $res["shop_discount_created_at"] ?? null,
            $res["shop_discount_updated_at"] ?? null
        );
    }

    /**
     * @return \CMW\Entity\Shop\Discounts\ShopDiscountEntity []
     */
    public function getAllShopDiscounts(): array
    {

        $sql = "SELECT shop_discount_id FROM cmw_shops_discount";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return [];
        }

        $toReturn = [];

        while ($discount = $res->fetch()) {
            $toReturn[] = $this->getAllShopDiscountById($discount["shop_discount_id"]);
        }

        return $toReturn;

    }

    public function codeExist(string $code): bool
    {
        $sql = "SELECT shop_discount_id FROM cmw_shops_discount WHERE shop_discount_code = :shop_discount_code AND shop_discount_default_active = 0 AND shop_discount_status = 1;";
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
     * @return \CMW\Entity\Shop\Discounts\ShopDiscountEntity
     */
    public function getShopDiscountsByCode(string $code): ?ShopDiscountEntity
    {
        $sql = "SELECT shop_discount_id FROM cmw_shops_discount WHERE shop_discount_code = :shop_discount_code AND shop_discount_default_active = 0 AND shop_discount_status = 1";

        $data = ['shop_discount_code' => $code];

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute($data)) {
            return null;
        }

        $res = $res->fetch();

        return $this->getShopDiscountById($res["shop_discount_id"]);

    }

    /**
     * @return \CMW\Entity\Shop\Discounts\ShopDiscountEntity []
     */
    public function getShopDiscountsDefaultAppliedForAll(): array
    {

        $sql = "SELECT shop_discount_id FROM cmw_shops_discount WHERE shop_discount_linked = 0 AND shop_discount_default_active = 1 AND shop_discount_status = 1";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return [];
        }

        $toReturn = [];

        while ($discount = $res->fetch()) {
            $toReturn[] = $this->getShopDiscountDefaultAppliedById($discount["shop_discount_id"]);
        }

        return $toReturn;
    }

    /**
     * @return \CMW\Entity\Shop\Discounts\ShopDiscountEntity []
     * @desc WARNING : This model do not check linked type is only used for check discount default applied health !!!
     */
    private function getShopDiscountsDefaultApplied(): array
    {

        $sql = "SELECT shop_discount_id FROM cmw_shops_discount WHERE shop_discount_default_active = 1 AND shop_discount_status = 1";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return [];
        }

        $toReturn = [];

        while ($discount = $res->fetch()) {
            $toReturn[] = $this->getShopDiscountDefaultAppliedById($discount["shop_discount_id"]);
        }

        return $toReturn;
    }

    public function updateStatus(int $id, int $status): void
    {
        $sql = "UPDATE cmw_shops_discount SET shop_discount_status = :status WHERE shop_discount_id = :shop_discount_id";

        $db = DatabaseManager::getInstance();
        $db->prepare($sql)->execute(['shop_discount_id' => $id, 'status' => $status]);
    }

    public function autoStatusChecker():void {
        $defaultAppliedDiscounts = $this->getShopDiscountsDefaultApplied();
        if (!empty($defaultAppliedDiscounts)) {
            foreach ($defaultAppliedDiscounts as $defaultAppliedDiscount) {
                if (!$this->checkDate($defaultAppliedDiscount->getStartDate(), $defaultAppliedDiscount->getEndDate())) {
                    ShopDiscountModel::getInstance()->updateStatus($defaultAppliedDiscount->getId(), 0);
                }
            }
        }
        //TODO : changer aussi le status des non default applied
    }

    private function checkDate($startDate, $endDate): bool
    {
        $currentTime = time();
        $startDate = strtotime($startDate);
        if ($endDate !== null) {
            $endDate = strtotime($endDate);
            return ($currentTime >= $startDate && $currentTime <= $endDate);
        } else {
            return ($currentTime >= $startDate);
        }
    }

}