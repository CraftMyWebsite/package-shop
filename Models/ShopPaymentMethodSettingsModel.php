<?php

namespace CMW\Model\Shop;

use CMW\Entity\Shop\ShopPaymentMethodSettingsEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;

/**
 * Class: @ShopPaymentMethodSettingsModel
 * @package Shop
 * @author Teyir
 * @version 0.0.1
 */
class ShopPaymentMethodSettingsModel extends AbstractModel
{
    /**
     * @param string $key
     * @return string|null
     */
    public function getSetting(string $key): ?string
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare('SELECT shop_payment_method_settings_value FROM cmw_shops_payment_method_settings 
                                          WHERE shop_payment_method_settings_key = :key');

        if (!$req->execute(['key' => $key])) {
            return null;
        }

        $res = $req->fetch();

        if (!$res) {
            return null;
        }

        return $res['shop_payment_method_settings_value'];
    }

    /**
     * @return \CMW\Entity\Shop\ShopPaymentMethodSettingsEntity[]
     */
    public function getSettings(): array
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare('SELECT shop_payment_method_settings_key, shop_payment_method_settings_value 
                                            FROM cmw_shops_payment_method_settings');

        if (!$req->execute()) {
            return [];
        }

        $res = $req->fetchAll();

        if (!$res) {
            return [];
        }

        $toReturn = [];

        foreach ($res as $setting) {
            $toReturn[] = new ShopPaymentMethodSettingsEntity(
                $setting['shop_payment_method_settings_key'],
                $setting['shop_payment_method_settings_value'],
            );
        }

        return $toReturn;
    }

    /**
     * @param string $key
     * @param string $value
     * @return bool
     */
    public function updateOrInsertSetting(string $key, string $value): bool
    {
        $sql = "INSERT INTO cmw_shops_payment_method_settings 
                            (shop_payment_method_settings_key, shop_payment_method_settings_value, shop_payment_method_settings_updated_at) 
                            VALUES (:key, :value, NOW()) 
                            ON DUPLICATE KEY UPDATE shop_payment_method_settings_value=:value2, 
                                                    shop_payment_method_settings_updated_at=NOW()";

        $db = DatabaseManager::getInstance();
        return $db->prepare($sql)->execute(['key' => $key, 'value' => $value, 'value2' => $value]);
    }
}