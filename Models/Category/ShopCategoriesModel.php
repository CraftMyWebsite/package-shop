<?php

namespace CMW\Model\Shop\Category;

use CMW\Entity\Shop\Categories\ShopCategoryEntity;
use CMW\Entity\Shop\Categories\ShopSubCategoryEntity;
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
     * @return \CMW\Entity\Shop\Categories\ShopCategoryEntity []
     */
    public function getShopCategories(): array
    {
        $sql = 'SELECT shop_category_id FROM cmw_shops_categories WHERE shop_sub_category_id IS NULL ORDER BY shop_category_id ASC';
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($cat = $res->fetch()) {
            $toReturn[] = $this->getShopCategoryById($cat['shop_category_id']);
        }

        return $toReturn;
    }

    public function getShopCategoryById(int $id): ?ShopCategoryEntity
    {
        $sql = 'SELECT * FROM cmw_shops_categories WHERE shop_category_id = :shop_category_id';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('shop_category_id' => $id))) {
            return null;
        }

        $res = $res->fetch();

        $element = is_null($res['shop_sub_category_id']) ? null : $this->getShopCategoryById($res['shop_sub_category_id']);

        return new ShopCategoryEntity(
            $res['shop_category_id'],
            $res['shop_category_name'],
            $res['shop_category_icon'] ?? null,
            $res['shop_category_description'] ?? null,
            $res['shop_category_slug'],
            $element,
            $res['shop_category_created_at'],
            $res['shop_category_updated_at']
        );
    }

    public function getShopCategoryIdBySlug(string $catSlug): int
    {
        $sql = 'SELECT shop_category_id FROM cmw_shops_categories WHERE shop_category_slug = :shop_category_slug';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('shop_category_slug' => $catSlug))) {
            return 0;
        }

        $res = $res->fetch();
        if (!$res) {
            return 0;
        }
        return $res['shop_category_id'] ?? 0;
    }

    public function getAllChildrenCategoryIds(int $parentId): array
    {
        $db = DatabaseManager::getInstance();

        $sql = 'SELECT shop_category_id, shop_sub_category_id FROM cmw_shops_categories';
        $res = $db->query($sql);
        $allCategories = $res->fetchAll(\PDO::FETCH_ASSOC);

        return $this->recursiveChildSearch($allCategories, $parentId);
    }

    private function recursiveChildSearch(array $all, int $parentId): array
    {
        $children = [];

        foreach ($all as $cat) {
            if ((int)($cat['shop_sub_category_id'] ?? 0) === $parentId) {
                $children[] = (int)$cat['shop_category_id'];
                $children = array_merge($children, $this->recursiveChildSearch($all, (int)$cat['shop_category_id']));
            }
        }

        return $children;
    }


    public function createShopCategory(string $name, string $description, string $icon): ?ShopCategoryEntity
    {
        $data = array(
            'shop_category_name' => $name,
            'shop_category_description' => $description,
            'shop_category_icon' => $icon,
            'shop_category_slug' => 'NOT_DEFINED'
        );

        $sql = 'INSERT INTO cmw_shops_categories(shop_category_name, shop_category_description, shop_category_slug, shop_category_icon)
                VALUES (:shop_category_name, :shop_category_description, :shop_category_slug, :shop_category_icon)';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $id = $db->lastInsertId();
            $this->setShopCategorySlug($id, $name);
            return $this->getShopCategoryById($id);
        }

        return null;
    }

    public function createShopSubCategory(string $name, string $description, string $icon, int $reattached_Id): ?ShopCategoryEntity
    {
        $data = array(
            'shop_category_name' => $name,
            'shop_category_description' => $description,
            'shop_category_icon' => $icon,
            'shop_category_slug' => 'NOT_DEFINED',
            'shop_sub_category_id' => $reattached_Id
        );

        $sql = 'INSERT INTO cmw_shops_categories(shop_category_name, shop_category_description, shop_category_slug, shop_category_icon, shop_sub_category_id)
                VALUES (:shop_category_name, :shop_category_description, :shop_category_slug, :shop_category_icon, :shop_sub_category_id)';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $id = $db->lastInsertId();
            $this->setShopCategorySlug($id, $name);
            return $this->getShopCategoryById($id);
        }

        return null;
    }

    public function editCategory(string $name, string $description, string $icon, ?int $reattached_Id, int $catId): ?ShopCategoryEntity
    {
        $data = array(
            'shop_category_name' => $name,
            'shop_category_description' => $description,
            'shop_category_icon' => $icon,
            'shop_category_slug' => 'NOT_DEFINED',
            'shop_sub_category_id' => $reattached_Id,
            'shop_category_id' => $catId
        );

        $sql = 'UPDATE cmw_shops_categories SET shop_category_name=:shop_category_name, shop_category_description=:shop_category_description, shop_category_slug=:shop_category_slug, shop_category_icon=:shop_category_icon, shop_sub_category_id=:shop_sub_category_id WHERE shop_category_id=:shop_category_id';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $this->setShopCategorySlug($catId, $name);
            return $this->getShopCategoryById($catId);
        }

        return null;
    }

    private function setShopCategorySlug(int $id, string $name): void
    {
        $slug = $this->generateShopSlug($id, $name);

        $data = array(
            'shop_category_slug' => $slug,
            'shop_category_id' => $id,
        );

        $sql = 'UPDATE cmw_shops_categories SET shop_category_slug = :shop_category_slug WHERE shop_category_id = :shop_category_id';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        $req->execute($data);
    }

    public function generateShopSlug(int $id, string $name): string
    {
        return Utils::normalizeForSlug($name) . "-$id";
    }

    /**
     * @param int $catId
     * @return ShopCategoryEntity[]
     */
    public function getSubCatByCat(int $catId): array
    {
        $sql = 'SELECT shop_category_id FROM cmw_shops_categories WHERE shop_sub_category_id = :cat_id';
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(['cat_id' => $catId])) {
            return [];
        }

        $toReturn = [];

        while ($cat = $res->fetch()) {
            $toReturn[] = $this->getShopCategoryById($cat['shop_category_id']);
        }

        return $toReturn;
    }

    public function deleteShopCat(int $id): bool
    {
        $sql = 'DELETE FROM cmw_shops_categories WHERE shop_category_id = :shop_category_id';

        $db = DatabaseManager::getInstance();

        return $db->prepare($sql)->execute(array('shop_category_id' => $id));
    }

    /**
     * @param int $catId
     * @return ShopSubCategoryEntity[]
     */
    public function getSubsCat(int $catId): array
    {
        return $this->getSubCatRecursively($catId, 1);
    }

    /**
     * @param int $catId
     * @param int $depth
     * @return ShopSubCategoryEntity[]
     */
    private function getSubCatRecursively(int $catId, int $depth): array
    {
        $toReturn = [];
        $subCat = $this->getSubCatByCat($catId);

        foreach ($subCat as $subCats) {
            $toReturn[] = new ShopSubCategoryEntity($subCats, $depth);

            $subToReturn = $this->getSubCatRecursively($subCats->getId(), $depth + 1);
            $toReturn = [...$toReturn, ...$subToReturn];
        }

        return $toReturn;
    }

    /**
     * @param int $catId
     * @return int
     */
    public function countItemsByCatId(int $catId): int
    {
        $sql = 'SELECT COUNT(cmw_shops_items.shop_item_id) AS `count` FROM cmw_shops_items WHERE shop_category_id = :shop_category_id';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(['shop_category_id' => $catId])) {
            return 0;
        }

        return $res->fetch()['count'] ?? 0;
    }
}
