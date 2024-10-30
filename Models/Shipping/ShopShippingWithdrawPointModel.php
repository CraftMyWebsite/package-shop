<?php

namespace CMW\Model\Shop\Shipping;

use CMW\Entity\Shop\Shippings\ShopShippingEntity;
use CMW\Entity\Shop\Shippings\ShopShippingWithdrawPointEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;

/**
 * Class: @ShopShippingWithdrawPointModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopShippingWithdrawPointModel extends AbstractModel
{
    /**
     * @param int $id
     * @return \CMW\Entity\Shop\Shippings\ShopShippingWithdrawPointEntity
     */
    public function getShopShippingWithdrawPointById(int $id): ?ShopShippingWithdrawPointEntity
    {
        $sql = 'SELECT * FROM cmw_shops_shipping_withdraw_point WHERE shops_shipping_withdraw_point_id = :shops_shipping_withdraw_point_id';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('shops_shipping_withdraw_point_id' => $id))) {
            return null;
        }

        $res = $res->fetch();

        return new ShopShippingWithdrawPointEntity(
            $res['shops_shipping_withdraw_point_id'],
            $res['shops_shipping_withdraw_point_name'],
            $res['shops_shipping_withdraw_point_address_distance'] ?? null,
            $res['shops_shipping_withdraw_point_address_line'],
            $res['shops_shipping_withdraw_point_address_city'],
            $res['shops_shipping_withdraw_point_address_postal_code'],
            $res['shops_shipping_withdraw_point_address_country']
        );
    }

    /**
     * @return \CMW\Entity\Shop\Shippings\ShopShippingWithdrawPointEntity []
     */
    public function getShopShippingWithdrawPoint(): array
    {
        $sql = 'SELECT shops_shipping_withdraw_point_id FROM cmw_shops_shipping_withdraw_point';
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($shipping = $res->fetch()) {
            $toReturn[] = $this->getShopShippingWithdrawPointById($shipping['shops_shipping_withdraw_point_id']);
        }

        return $toReturn;
    }

    public function createWithdrawPoint(int $distance, string $name, string $addressLine, string $addressCity, string $addressPostalCode, string $addressCountry): ?ShopShippingWithdrawPointEntity
    {
        $data = array(
            'name' => $name,
            'distance' => $distance,
            'address_line' => $addressLine,
            'address_city' => $addressCity,
            'address_postal_code' => $addressPostalCode,
            'address_country' => $addressCountry,
        );

        $sql = 'INSERT INTO cmw_shops_shipping_withdraw_point(shops_shipping_withdraw_point_name, shops_shipping_withdraw_point_address_distance, shops_shipping_withdraw_point_address_line, shops_shipping_withdraw_point_address_city, shops_shipping_withdraw_point_address_postal_code, shops_shipping_withdraw_point_address_country)
                VALUES (:name, :distance, :address_line, :address_city, :address_postal_code, :address_country)';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $id = $db->lastInsertId();
            return $this->getShopShippingWithdrawPointById($id);
        }

        return null;
    }

    public function editWithdrawPoint(int $withdrawPointId, string $name, int $distance, string $addressLine, string $addressCity, string $addressPostalCode, string $addressCountry): ?ShopShippingWithdrawPointEntity
    {
        $data = array(
            'id' => $withdrawPointId,
            'distance' => $distance,
            'name' => $name,
            'address_line' => $addressLine,
            'address_city' => $addressCity,
            'address_postal_code' => $addressPostalCode,
            'address_country' => $addressCountry,
        );

        $sql = 'UPDATE cmw_shops_shipping_withdraw_point SET 
                                             shops_shipping_withdraw_point_address_distance=:distance,
                                             shops_shipping_withdraw_point_name=:name,
                                             shops_shipping_withdraw_point_address_line=:address_line,
                                             shops_shipping_withdraw_point_address_city =:address_city,
                                             shops_shipping_withdraw_point_address_postal_code =:address_postal_code,
                                             shops_shipping_withdraw_point_address_country =:address_country
                                         WHERE shops_shipping_withdraw_point_id=:id';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            return $this->getShopShippingWithdrawPointById($withdrawPointId);
        }

        return null;
    }

    public function deleteWithdrawPoint(int $id): bool
    {
        $sql = 'DELETE FROM cmw_shops_shipping_withdraw_point WHERE shops_shipping_withdraw_point_id = :shops_shipping_withdraw_point_id';

        $db = DatabaseManager::getInstance();

        return $db->prepare($sql)->execute(array('shops_shipping_withdraw_point_id' => $id));
    }
}
