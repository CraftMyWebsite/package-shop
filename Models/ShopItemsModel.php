<?php

namespace CMW\Model\Shop;

use CMW\Entity\Shop\ShopItemEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Manager\Uploads\ImagesManager;
use CMW\Utils\Utils;


/**
 * Class: @ShopItemsModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopItemsModel extends AbstractModel
{

    /**
     * @return \CMW\Entity\Shop\ShopItemEntity []
     */
    public function getShopItems(): array
    {

        $sql = "SELECT shop_category_id FROM cmw_shops_items ORDER BY shop_item_id ASC";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($cat = $res->fetch()) {
            $toReturn[] = $this->getShopItemsByCategoryId($cat["shop_category_id"]);
        }

        return $toReturn;

    }

    public function getShopItemsById(int $id): ?ShopItemEntity
    {
        $sql = "SELECT * FROM cmw_shops_items WHERE shop_item_id = :shop_item_id";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("shop_item_id" => $id))) {
            return null;
        }

        $res = $res->fetch();

        return new ShopItemEntity(
            $res["shop_item_id"],
            $res["shop_category_id"] ?? null,
            $res["shop_item_name"] ?? null,
            $res["shop_item_description"],
            $res["shop_item_slug"],
            $res["shop_image_id"] ?? null,
            $res["shop_item_type"],
            $res["shop_item_default_stock"] ?? null,
            $res["shop_item_current_stock"] ?? null,
            $res["shop_item_price"] ?? null,
            $res["shop_item_global_limit"] ?? null,
            $res["shop_item_user_limit"] ?? null,
            $res["shop_item_created_at"],
            $res["shop_item_updated_at"]
        );
    }

    /**
     * @return \CMW\Entity\Shop\ShopItemEntity []
     */
    public function getShopItemByCat(int $id): array
    {
        $sql = "SELECT shop_item_id FROM cmw_shops_items WHERE shop_category_id = :shop_category_id";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("shop_category_id" => $id))) {
            return array();
        }

        $toReturn = array();

        while ($items = $res->fetch()) {
            $toReturn[] = $this->getShopItemsById($items["shop_item_id"]);
        }

        return $toReturn;
    }

    public function createShopItem(?string $name, ?string $category, string $description, int $type, ?int $stock, float $price, ?int $globalLimit, ?int $userLimit): ?ShopItemEntity
    {
        $data = array(
            "shop_item_name" => $name,
            "shop_category_id" => $category,
            "shop_item_description" => $description,
            "shop_category_slug" => "NOT_DEFINED",
            "shop_item_type" => $type,
            "shop_item_default_stock" => $stock,
            "shop_item_current_stock" => $stock,
            "shop_item_price" => $price,
            "shop_item_global_limit" => $globalLimit,
            "shop_item_user_limit" => $userLimit,
        );

        $sql = "INSERT INTO cmw_shops_items(shop_item_name, shop_category_id, shop_item_description, shop_item_slug, shop_item_type, shop_item_default_stock, shop_item_current_stock, shop_item_price, shop_item_global_limit, shop_item_user_limit )
                VALUES (:shop_item_name, :shop_category_id, :shop_item_description, :shop_category_slug, :shop_item_type, :shop_item_default_stock, :shop_item_current_stock, :shop_item_price, :shop_item_global_limit, :shop_item_user_limit )";


        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $id = $db->lastInsertId();
            $this->setShopItemSlug($id, $name);
            return $this->getShopItemsById($id);
        }

        return null;
    }

    private function setShopItemSlug(int $id, string $name): void
    {
        $slug = $this->generateShopSlug($id, $name);

        $data = array(
            "shop_item_slug" => $slug,
            "shop_item_id" => $id,
        );

        $sql = "UPDATE cmw_shops_items SET shop_item_slug = :shop_item_slug WHERE shop_item_id = :shop_item_id";

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        $req->execute($data);
    }

    public function generateShopSlug(int $id, string $name): string
    {
        return Utils::normalizeForSlug($name) . "-$id";
    }

    public function deleteShopItem(int $id): bool
    {
        $sql = "DELETE FROM cmw_shops_items WHERE shop_item_id = :shop_item_id";

        $db = DatabaseManager::getInstance();

        return $db->prepare($sql)->execute(array("shop_item_id" => $id));
    }
}