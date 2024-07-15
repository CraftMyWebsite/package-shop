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
            $res["shop_discount_linked"],
            $res["shop_discount_start_date"] ?? null,
            $res["shop_discount_end_date"] ?? null,
            $res["shop_discount_max_uses"] ?? null,
            $res["shop_discount_current_uses"] ?? null,
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
            $res["shop_discount_linked"],
            $res["shop_discount_start_date"] ?? null,
            $res["shop_discount_end_date"] ?? null,
            $res["shop_discount_max_uses"] ?? null,
            $res["shop_discount_current_uses"] ?? null,
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
            $res["shop_discount_linked"],
            $res["shop_discount_start_date"] ?? null,
            $res["shop_discount_end_date"] ?? null,
            $res["shop_discount_max_uses"] ?? null,
            $res["shop_discount_current_uses"] ?? null,
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

    public function createDiscount(string $name, int $linked, ?string $startDate, ?string $endDate, ?int $maxUses, ?int $currentUses, ?int $percent, ?float $price, ?int $useMultipleByUser, ?int $status, ?int $isTest, ?string $code, int $defaultApplied, ?int $needPurchaseBeforeBuy, int $quantityImpacted): ?ShopDiscountEntity
    {
        $data = [
            "shop_discount_name" => $name,
            "shop_discount_linked" => $linked,
            "shop_discount_start_date" => $startDate,
            "shop_discount_end_date" => $endDate,
            "shop_discount_max_uses" => $maxUses,
            "shop_discount_current_uses" => $currentUses,
            "shop_discount_percent" => $percent,
            "shop_discount_price" => $price,
            "shop_discount_use_multiple_per_users" => $useMultipleByUser,
            "shop_discount_status" => $status,
            "shop_discount_test" => $isTest,
            "shop_discount_code" => $code,
            "shop_discount_default_active" => $defaultApplied,
            "shop_discount_users_need_purchase_before_use" => $needPurchaseBeforeBuy,
            "shop_discount_quantity_impacted" => $quantityImpacted,
        ];

        $sql = "INSERT INTO cmw_shops_discount(shop_discount_name,shop_discount_linked,shop_discount_start_date,shop_discount_end_date,shop_discount_max_uses,shop_discount_current_uses,shop_discount_percent,shop_discount_price,shop_discount_use_multiple_per_users,shop_discount_status,shop_discount_test,shop_discount_code,shop_discount_default_active,shop_discount_users_need_purchase_before_use,shop_discount_quantity_impacted) 
                                VALUES (:shop_discount_name,:shop_discount_linked,:shop_discount_start_date,:shop_discount_end_date,:shop_discount_max_uses,:shop_discount_current_uses,:shop_discount_percent,:shop_discount_price,:shop_discount_use_multiple_per_users,:shop_discount_status,:shop_discount_test,:shop_discount_code,:shop_discount_default_active,:shop_discount_users_need_purchase_before_use,:shop_discount_quantity_impacted)";

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $id = $db->lastInsertId();
            return $this->getAllShopDiscountById($id);
        }

        return null;
    }

    /**
     * @return \CMW\Entity\Shop\Discounts\ShopDiscountEntity []
     */
    public function getAllDiscounts(): array
    {
        $sql = "SELECT shop_discount_id FROM cmw_shops_discount WHERE shop_discount_linked IN (0, 1, 2)";
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

    /**
     * @return \CMW\Entity\Shop\Discounts\ShopDiscountEntity []
     */
    public function getAllGiftCard(): array
    {
        $sql = "SELECT shop_discount_id FROM cmw_shops_discount WHERE shop_discount_linked = 3";
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

    public function addUses(int $id, int $uses): void
    {
        $sql = "UPDATE cmw_shops_discount SET shop_discount_current_uses = :uses WHERE shop_discount_id = :shop_discount_id";

        $db = DatabaseManager::getInstance();
        $db->prepare($sql)->execute(['shop_discount_id' => $id, 'uses' => $uses]);
    }

    public function autoStatusChecker(): void
    {
        //TODO : Max uses ateint on desactive
        $discounts = $this->getAllDiscounts();
        if (!empty($discounts)) {
            foreach ($discounts as $discount) {
                if ($this->checkDate($discount->getStartDate(), $discount->getEndDate())) {
                    ShopDiscountModel::getInstance()->updateStatus($discount->getId(), 1);
                } else {
                    ShopDiscountModel::getInstance()->updateStatus($discount->getId(), 0);
                }
            }
        }
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

    public function deleteDiscount(int $id): bool
    {
        $sql = "DELETE FROM cmw_shops_discount WHERE shop_discount_id = :shop_discount_id";

        $db = DatabaseManager::getInstance();

        return $db->prepare($sql)->execute(array("shop_discount_id" => $id));
    }

    public function stopDiscount(int $id): void
    {
        $targetDate = strtotime('now');
        $endDate = date('Y-m-d H:i:s', $targetDate);

        $sql = "UPDATE cmw_shops_discount SET shop_discount_status = 0, shop_discount_end_date = :endDate WHERE shop_discount_id = :shop_discount_id";

        $db = DatabaseManager::getInstance();
        $db->prepare($sql)->execute(['shop_discount_id' => $id, 'endDate' => $endDate]);
    }

    public function startDiscount(int $id): void
    {
        $currentDateTime = date('Y-m-d H:i:s');

        $sql = "UPDATE cmw_shops_discount SET shop_discount_status = 1, shop_discount_start_date = :startDate WHERE shop_discount_id = :shop_discount_id";

        $db = DatabaseManager::getInstance();
        $db->prepare($sql)->execute(['shop_discount_id' => $id, 'startDate' => $currentDateTime]);
    }

    public function reportDiscount(int $id, string $startDate): void
    {
        $sql = "UPDATE cmw_shops_discount SET shop_discount_status = 1, shop_discount_start_date = :shop_discount_start_date WHERE shop_discount_id = :shop_discount_id";

        $db = DatabaseManager::getInstance();
        $db->prepare($sql)->execute(['shop_discount_id' => $id, "shop_discount_start_date" => $startDate]);
    }

    public function editDiscount(int $id, string $name, ?string $endDate, ?int $maxUses, ?int $currentUses, ?int $percent, ?float $price, ?int $useMultipleByUser, ?int $status, ?int $isTest, ?string $code, ?int $needPurchaseBeforeBuy, int $quantityImpacted): void
    {
        $data = [
            "shop_discount_id" => $id,
            "shop_discount_name" => $name,
            "shop_discount_end_date" => $endDate,
            "shop_discount_max_uses" => $maxUses,
            "shop_discount_current_uses" => $currentUses,
            "shop_discount_percent" => $percent,
            "shop_discount_price" => $price,
            "shop_discount_use_multiple_per_users" => $useMultipleByUser,
            "shop_discount_status" => $status,
            "shop_discount_test" => $isTest,
            "shop_discount_code" => $code,
            "shop_discount_users_need_purchase_before_use" => $needPurchaseBeforeBuy,
            "shop_discount_quantity_impacted" => $quantityImpacted,
        ];

        $sql = "UPDATE cmw_shops_discount SET shop_discount_name = :shop_discount_name, shop_discount_end_date = :shop_discount_end_date,
                             shop_discount_max_uses = :shop_discount_max_uses, shop_discount_current_uses = :shop_discount_current_uses,
                             shop_discount_percent = :shop_discount_percent, shop_discount_price =:shop_discount_price, 
                             shop_discount_use_multiple_per_users = :shop_discount_use_multiple_per_users, shop_discount_status =:shop_discount_status, 
                             shop_discount_test =:shop_discount_test,shop_discount_code =:shop_discount_code, shop_discount_users_need_purchase_before_use=:shop_discount_users_need_purchase_before_use,
                             shop_discount_quantity_impacted =:shop_discount_quantity_impacted WHERE shop_discount_id = :shop_discount_id";

        $db = DatabaseManager::getInstance();
        $db->prepare($sql)->execute($data);
    }

}