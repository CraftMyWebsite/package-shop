<?php

namespace CMW\Model\Shop;

use CMW\Entity\Shop\ShopCategoryEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Utils\Utils;


/**
 * Class: @ShopCategoryModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopCategoriesModel extends AbstractModel
{

    /**
     * @return \CMW\Entity\Shop\ShopCategoryEntity []
     */
    public function getShopCategories(): array
    {

        $sql = "SELECT shop_category_id FROM cmw_shops_categories ORDER BY shop_category_id ASC";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($cat = $res->fetch()) {
            $toReturn[] = $this->getShopCategoryById($cat["shop_category_id"]);
        }

        return $toReturn;

    }

    public function getShopCategoryById(int $id): ?ShopCategoryEntity
    {
        $sql = "SELECT * FROM cmw_shops_categories WHERE shop_category_id = :shop_category_id";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("shop_category_id" => $id))) {
            return null;
        }

        $res = $res->fetch();

        return new ShopCategoryEntity(
            $res["shop_category_id"],
            $res["shop_category_name"],
            $res["shop_category_description"],
            $res["shop_category_slug"],
            $res["shop_image_id"] ?? null,
            $res["shop_category_created_at"],
            $res["shop_category_updated_at"]
        );
    }

    public function getShopCategoryIdBySlug(string $catSlug): int
    {
        $sql = "SELECT shop_category_id FROM cmw_shops_categories WHERE shop_category_slug = :shop_category_slug";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("shop_category_slug" => $catSlug))) {
            return 0;
        }

        $res = $res->fetch();
        if(!$res){
            return 0;
        }
        return $res['shop_category_id'] ?? 0;
    }

    public function createShopCategory(string $name, string $description): ?ShopCategoryEntity
    {
        $data = array(
            "shop_category_name" => $name,
            "shop_category_description" => $description,
            "shop_category_slug" => "NOT_DEFINED"
        );

        $sql = "INSERT INTO cmw_shops_categories(shop_category_name, shop_category_description, shop_category_slug)
                VALUES (:shop_category_name, :shop_category_description, :shop_category_slug)";


        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $id = $db->lastInsertId();
            $this->setShopCategorySlug($id, $name);
            return $this->getShopCategoryById($id);
        }

        return null;
    }

    private function setShopCategorySlug(int $id, string $name): void
    {
        $slug = $this->generateShopSlug($id, $name);

        $data = array(
            "shop_category_slug" => $slug,
            "shop_category_id" => $id,
        );

        $sql = "UPDATE cmw_shops_categories SET shop_category_slug = :shop_category_slug WHERE shop_category_id = :shop_category_id";

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        $req->execute($data);
    }
    public function generateShopSlug(int $id, string $name): string
    {
        return Utils::normalizeForSlug($name) . "-$id";
    }


}