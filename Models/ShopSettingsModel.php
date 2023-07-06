<?php

namespace CMW\Model\Shop;

use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;

/**
 * Class @ShopSettingsModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopSettingsModel extends AbstractModel
{

    /**
     * @param string $key
     * @return string
     * @desc Return the value of a key (currency)
     */
    public static function getSettingValue(string $key): string
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare('SELECT shop_settings_value FROM cmw_shops_settings WHERE shop_settings_key = ?');

        return ($req->execute(array($key))) ? $req->fetch()["shop_settings_value"] : "";
    }

    /**
     * @param string $key
     * @param string $value
     * @return void
     * @desc Edit a setting
     */
    public static function updateSetting(string $key, string $value): void
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare('UPDATE cmw_shops_settings SET shop_settings_value= :value WHERE shop_settings_key= :key');
        $req->execute(array("value" => $value, "key" => $key));
    }

}