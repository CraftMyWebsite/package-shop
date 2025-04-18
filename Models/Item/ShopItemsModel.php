<?php

namespace CMW\Model\Shop\Item;

use CMW\Entity\Shop\Items\ShopItemEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Model\Shop\Category\ShopCategoriesModel;
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

    /**
     * @param int $id
     * @return \CMW\Entity\Shop\Items\ShopItemEntity
     */
    public function getShopItemsById(int $id): ?ShopItemEntity
    {
        $sql = 'SELECT * FROM cmw_shops_items WHERE shop_item_id = :shop_item_id';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('shop_item_id' => $id))) {
            return null;
        }

        $res = $res->fetch();

        $category = is_null($res['shop_category_id']) ? null : $this->shopCategoriesModel->getShopCategoryById($res['shop_category_id']);

        return new ShopItemEntity(
            $res['shop_item_id'],
            $category ?? null,
            $res['shop_item_name'] ?? null,
            $res['shop_item_description'],
            $res['shop_item_short_description'],
            $res['shop_item_slug'],
            $res['shop_image_id'] ?? null,
            $res['shop_item_type'],
            $res['shop_item_default_stock'] ?? null,
            $res['shop_item_current_stock'] ?? null,
            $res['shop_item_price'] ?? null,
            $res['shop_item_price_type'],
            $res['shop_item_by_order_limit'] ?? null,
            $res['shop_item_global_limit'] ?? null,
            $res['shop_item_user_limit'] ?? null,
            $res['shop_item_draft'],
            $res['shop_item_created_at'],
            $res['shop_item_updated_at'],
            $res['shop_item_archived'],
            $res['shop_item_archived_reason']
        );
    }

    /**
     * @return \CMW\Entity\Shop\Items\ShopItemEntity []
     */
    public function getPublicShopItems(): array
    {
        $sql = 'SELECT shop_item_id FROM cmw_shops_items WHERE shop_item_archived = 0 AND shop_item_draft = 0 ORDER BY shop_item_id DESC';
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($item = $res->fetch()) {
            $toReturn[] = $this->getShopItemsById($item['shop_item_id']);
        }

        return $toReturn;
    }

    /**
     * @return \CMW\Entity\Shop\Items\ShopItemEntity []
     */
    public function getAdminShopItems(): array
    {
        $sql = 'SELECT shop_item_id FROM cmw_shops_items WHERE shop_item_archived = 0 ORDER BY shop_item_id DESC';
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($item = $res->fetch()) {
            $toReturn[] = $this->getShopItemsById($item['shop_item_id']);
        }

        return $toReturn;
    }

    /**
     * @return \CMW\Entity\Shop\Items\ShopItemEntity []
     */
    public function getShopArchivedItems(): array
    {
        $sql = 'SELECT shop_item_id FROM cmw_shops_items WHERE shop_item_archived = 1 ORDER BY shop_item_id DESC';
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($item = $res->fetch()) {
            $toReturn[] = $this->getShopItemsById($item['shop_item_id']);
        }

        return $toReturn;
    }

    /**
     * @return \CMW\Entity\Shop\Items\ShopItemEntity []
     */
    public function getAdminShopItemByCat(int $id): array
    {
        $sql = 'SELECT shop_item_id FROM cmw_shops_items WHERE shop_category_id = :shop_category_id AND shop_item_archived = 0';
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('shop_category_id' => $id))) {
            return array();
        }

        $toReturn = array();

        while ($items = $res->fetch()) {
            $toReturn[] = $this->getShopItemsById($items['shop_item_id']);
        }

        return $toReturn;
    }

    /**
     * @return \CMW\Entity\Shop\Items\ShopItemEntity []
     */
    public function getPublicShopItemByCatSlug(string $catSlug): array
    {
        $catId = $this->shopCategoriesModel->getShopCategoryIdBySlug($catSlug);
        $sql = 'SELECT shop_item_id FROM cmw_shops_items WHERE shop_category_id = :shop_category_id  AND shop_item_archived = 0 AND shop_item_draft = 0 ORDER BY shop_item_id DESC';
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('shop_category_id' => $catId))) {
            return array();
        }

        $toReturn = array();

        while ($items = $res->fetch()) {
            $toReturn[] = $this->getShopItemsById($items['shop_item_id']);
        }

        return $toReturn;
    }

    /**
     * @return \CMW\Entity\Shop\Items\ShopItemEntity []
     */
    public function getAdminShopItemByCatSlug(string $catSlug): array
    {
        $catId = $this->shopCategoriesModel->getShopCategoryIdBySlug($catSlug);
        $sql = 'SELECT shop_item_id FROM cmw_shops_items WHERE shop_category_id = :shop_category_id  AND shop_item_archived = 0 ORDER BY shop_item_id DESC';
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('shop_category_id' => $catId))) {
            return array();
        }

        $toReturn = array();

        while ($items = $res->fetch()) {
            $toReturn[] = $this->getShopItemsById($items['shop_item_id']);
        }

        return $toReturn;
    }

    public function getPublicShopItemIdBySlug(string $itemSlug): int
    {
        $sql = 'SELECT shop_item_id FROM cmw_shops_items WHERE shop_item_slug = :shop_item_slug AND shop_item_draft = 0';
        $db = DatabaseManager::getInstance();
        $res = $db->prepare($sql);

        if (!$res->execute(array('shop_item_slug' => $itemSlug))) {
            return 0;
        }

        $res = $res->fetch();

        if (!$res) {
            return 0;
        }

        return $res['shop_item_id'] ?? 0;
    }

    public function getAdminShopItemIdBySlug(string $itemSlug): int
    {
        $sql = 'SELECT shop_item_id FROM cmw_shops_items WHERE shop_item_slug = :shop_item_slug';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('shop_item_slug' => $itemSlug))) {
            return 0;
        }

        $res = $res->fetch();
        if (!$res) {
            return 0;
        }
        return $res['shop_item_id'] ?? 0;
    }

    /**
     * @return \CMW\Entity\Shop\Items\ShopItemEntity []
     */
    public function getShopItemByVirtualMethodVarName(string $varName): array
    {
        $sql = 'SELECT csi.shop_item_id
FROM cmw_shops_items csi
INNER JOIN cmw_shops_items_virtual_method csivm
ON csi.shop_item_id = csivm.shop_item_id
WHERE csivm.shops_items_virtual_method_var_name = :varName AND csi.shop_item_archived = 0
ORDER BY csi.shop_item_price ASC;';
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('varName' => $varName))) {
            return array();
        }

        $toReturn = array();

        while ($items = $res->fetch()) {
            $toReturn[] = $this->getShopItemsById($items['shop_item_id']);
        }

        return $toReturn;
    }

    public function createShopItem(?string $name, ?string $shortDesc, ?string $category, string $description, int $type, ?int $stock, float $price, string $priceType, ?int $byOrderLimit, ?int $globalLimit, ?int $userLimit, int $draft): int
    {
        $data = array(
            'shop_item_name' => $name,
            'shop_category_id' => $category,
            'shop_item_short_description' => $shortDesc,
            'shop_item_description' => $description,
            'shop_category_slug' => 'NOT_DEFINED',
            'shop_item_type' => $type,
            'shop_item_default_stock' => $stock,
            'shop_item_current_stock' => $stock,
            'shop_item_price' => $price,
            'shop_item_price_type' => $priceType,
            'shop_item_by_order_limit' => $byOrderLimit,
            'shop_item_global_limit' => $globalLimit,
            'shop_item_user_limit' => $userLimit,
            'shop_item_draft' => $draft,
        );

        $sql = 'INSERT INTO cmw_shops_items(shop_item_name, shop_item_short_description, shop_category_id, shop_item_description, shop_item_slug, shop_item_type, shop_item_default_stock, shop_item_current_stock, shop_item_price, shop_item_price_type, shop_item_by_order_limit, shop_item_global_limit, shop_item_user_limit, shop_item_draft )
                VALUES (:shop_item_name, :shop_item_short_description, :shop_category_id, :shop_item_description, :shop_category_slug, :shop_item_type, :shop_item_default_stock, :shop_item_current_stock, :shop_item_price, :shop_item_price_type, :shop_item_by_order_limit, :shop_item_global_limit, :shop_item_user_limit, :shop_item_draft )';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $id = $db->lastInsertId();
            $this->setShopItemSlug($id, $name);
            return $id;
        }
    }

    public function editShopItem(int $itemId, ?string $name, ?string $shortDesc, ?string $category, string $description, int $type, ?int $stock, float $price, string $priceType, ?int $byOrderLimit, ?int $globalLimit, ?int $userLimit, int $draft): int
    {
        $data = array(
            'shop_item_id' => $itemId,
            'shop_item_name' => $name,
            'shop_category_id' => $category,
            'shop_item_short_description' => $shortDesc,
            'shop_item_description' => $description,
            'shop_category_slug' => 'NOT_DEFINED',
            'shop_item_type' => $type,
            'shop_item_default_stock' => $stock,
            'shop_item_current_stock' => $stock,
            'shop_item_price' => $price,
            'shop_item_price_type' => $priceType,
            'shop_item_by_order_limit' => $byOrderLimit,
            'shop_item_global_limit' => $globalLimit,
            'shop_item_user_limit' => $userLimit,
            'shop_item_draft' => $draft,
        );

        $sql = 'UPDATE cmw_shops_items SET shop_item_name = :shop_item_name,
        shop_category_id = :shop_category_id,
        shop_item_short_description = :shop_item_short_description,
        shop_item_description = :shop_item_description,
        shop_item_slug = :shop_category_slug,
        shop_item_type = :shop_item_type,
        shop_item_default_stock = :shop_item_default_stock,
        shop_item_current_stock = :shop_item_current_stock,
        shop_item_price =:shop_item_price,
        shop_item_price_type =:shop_item_price_type,
        shop_item_by_order_limit = :shop_item_by_order_limit,
        shop_item_global_limit = :shop_item_global_limit,
        shop_item_user_limit = :shop_item_user_limit,
        shop_item_draft = :shop_item_draft
        WHERE shop_item_id = :shop_item_id';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $this->setShopItemSlug($itemId, $name);
            return $itemId;
        }
    }

    private function setShopItemSlug(int $id, string $name): void
    {
        $slug = $this->generateShopSlug($id, $name);

        $data = array(
            'shop_item_slug' => $slug,
            'shop_item_id' => $id,
        );

        $sql = 'UPDATE cmw_shops_items SET shop_item_slug = :shop_item_slug WHERE shop_item_id = :shop_item_id';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        $req->execute($data);
    }

    public function archiveItem(int $id, int $reason): void
    {
        $data = array(
            'shop_item_id' => $id,
            'shop_item_archived_reason' => $reason
        );

        $sql = 'UPDATE cmw_shops_items SET shop_item_archived = 1, shop_item_archived_reason = :shop_item_archived_reason WHERE shop_item_id = :shop_item_id';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        $req->execute($data);
    }

    public function unarchivedItem(int $id): void
    {
        $data = array(
            'shop_item_id' => $id
        );

        $sql = 'UPDATE cmw_shops_items SET shop_item_archived = 0, shop_item_archived_reason = 0 WHERE shop_item_id = :shop_item_id';

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
        $sql = 'DELETE FROM cmw_shops_items WHERE shop_item_id = :shop_item_id';

        $db = DatabaseManager::getInstance();

        return $db->prepare($sql)->execute(array('shop_item_id' => $id));
    }

    public function isArchivedItem(int $itemId): bool
    {
        $sql = 'SELECT shop_item_id FROM cmw_shops_items WHERE shop_item_archived = 0 AND shop_item_id =:shop_item_id;';
        $data['shop_item_id'] = $itemId;
        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute($data)) {
            return false;
        }

        $res = $req->fetch();

        if (!$res) {
            return true;
        }

        return false;
    }

    public function itemStillExist(int $itemId): bool
    {
        $sql = 'SELECT shop_item_id FROM cmw_shops_items WHERE shop_item_id =:shop_item_id;';
        $data['shop_item_id'] = $itemId;
        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute($data)) {
            return false;
        }

        $res = $req->fetch();

        if (!$res) {
            return true;
        }

        return false;
    }

    public function itemNotInStock(int $itemId): bool
    {
        $sql = 'SELECT shop_item_id FROM cmw_shops_items WHERE shop_item_id = :shop_item_id AND (shop_item_current_stock > 0 OR shop_item_current_stock IS NULL);';
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
        $sql = 'SELECT shop_item_global_limit FROM cmw_shops_items WHERE shop_item_id = :shop_item_id;';

        $data = ['shop_item_id' => $itemId];

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute($data)) {
            return 0;
        }

        return $res->fetch(0)['shop_item_global_limit'] ?? 0;
    }

    public function getItemByOrderLimit(int $itemId): int
    {
        $sql = 'SELECT shop_item_by_order_limit FROM cmw_shops_items WHERE shop_item_id = :shop_item_id;';

        $data = ['shop_item_id' => $itemId];

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute($data)) {
            return 0;
        }

        return $res->fetch(0)['shop_item_by_order_limit'] ?? 0;
    }

    public function getItemCurrentStock(int $itemId): int
    {
        $sql = 'SELECT shop_item_current_stock FROM cmw_shops_items WHERE shop_item_id = :shop_item_id;';

        $data = ['shop_item_id' => $itemId];

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute($data)) {
            return 0;
        }

        return $res->fetch(0)['shop_item_current_stock'] ?? 999999;
    }

    public function itemHaveUserLimit(int $itemId): bool
    {
        $sql = 'SELECT shop_item_id FROM cmw_shops_items WHERE shop_item_id = :shop_item_id AND shop_item_user_limit IS NOT NULL;';
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

    public function itemHaveGlobalLimit(int $itemId): bool
    {
        $sql = 'SELECT shop_item_id FROM cmw_shops_items WHERE shop_item_id = :shop_item_id AND shop_item_global_limit IS NOT NULL;';
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

    public function itemHaveByOrderLimit(int $itemId): bool
    {
        $sql = 'SELECT shop_item_id FROM cmw_shops_items WHERE shop_item_id = :shop_item_id AND shop_item_by_order_limit IS NOT NULL;';
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

    public function getItemUserLimit(int $itemId): int
    {
        $sql = 'SELECT shop_item_user_limit FROM cmw_shops_items WHERE shop_item_id = :shop_item_id';
        $data['shop_item_id'] = $itemId;
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        if (!$req->execute($data)) {
            return 0;
        }
        $res = $req->fetch();
        return $res['shop_item_user_limit'] ?? 0;
    }

    public function decreaseStock(int $itemId, int $stock): void
    {
        $data = array(
            'shop_item_current_stock' => $stock,
            'shop_item_id' => $itemId
        );

        $sql = 'UPDATE cmw_shops_items SET shop_item_current_stock = :shop_item_current_stock WHERE shop_item_id = :shop_item_id';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        $req->execute($data);
    }

    /**
     * @param string $search
     * @return \CMW\Entity\Shop\Items\ShopItemEntity|null
     */
    public function getItemByResearch(string $search): ?array
    {
        $sql = 'SELECT shop_item_id FROM cmw_shops_items WHERE ( shop_item_name LIKE :search  OR shop_item_description LIKE :search1 OR shop_item_short_description LIKE :search2 ) AND shop_item_archived = 0 AND shop_item_draft = 0 ORDER BY shop_item_id DESC;';
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('search' => '%'.$search.'%', 'search1' => '%'.$search.'%', 'search2' => '%'.$search.'%'))) {
            return [];
        }

        $toReturn = array();

        while ($item = $res->fetch()) {
            $toReturn[] = $this->getShopItemsById($item['shop_item_id']);
        }

        return $toReturn;
    }
}
