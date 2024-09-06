<?php

namespace CMW\Model\Shop\Item;

use CMW\Controller\Shop\Admin\Item\ShopItemsController;
use CMW\Entity\Shop\Items\ShopItemVirtualMethodEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;

/**
 * Class: @ShopItemsVirtualMethodModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopItemsVirtualMethodModel extends AbstractModel
{
    /**
     * @param int $id
     * @return \CMW\Entity\Shop\Items\ShopItemVirtualMethodEntity|null
     */
    public function getVirtualItemMethodById(int $id): ?ShopItemVirtualMethodEntity
    {
        $sql = 'SELECT * FROM cmw_shops_items_virtual_method WHERE shops_items_virtual_method_id = :shops_items_virtual_method_id';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('shops_items_virtual_method_id' => $id))) {
            return null;
        }

        $res = $res->fetch();

        if (!$res) {
            return null;
        }

        $virtualMethod = ShopItemsController::getInstance()->getVirtualItemsMethodsByVarName($res['shops_items_virtual_method_var_name']);
        $item = ShopItemsModel::getInstance()->getShopItemsById($res['shop_item_id']);

        return new ShopItemVirtualMethodEntity(
            $res['shops_items_virtual_method_id'],
            $virtualMethod,
            $item,
            $res['shops_items_virtual_requirement_created_at'],
            $res['shops_items_virtual_requirement_updated_at']
        );
    }

    /**
     * @param int $id
     * @return \CMW\Entity\Shop\Items\ShopItemVirtualMethodEntity|null
     */
    public function getVirtualItemMethodByItemId(int $itemId): ?ShopItemVirtualMethodEntity
    {
        $sql = 'SELECT * FROM cmw_shops_items_virtual_method WHERE shop_item_id = :shop_item_id';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('shop_item_id' => $itemId))) {
            return null;
        }

        $res = $res->fetch();

        if (!$res) {
            return null;
        }

        $virtualMethod = ShopItemsController::getInstance()->getVirtualItemsMethodsByVarName($res['shops_items_virtual_method_var_name']);
        $item = ShopItemsModel::getInstance()->getShopItemsById($itemId);

        return new ShopItemVirtualMethodEntity(
            $res['shops_items_virtual_method_id'],
            $virtualMethod,
            $item,
            $res['shops_items_virtual_requirement_created_at'],
            $res['shops_items_virtual_requirement_updated_at']
        );
    }

    /**
     * @param string $varName
     * @param int $itemId
     * @return ?\CMW\Entity\Shop\Items\ShopItemVirtualMethodEntity|null
     */
    public function insertMethod(string $varName, int $itemId): ?ShopItemVirtualMethodEntity
    {
        $data = array(
            'varName' => $varName,
            'itemId' => $itemId
        );

        $sql = 'INSERT INTO cmw_shops_items_virtual_method 
                            (shops_items_virtual_method_var_name, shop_item_id) 
                            VALUES (:varName, :itemId) ';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $id = $db->lastInsertId();
            return $this->getVirtualItemMethodById($id);
        }

        return null;
    }

    /**
     * @param string $varName
     * @param int $itemId
     * @return ?\CMW\Entity\Shop\Items\ShopItemVirtualMethodEntity|null
     */
    public function updateMethod(string $varName, int $itemId): ?ShopItemVirtualMethodEntity
    {
        $data = array(
            'varName' => $varName,
            'itemId' => $itemId
        );

        $sql = 'UPDATE cmw_shops_items_virtual_method SET shops_items_virtual_method_var_name = :varName WHERE shop_item_id = :itemId';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            return $this->getVirtualItemMethodByItemId($itemId);
        }

        return null;
    }

    public function clearMethod(int $itemId): bool
    {
        $data = ['shop_item_id' => $itemId];

        $sql = 'DELETE FROM cmw_shops_items_virtual_method WHERE shop_item_id = :shop_item_id';

        $db = DatabaseManager::getInstance();

        return $db->prepare($sql)->execute($data);
    }
}
