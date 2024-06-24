<?php

namespace CMW\Model\Shop\Item;

use CMW\Entity\Shop\Items\ShopItemPhysicalRequirementEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;


/**
 * Class: @ShopItemsPhysicalRequirementModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopItemsPhysicalRequirementModel extends AbstractModel
{
    private ShopItemsModel $itemModel;

    public function __construct()
    {
        $this->itemModel = new ShopItemsModel();
    }

    /**
     * @param int $id
     * @return \CMW\Entity\Shop\Commands\ShopCommandTunnelEntity
     */
    public function getShopItemPhysicalRequirementById(int $id): ?ShopItemPhysicalRequirementEntity
    {
        $sql = "SELECT * FROM cmw_shops_items_physical_requirement WHERE shop_item_physical_requirement_id = :shop_item_physical_requirement_id";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("shop_item_physical_requirement_id" => $id))) {
            return null;
        }

        $res = $res->fetch();

        $item = $this->itemModel->getShopItemsById($res["shop_item_id"]);

        return new ShopItemPhysicalRequirementEntity(
            $res["shop_item_physical_requirement_id"],
            $item,
            $res["shop_physical_requirement_weight"] ?? null,
            $res["shop_physical_requirement_length"] ?? null,
            $res["shop_physical_requirement_width"] ?? null,
            $res["shop_physical_requirement_height"] ?? null,
            $res["shop_physical_requirement_created_at"],
            $res["shop_physical_requirement_updated_at"]
        );
    }

    /**
     * @param int $itemId
     * @return \CMW\Entity\Shop\Items\ShopItemPhysicalRequirementEntity
     */
    public function getShopItemPhysicalRequirementByItemId(int $itemId): ?ShopItemPhysicalRequirementEntity
    {
        $sql = "SELECT * FROM cmw_shops_items_physical_requirement WHERE shop_item_id = :shop_item_id";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("shop_item_id" => $itemId))) {
            return null;
        }

        $res = $res->fetch();

        $item = is_null($res["shop_item_id"]) ? null : $this->itemModel->getShopItemsById($res["shop_item_id"]);


        if ($item === null) {
            return null;
        }

        return new ShopItemPhysicalRequirementEntity(
            $res["shop_item_physical_requirement_id"],
            $item,
            $res["shop_physical_requirement_weight"] ?? null,
            $res["shop_physical_requirement_length"] ?? null,
            $res["shop_physical_requirement_width"] ?? null,
            $res["shop_physical_requirement_height"] ?? null,
            $res["shop_physical_requirement_created_at"],
            $res["shop_physical_requirement_updated_at"]
        );
    }

    public function createPhysicalRequirement(int $itemId, ?float $weight, ?float $length, ?float $width, ?float $height): int
    {
        $data = array(
            "itemId" => $itemId,
            "weight" => $weight,
            "length" => $length,
            "width" => $width,
            "height" => $height
        );

        $sql = "INSERT INTO cmw_shops_items_physical_requirement(shop_item_id, shop_physical_requirement_weight, shop_physical_requirement_length, shop_physical_requirement_width, shop_physical_requirement_height)
                VALUES (:itemId,:weight,:length,:width,:height)";


        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $id = $db->lastInsertId();
            $this->getShopItemPhysicalRequirementById($id);
            return $id;
        }
    }
}