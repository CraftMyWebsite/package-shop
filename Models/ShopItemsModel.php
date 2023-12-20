<?php

namespace CMW\Model\Shop;

use CMW\Entity\Shop\ShopItemEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Utils\Utils;


/**
 * Class: @ShopItemsModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopItemsModel extends AbstractModel
{
    private ShopCategoriesModel $shopCategoriesModel;

    public function __construct()
    {

        $this->shopCategoriesModel = new ShopCategoriesModel();
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

        $category = is_null($res["shop_category_id"]) ? null : $this->shopCategoriesModel->getShopCategoryById($res["shop_category_id"]);

        return new ShopItemEntity(
            $res["shop_item_id"],
            $category ?? null,
            $res["shop_item_name"] ?? null,
            $res["shop_item_description"],
            $res["shop_item_short_description"],
            $res["shop_item_slug"],
            $res["shop_image_id"] ?? null,
            $res["shop_item_type"],
            $res["shop_item_default_stock"] ?? null,
            $res["shop_item_current_stock"] ?? null,
            $res["shop_item_price"] ?? null,
            $res["shop_item_by_order_limit"] ?? null,
            $res["shop_item_global_limit"] ?? null,
            $res["shop_item_user_limit"] ?? null,
            $res["shop_item_created_at"],
            $res["shop_item_updated_at"]
        );
    }

    /**
     * @return \CMW\Entity\Shop\ShopItemEntity []
     */
    public function getShopItems(): array
    {

        $sql = "SELECT shop_item_id FROM cmw_shops_items WHERE shop_item_archived = 0 ORDER BY shop_item_id DESC";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($item = $res->fetch()) {
            $toReturn[] = $this->getShopItemsById($item["shop_item_id"]);
        }

        return $toReturn;

    }

    /**
     * @return \CMW\Entity\Shop\ShopItemEntity []
     */
    public function getShopArchivedItems(): array
    {

        $sql = "SELECT shop_item_id FROM cmw_shops_items WHERE shop_item_archived = 1 ORDER BY shop_item_id DESC";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($item = $res->fetch()) {
            $toReturn[] = $this->getShopItemsById($item["shop_item_id"]);
        }

        return $toReturn;

    }

    /**
     * @return \CMW\Entity\Shop\ShopItemEntity []
     */
    public function getShopItemByCat(int $id): array
    {
        $sql = "SELECT shop_item_id FROM cmw_shops_items WHERE shop_category_id = :shop_category_id AND shop_item_archived = 0";
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

    /**
     * @return \CMW\Entity\Shop\ShopItemEntity []
     */
    public function getShopItemByCatSlug(string $catSlug): array
    {
        $catId = $this->shopCategoriesModel->getShopCategoryIdBySlug($catSlug);
        $sql = "SELECT shop_item_id FROM cmw_shops_items WHERE shop_category_id = :shop_category_id  AND shop_item_archived = 0";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("shop_category_id" => $catId))) {
            return array();
        }

        $toReturn = array();

        while ($items = $res->fetch()) {
            $toReturn[] = $this->getShopItemsById($items["shop_item_id"]);
        }

        return $toReturn;
    }

    public function getShopItemIdBySlug(string $itemSlug): int
    {
        $sql = "SELECT shop_item_id FROM cmw_shops_items WHERE shop_item_slug = :shop_item_slug";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("shop_item_slug" => $itemSlug))) {
            return 0;
        }

        $res = $res->fetch();
        if(!$res){
            return 0;
        }
        return $res['shop_item_id'] ?? 0;
    }

    public function createShopItem(?string $name, ?string $shortDesc, ?string $category, string $description, int $type, ?int $stock, float $price, ?int $byOrderLimit, ?int $globalLimit, ?int $userLimit): int
    {
        $data = array(
            "shop_item_name" => $name,
            "shop_category_id" => $category,
            "shop_item_short_description" => $shortDesc,
            "shop_item_description" => $description,
            "shop_category_slug" => "NOT_DEFINED",
            "shop_item_type" => $type,
            "shop_item_default_stock" => $stock,
            "shop_item_current_stock" => $stock,
            "shop_item_price" => $price,
            "shop_item_by_order_limit" => $byOrderLimit,
            "shop_item_global_limit" => $globalLimit,
            "shop_item_user_limit" => $userLimit,
        );

        $sql = "INSERT INTO cmw_shops_items(shop_item_name, shop_item_short_description, shop_category_id, shop_item_description, shop_item_slug, shop_item_type, shop_item_default_stock, shop_item_current_stock, shop_item_price, shop_item_by_order_limit, shop_item_global_limit, shop_item_user_limit )
                VALUES (:shop_item_name, :shop_item_short_description, :shop_category_id, :shop_item_description, :shop_category_slug, :shop_item_type, :shop_item_default_stock, :shop_item_current_stock, :shop_item_price, :shop_item_by_order_limit, :shop_item_global_limit, :shop_item_user_limit )";


        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $id = $db->lastInsertId();
            $this->setShopItemSlug($id, $name);
            return $id;
        }

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

    public function isArchivedItem(int $itemId): bool
    {
        $sql = "SELECT shop_item_id FROM cmw_shops_items WHERE shop_item_archived = 0 AND shop_item_id =:shop_item_id;";
        $data['shop_item_id'] = $itemId;
        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if(!$req->execute($data)){
            return false;
        }

        $res = $req->fetch();

        if (!$res){
            return true;
        }

        return false;
    }

    public function itemStillExist(int $itemId): bool
    {
        $sql = "SELECT shop_item_id FROM cmw_shops_items WHERE shop_item_id =:shop_item_id;";
        $data['shop_item_id'] = $itemId;
        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if(!$req->execute($data)){
            return false;
        }

        $res = $req->fetch();

        if (!$res){
            return true;
        }

        return false;
    }

    public function itemNotInStock(int $itemId): bool
    {
        $sql = "SELECT shop_item_id FROM cmw_shops_items WHERE shop_item_id = :shop_item_id AND (shop_item_current_stock > 0 OR shop_item_current_stock IS NULL);";
        $data['shop_item_id'] = $itemId;
        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute($data)) {
            return true;
        }

        $res = $req->fetch();

        if (!$res) {
            return true;
        }

        return false;
    }

    public function getItemGlobalLimit(int $itemId): int
    {
        $sql = "SELECT shop_item_global_limit FROM cmw_shops_items WHERE shop_item_id = :shop_item_id;";

        $data = ["shop_item_id" => $itemId];

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute($data)) {
            return 0;
        }

        return $res->fetch(0)['shop_item_global_limit'] ?? 0;
    }

    public function getItemByOrderLimit(int $itemId): int
    {
        $sql = "SELECT shop_item_by_order_limit FROM cmw_shops_items WHERE shop_item_id = :shop_item_id;";

        $data = ["shop_item_id" => $itemId];

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute($data)) {
            return 0;
        }

        return $res->fetch(0)['shop_item_by_order_limit'] ?? 0;
    }

    public function itemHaveUserLimit(int $itemId): bool
    {
        $sql = "SELECT shop_item_id FROM cmw_shops_items WHERE shop_item_id = :shop_item_id AND shop_item_user_limit IS NOT NULL;";
        $data['shop_item_id'] = $itemId;
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

    public function getItemUserLimit(int $itemId) : int
    {
        $sql = "SELECT shop_item_user_limit FROM cmw_shops_items WHERE shop_item_id = :shop_item_id";
        $data['shop_item_id'] = $itemId;
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        if (!$req->execute($data)) {
            return 0;
        }
        $res = $req->fetch();
        return $res['shop_item_user_limit']?? 0;
    }

}