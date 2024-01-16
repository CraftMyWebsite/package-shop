<?php

namespace CMW\Model\Shop;

use CMW\Entity\Shop\Items\ShopItemVariantEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Model\Users\UsersModel;


/**
 * Class: @ShopItemVariantModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopItemVariantModel extends AbstractModel
{
    private ShopItemsModel $itemModel;

    public function __construct()
    {
        $this->itemModel = new ShopItemsModel();
    }

    /**
     * @param ?int $id
     * @return \CMW\Entity\Shop\Items\ShopItemVariantEntity
     */
    public function getShopItemVariantById(?int $id): ?ShopItemVariantEntity
    {
        $sql = "SELECT * FROM cmw_shops_items_variants WHERE shop_variants_id = :shop_variants_id";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("shop_variants_id" => $id))) {
            return null;
        }
        if (is_null($id)) {
            return null;
        }

        $res = $res->fetch();

        $item = $this->itemModel->getShopItemsById($res["shop_item_id"]);

        return new ShopItemVariantEntity(
            $res["shop_variants_id"],
            $item,
            $res["shop_variants_name"] ?? null,
            $res["shop_variants_created_at"] ?? null,
            $res["shop_variants_updated_at"] ?? null
        );
    }

    /**
     * @param int $id
     * @return \CMW\Entity\Shop\Items\ShopItemVariantEntity []
     */
    public function getShopItemVariantByItemId(int $id): array
    {
        $sql = "SELECT * FROM cmw_shops_items_variants WHERE shop_item_id = :shop_item_id";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("shop_item_id" => $id))) {
            return [];
        }

        $toReturn = [];

        while ($values = $res->fetch()) {
            $toReturn[] = $this->getShopItemVariantById($values["shop_variants_id"]);
        }

        return $toReturn;
    }

    public function createVariant(string $name, int $itemId): ?ShopItemVariantEntity
    {
        $data = array(
            "shop_variants_name" => $name,
            "shop_item_id" => $itemId,
        );

        $sql = "INSERT INTO cmw_shops_items_variants (shop_item_id, shop_variants_name)
                VALUES (:shop_item_id, :shop_variants_name)";


        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $id = $db->lastInsertId();
            return $this->getShopItemVariantById($id);
        }

        return null;
    }

    public function itemHasVariant(int $itemId): bool
    {
        $data = ["shop_item_id" => $itemId];

        $sql = "SELECT shop_variants_id FROM cmw_shops_items_variants WHERE shop_item_id =:shop_item_id";

        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if(!$req->execute($data)){
            return true;
        }

        $res = $req->fetch();

        if (!$res){
            return false;
        }

        return true;
    }
}