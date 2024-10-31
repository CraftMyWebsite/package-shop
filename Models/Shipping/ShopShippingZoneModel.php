<?php

namespace CMW\Model\Shop\Shipping;

use CMW\Entity\Shop\Shippings\ShopShippingZoneEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;

/**
 * Class: @ShopShippingZoneModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopShippingZoneModel extends AbstractModel
{
    /**
     * @param int $id
     * @return \CMW\Entity\Shop\Shippings\ShopShippingZoneEntity
     */
    public function getShopShippingZoneById(int $id): ?ShopShippingZoneEntity
    {
        $sql = 'SELECT * FROM cmw_shops_shipping_zone WHERE shops_shipping_zone_id = :shops_shipping_zone_id';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('shops_shipping_zone_id' => $id))) {
            return null;
        }

        $res = $res->fetch();

        return new ShopShippingZoneEntity(
            $res['shops_shipping_zone_id'],
            $res['shops_shipping_zone_name'],
            $res['shops_shipping_zone_country']
        );
    }

    /**
     * @return \CMW\Entity\Shop\Shippings\ShopShippingZoneEntity []
     */
    public function getShopShippingZone(): array
    {
        $sql = 'SELECT shops_shipping_zone_id FROM cmw_shops_shipping_zone';
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($shipping = $res->fetch()) {
            $toReturn[] = $this->getShopShippingZoneById($shipping['shops_shipping_zone_id']);
        }

        return $toReturn;
    }

    public function createZone(string $name, string $zone): ?ShopShippingZoneEntity
    {
        $data = array(
            'shops_shipping_zone_name' => $name,
            'shops_shipping_zone_country' => $zone,
        );

        $sql = 'INSERT INTO cmw_shops_shipping_zone(shops_shipping_zone_name, shops_shipping_zone_country)
                VALUES (:shops_shipping_zone_name, :shops_shipping_zone_country)';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $id = $db->lastInsertId();
            return $this->getShopShippingZoneById($id);
        }

        return null;
    }

    public function editZone(int $zoneId, string $name, string $zone): ?ShopShippingZoneEntity
    {
        $data = array(
            'shops_shipping_zone_id' => $zoneId,
            'shops_shipping_zone_name' => $name,
            'shops_shipping_zone_country' => $zone,
        );

        $sql = 'UPDATE cmw_shops_shipping_zone SET shops_shipping_zone_name=:shops_shipping_zone_name, shops_shipping_zone_country=:shops_shipping_zone_country WHERE shops_shipping_zone_id=:shops_shipping_zone_id';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            return $this->getShopShippingZoneById($zoneId);
        }

        return null;
    }

    public function deleteZone(int $id): bool
    {
        $sql = 'DELETE FROM cmw_shops_shipping_zone WHERE shops_shipping_zone_id = :shops_shipping_zone_id';

        $db = DatabaseManager::getInstance();

        return $db->prepare($sql)->execute(array('shops_shipping_zone_id' => $id));
    }
}
