<?php

namespace CMW\Model\Shop\HistoryOrder;

use CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersUserAddressEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Manager\Security\EncryptManager;

/**
 * Class: @ShopHistoryOrdersUserAddressModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopHistoryOrdersUserAddressModel extends AbstractModel
{
    /**
     * @param int $id
     * @return ShopHistoryOrdersUserAddressEntity|null
     */
    public function getHistoryOrdersUserAddressByHistoryOrderId(int $id): ?ShopHistoryOrdersUserAddressEntity
    {
        $sql = 'SELECT * FROM cmw_shop_history_order_user_address WHERE shop_history_order_id = :shop_history_order_id';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('shop_history_order_id' => $id))) {
            return null;
        }

        $res = $res->fetch();

        if (empty($res)) {
            return null;
        }

        $historyOrder = is_null($res['shop_history_order_id']) ? null : ShopHistoryOrdersModel::getInstance()->getHistoryOrdersById($res['shop_history_order_id']) ?? null;

        return new ShopHistoryOrdersUserAddressEntity(
            $res['shop_history_order_user_address_id'],
            $historyOrder,
            $res['shop_history_order_user_address_name'] ?? null,
            $res['shop_history_order_user_address_user_mail'] ?? null,
            $res['shop_history_order_user_address_user_last_name'] ?? null,
            $res['shop_history_order_user_address_user_first_name'] ?? null,
            $res['shop_history_order_user_address_user_line_1'] ?? null,
            $res['shop_history_order_user_address_user_line_2'] ?? null,
            $res['shop_history_order_user_address_user_city'] ?? null,
            $res['shop_history_order_user_address_user_postal_code'] ?? null,
            $res['shop_history_order_user_address_user_country'] ?? null,
            $res['shop_history_order_user_address_user_phone'] ?? null
        );
    }

    public function addHistoryUserAddressOrder(int $orderId, ?string $addressName, ?string $addressUserMail, ?string $addressUserLastName, ?string $addressUserFirstName, ?string $addressUserLine1, ?string $addressUserLine2, ?string $addressUserCity, ?string $addressUserPostalCode, ?string $addressUserCountry, ?string $addressUserPhone): ?ShopHistoryOrdersUserAddressEntity
    {
        $encryptedMail = EncryptManager::encrypt($addressUserMail);
        $encryptedLine1 = EncryptManager::encrypt($addressUserLine1);
        $encryptedLine2 = EncryptManager::encrypt($addressUserLine2);
        $encryptedCity = EncryptManager::encrypt($addressUserCity);
        $encryptedPostalCode = EncryptManager::encrypt($addressUserPostalCode);
        $encryptedPhone = EncryptManager::encrypt($addressUserPhone);
        $var = array(
            'shop_history_order_id' => $orderId,
            'shop_history_order_user_address_name' => $addressName,
            'shop_history_order_user_address_user_mail' => $encryptedMail,
            'shop_history_order_user_address_user_last_name' => $addressUserLastName,
            'shop_history_order_user_address_user_first_name' => $addressUserFirstName,
            'shop_history_order_user_address_user_line_1' => $encryptedLine1,
            'shop_history_order_user_address_user_line_2' => $encryptedLine2,
            'shop_history_order_user_address_user_city' => $encryptedCity,
            'shop_history_order_user_address_user_postal_code' => $encryptedPostalCode,
            'shop_history_order_user_address_user_country' => $addressUserCountry,
            'shop_history_order_user_address_user_phone' => $encryptedPhone,
        );

        $sql = 'INSERT INTO cmw_shop_history_order_user_address (shop_history_order_id, shop_history_order_user_address_name, shop_history_order_user_address_user_mail,shop_history_order_user_address_user_last_name, shop_history_order_user_address_user_first_name,shop_history_order_user_address_user_line_1, shop_history_order_user_address_user_line_2, shop_history_order_user_address_user_city,shop_history_order_user_address_user_postal_code, shop_history_order_user_address_user_country, shop_history_order_user_address_user_phone) VALUES (:shop_history_order_id, :shop_history_order_user_address_name, :shop_history_order_user_address_user_mail, :shop_history_order_user_address_user_last_name, :shop_history_order_user_address_user_first_name, :shop_history_order_user_address_user_line_1, :shop_history_order_user_address_user_line_2, :shop_history_order_user_address_user_city, :shop_history_order_user_address_user_postal_code, :shop_history_order_user_address_user_country, :shop_history_order_user_address_user_phone)';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            return $this->getHistoryOrdersUserAddressByHistoryOrderId($orderId);
        }

        return null;
    }
}
