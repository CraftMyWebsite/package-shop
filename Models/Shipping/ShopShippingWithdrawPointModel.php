<?php

namespace CMW\Model\Shop\Shipping;

use CMW\Entity\Shop\Shippings\ShopShippingWithdrawPointEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Package\AbstractModel;
use CMW\Manager\Security\EncryptManager;
use CMW\Model\Shop\Country\ShopCountryModel;

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
            $res['shops_shipping_withdraw_point_address_latitude'],
            $res['shops_shipping_withdraw_point_address_longitude'],
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
        $formattedCountry = ShopCountryModel::getInstance()->getCountryByCode($addressCountry)->getName();
        $coordinates = ShopCoordinatesModel::getInstance()->generateCoordinates($addressLine, $addressCity, $addressPostalCode, $formattedCountry);
        if ($coordinates) {
            $encryptedAddressLine = EncryptManager::encrypt($addressLine);
            $encryptedAddressCity = EncryptManager::encrypt($addressCity);
            $encryptedAddressPostalCode = EncryptManager::encrypt($addressPostalCode);
            $encryptedAddressLatitude = EncryptManager::encrypt($coordinates['latitude']);
            $encryptedAddressLongitude = EncryptManager::encrypt($coordinates['longitude']);
            $data = array(
                'name' => $name,
                'distance' => $distance,
                'address_line' => $encryptedAddressLine,
                'address_city' => $encryptedAddressCity,
                'address_postal_code' => $encryptedAddressPostalCode,
                'latitude' => $encryptedAddressLatitude,
                'longitude' => $encryptedAddressLongitude,
                'address_country' => $addressCountry,
            );

            $sql = 'INSERT INTO cmw_shops_shipping_withdraw_point(shops_shipping_withdraw_point_name, shops_shipping_withdraw_point_address_distance, shops_shipping_withdraw_point_address_line, shops_shipping_withdraw_point_address_city, shops_shipping_withdraw_point_address_postal_code, shops_shipping_withdraw_point_address_latitude, shops_shipping_withdraw_point_address_longitude, shops_shipping_withdraw_point_address_country)
                VALUES (:name, :distance, :address_line, :address_city, :address_postal_code, :latitude, :longitude, :address_country)';

            $db = DatabaseManager::getInstance();
            $req = $db->prepare($sql);

            if ($req->execute($data)) {
                $id = $db->lastInsertId();
                return $this->getShopShippingWithdrawPointById($id);
            }
        } else {
            Flash::send(Alert::WARNING, 'Boutique', 'Impossible de trouver les coordonnées géographique de votre adresse ! Veuillez réessayer');
        }

        return null;
    }

    public function editWithdrawPoint(int $withdrawPointId, string $name, int $distance, string $addressLine, string $addressCity, string $addressPostalCode, string $addressCountry): ?ShopShippingWithdrawPointEntity
    {
        $formattedCountry = ShopCountryModel::getInstance()->getCountryByCode($addressCountry)->getName();
        $coordinates = ShopCoordinatesModel::getInstance()->generateCoordinates($addressLine, $addressCity, $addressPostalCode, $formattedCountry);

        if ($coordinates) {
            $encryptedAddressLine = EncryptManager::encrypt($addressLine);
            $encryptedAddressCity = EncryptManager::encrypt($addressCity);
            $encryptedAddressPostalCode = EncryptManager::encrypt($addressPostalCode);
            $encryptedAddressLatitude = EncryptManager::encrypt($coordinates['latitude']);
            $encryptedAddressLongitude = EncryptManager::encrypt($coordinates['longitude']);
            $data = array(
                'id' => $withdrawPointId,
                'distance' => $distance,
                'name' => $name,
                'address_line' => $encryptedAddressLine,
                'address_city' => $encryptedAddressCity,
                'address_postal_code' => $encryptedAddressPostalCode,
                'latitude' => $encryptedAddressLatitude,
                'longitude' => $encryptedAddressLongitude,
                'address_country' => $addressCountry,
            );

            $sql = 'UPDATE cmw_shops_shipping_withdraw_point SET 
                                             shops_shipping_withdraw_point_address_distance=:distance,
                                             shops_shipping_withdraw_point_name=:name,
                                             shops_shipping_withdraw_point_address_line=:address_line,
                                             shops_shipping_withdraw_point_address_city =:address_city,
                                             shops_shipping_withdraw_point_address_postal_code =:address_postal_code,
                                             shops_shipping_withdraw_point_address_country =:address_country,
                                             shops_shipping_withdraw_point_address_latitude =:latitude, 
                                             shops_shipping_withdraw_point_address_longitude =:longitude
                                         WHERE shops_shipping_withdraw_point_id=:id';

            $db = DatabaseManager::getInstance();
            $req = $db->prepare($sql);

            if ($req->execute($data)) {
                return $this->getShopShippingWithdrawPointById($withdrawPointId);
            }
        } else {
            Flash::send(Alert::WARNING, 'Boutique', 'Impossible de trouver les coordonnées géographique de votre adresse ! Veuillez réessayer');
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
