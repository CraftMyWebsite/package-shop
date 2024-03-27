<?php

namespace CMW\Model\Shop\Item;

use CMW\Manager\Database\DatabaseManager;
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
     * @return string|null
     */
    public function getSetting(string $key): ?string
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
     * @param string $key
     * @param string $value
     * @return bool
     */
    public function updateOrInsertSetting(string $key, string $value): bool
    {
        //TODO prendre en compte l'article lié :
        $sql = "INSERT INTO cmw_shops_items_virtual_requirement 
                            (shops_items_virtual_requirement_key, shops_items_virtual_requirement_value) 
                            VALUES (:key, :value) 
                            ON DUPLICATE KEY UPDATE shops_items_virtual_requirement_value=:value2";

        $db = DatabaseManager::getInstance();
        return $db->prepare($sql)->execute(['key' => $key, 'value' => $value, 'value2' => $value]);
    }
}