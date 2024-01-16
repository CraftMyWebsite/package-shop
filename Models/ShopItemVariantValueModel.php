<?php

namespace CMW\Model\Shop;

use CMW\Entity\Shop\Items\ShopItemVariantValueEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Model\Users\UsersModel;


/**
 * Class: @ShopItemVariantValueModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopItemVariantValueModel extends AbstractModel
{
    private ShopItemVariantModel $variantModel;

    public function __construct()
    {
        $this->variantModel = new ShopItemVariantModel();
    }

    /**
     * @param ?int $id
     * @return \CMW\Entity\Shop\Items\ShopItemVariantValueEntity
     */
    public function getShopItemVariantValueById(?int $id): ?ShopItemVariantValueEntity
    {
        $sql = "SELECT * FROM cmw_shops_items_variants_values WHERE shop_variants_values_id = :shop_variants_values_id";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("shop_variants_values_id" => $id))) {
            return null;
        }
        if (is_null($id)) {
            return null;
        }

        $res = $res->fetch();

        $variant = $this->variantModel->getShopItemVariantById($res["shop_variants_id"]);

        return new ShopItemVariantValueEntity(
            $res["shop_variants_values_id"],
            $variant,
            $res["shop_variants_value"],
            $res["shop_variants_values_created_at"],
            $res["shop_variants_values_updated_at"]
        );
    }

    /**
     * @param int $id
     * @return \CMW\Entity\Shop\Items\ShopItemVariantValueEntity []
     */
    public function getShopItemVariantValueByVariantId(int $id): array
    {
        $sql = "SELECT * FROM cmw_shops_items_variants_values WHERE shop_variants_id = :shop_variants_id";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("shop_variants_id" => $id))) {
            return [];
        }

        $toReturn = [];

        while ($variants = $res->fetch()) {
            $toReturn[] = $this->getShopItemVariantValueById($variants["shop_variants_values_id"]);
        }

        return $toReturn;
    }

    public function addVariantValue(string $value, ?int $variantId): ?ShopItemVariantValueEntity
    {
        $data = array(
            "shop_variants_value" => $value,
            "shop_variants_id" => $variantId,
        );

        $sql = "INSERT INTO cmw_shops_items_variants_values (shop_variants_id, shop_variants_value)
                VALUES (:shop_variants_id, :shop_variants_value)";

        if (is_null($variantId)) {
            return null;
        }

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $id = $db->lastInsertId();
            return $this->getShopItemVariantValueById($id);
        }

        return null;
    }
}