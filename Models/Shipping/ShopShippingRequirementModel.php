<?php

namespace CMW\Model\Shop\Shipping;

use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;

/**
 * Class: @ShopShippingRequirementModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopShippingRequirementModel extends AbstractModel
{
    /**
     * @param string $key
     * @return string|null
     */
    public function getSetting(string $key): ?string
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare('SELECT shops_shipping_method_requirement_value FROM cmw_shops_shipping_method_requirement 
                                          WHERE shops_shipping_method_requirement_key = :key');

        if (!$req->execute(['key' => $key])) {
            return null;
        }

        $res = $req->fetch();

        if (!$res) {
            return null;
        }

        return $res['shops_shipping_method_requirement_value'];
    }

    /**
     * @param string $key
     * @param string $value
     * @return bool
     */
    public function updateOrInsertSetting(string $key, string $value): bool
    {
        $sql = 'INSERT INTO cmw_shops_shipping_method_requirement 
                            (shops_shipping_method_requirement_key, shops_shipping_method_requirement_value) 
                            VALUES (:key, :value) ON DUPLICATE KEY UPDATE shops_shipping_method_requirement_value=:value2';

        $db = DatabaseManager::getInstance();
        return $db->prepare($sql)->execute(['key' => $key, 'value' => $value, 'value2' => $value]);
    }
}
