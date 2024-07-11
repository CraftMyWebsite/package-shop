<?php

namespace CMW\Model\Shop\Item;

use CMW\Entity\Shop\Items\ShopItemEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Package\AbstractModel;


/**
 * Class: @ShopItemsVirtualRequirementModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopItemsVirtualRequirementModel extends AbstractModel
{
    /**
     * @param string $key
     * @param ?int $itemId
     * @return string|null
     */
    public function getSetting(string $key, ?int $itemId): ?string
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare('SELECT shops_items_virtual_requirement_value FROM cmw_shops_items_virtual_requirement 
                                          WHERE shops_items_virtual_requirement_key = :key');

        if (is_null($itemId)) {
            return null;
        }

        if (!$req->execute(['key' => $key.$itemId])) {
            return null;
        }

        $res = $req->fetch();

        if (!$res) {
            return null;
        }

        return $res['shops_items_virtual_requirement_value'];
    }

    /**
     * @return array
     */
    public function getAllSettingsForItem($itemId): array
    {

        $sql = "SELECT * FROM cmw_shops_items_virtual_requirement WHERE shops_items_virtual_requirement_key LIKE :key";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(['key' => "%".$itemId."%"])) {
            return array();
        }

        $toReturn = array();

        while ($item = $res->fetch()) {
            $toReturn[] = $item;
        }

        return $toReturn;

    }

    /**
     * @param string $key
     * @param ?int $itemId
     * @return string|null
     */
    public function getGlobalSetting(string $key): ?string
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare('SELECT shops_items_virtual_requirement_value FROM cmw_shops_items_virtual_requirement 
                                          WHERE shops_items_virtual_requirement_key = :key');

        if (!$req->execute(['key' => $key])) {
            return null;
        }

        $res = $req->fetch();

        if (!$res) {
            return null;
        }

        return $res['shops_items_virtual_requirement_value'];
    }

    /**
     * @param int $virtualMethodId
     * @param string $key
     * @param string $value
     * @return bool
     */
    public function insertSetting(int $virtualMethodId, string $key, string $value): bool
    {
        $sql = "INSERT INTO cmw_shops_items_virtual_requirement 
                            (shops_items_virtual_method_id, shops_items_virtual_requirement_key, shops_items_virtual_requirement_value) 
                            VALUES (:virtualMethod, :key, :value) ";

        $db = DatabaseManager::getInstance();
        return $db->prepare($sql)->execute(['virtualMethod' => $virtualMethodId, 'key' => $key, 'value' => $value]);
    }

    public function clearSetting(int $virtualMethodId): bool
    {
        $data = ['shops_items_virtual_method_id' => $virtualMethodId];

        $sql = "DELETE FROM cmw_shops_items_virtual_requirement WHERE shops_items_virtual_method_id = :shops_items_virtual_method_id";

        $db = DatabaseManager::getInstance();

        return $db->prepare($sql)->execute($data);
    }

    /**
     * @param string $key
     * @param string $value
     * @return bool
     */
    public function updateOrInsertGlobalSetting(string $key, string $value): bool
    {
        $sql = "INSERT INTO cmw_shops_items_virtual_requirement 
                            (shops_items_virtual_requirement_key, shops_items_virtual_requirement_value) 
                            VALUES (:key, :value) ON DUPLICATE KEY UPDATE shops_items_virtual_requirement_value=:value2";

        $db = DatabaseManager::getInstance();
        return $db->prepare($sql)->execute(['key' => $key, 'value' => $value, 'value2' => $value]);
    }
}