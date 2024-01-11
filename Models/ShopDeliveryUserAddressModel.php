<?php

namespace CMW\Model\Shop;

use CMW\Entity\Shop\ShopDeliveryUserAddressEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Model\Users\UsersModel;


/**
 * Class: @ShopDeliveryUserAddressModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopDeliveryUserAddressModel extends AbstractModel
{

    private UsersModel $userModel;
    public function __construct()
    {
        $this->userModel = new UsersModel();
    }

    /**
     * @return ShopDeliveryUserAddressEntity []
     */
    public function getShopDeliveryUserAddress(): array
    {

        $sql = "SELECT shop_delivery_user_address_id FROM cmw_shops_delivery_user_address WHERE ORDER BY shop_delivery_user_address_id ASC";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($delivery = $res->fetch()) {
            $toReturn[] = $this->getShopDeliveryUserAddressById($delivery["shop_delivery_user_address_id"]);
        }

        return $toReturn;
    }

    public function getShopDeliveryUserAddressById(int $id): ?ShopDeliveryUserAddressEntity
    {
        $sql = "SELECT * FROM cmw_shops_delivery_user_address WHERE shop_delivery_user_address_id = :shop_delivery_user_address_id ORDER BY shop_delivery_is_fav DESC";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("shop_delivery_user_address_id" => $id))) {
            return null;
        }

        $res = $res->fetch();

        $user = is_null($res["shop_user_id"]) ? null : $this->userModel->getUserById($res["shop_user_id"]);

        return new ShopDeliveryUserAddressEntity(
            $res["shop_delivery_user_address_id"],
            $res["shop_delivery_is_fav"],
            $res["shop_delivery_user_address_label"] ?? null,
            $user,
            $res["shop_delivery_user_address_first_name"] ?? null,
            $res["shop_delivery_user_address_last_name"] ?? null,
            $res["shop_delivery_user_address_line_1"] ?? null,
            $res["shop_delivery_user_address_line_2"] ?? null,
            $res["shop_delivery_user_address_city"] ?? null,
            $res["shop_delivery_user_address_postal_code"] ?? null,
            $res["shop_delivery_user_address_country"] ?? null,
            $res["shop_delivery_user_address_phone"] ?? null,
            $res["shop_delivery_user_address_created_at"],
            $res["shop_delivery_user_address_updated_at"]
        );
    }

    public function getShopDeliveryUserAddressByUserId(int $userId): array
    {
        $sql = "SELECT shop_delivery_user_address_id FROM cmw_shops_delivery_user_address WHERE shop_user_id = :userId ORDER BY shop_delivery_is_fav DESC";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("userId" => $userId))) {
            return [];
        }

        $toReturn = [];

        while ($cart = $res->fetch()) {
            $toReturn[] = $this->getShopDeliveryUserAddressById($cart["shop_delivery_user_address_id"]);
        }

        return $toReturn;
    }

    /**
     * @param string $label
     * @param int $isFav
     * @param \CMW\Entity\Users\UserEntity $userId;
     * @param string $firstName
     * @param string $lastName
     * @param string $phone
     * @param string $line1
     * @param string $line2
     * @param string $city
     * @param string $postalCode
     * @param string $country
     * @return ShopDeliveryUserAddressEntity|null
     */
    public function createDeliveryUserAddress(string $label, int $isFav, int $userId, string $firstName, string $lastName, string $phone, string $line1, string $line2, string $city, string $postalCode, string $country): ?ShopDeliveryUserAddressEntity
    {

        $var = array(
            "shop_delivery_user_address_label" => $label,
            "shop_delivery_is_fav" => $isFav,
            "shop_user_id" => $userId,
            "shop_delivery_user_address_first_name" => $firstName,
            "shop_delivery_user_address_last_name" => $lastName,
            "shop_delivery_user_address_phone" => $phone,
            "shop_delivery_user_address_line_1" => $line1,
            "shop_delivery_user_address_line_2" => $line2,
            "shop_delivery_user_address_city" => $city,
            "shop_delivery_user_address_postal_code" => $postalCode,
            "shop_delivery_user_address_country" => $country
        );

        $sql = "INSERT INTO cmw_shops_delivery_user_address (shop_delivery_user_address_label, shop_delivery_is_fav, shop_user_id, shop_delivery_user_address_first_name, 
                               shop_delivery_user_address_last_name, shop_delivery_user_address_line_1, shop_delivery_user_address_line_2, shop_delivery_user_address_city, shop_delivery_user_address_postal_code, shop_delivery_user_address_country, shop_delivery_user_address_phone) 
                VALUES (:shop_delivery_user_address_label, :shop_delivery_is_fav, :shop_user_id, :shop_delivery_user_address_first_name,
                        :shop_delivery_user_address_last_name, :shop_delivery_user_address_line_1, :shop_delivery_user_address_line_2,
                        :shop_delivery_user_address_city, :shop_delivery_user_address_postal_code, :shop_delivery_user_address_country, :shop_delivery_user_address_phone)";

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            $id = $db->lastInsertId();
            return $this->getShopDeliveryUserAddressById($id);
        }

        return null;

    }

    public function removeOtherFav(int $userId): void
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare('UPDATE cmw_shops_delivery_user_address SET shop_delivery_is_fav=0 WHERE shop_user_id=:userId');
        $req->execute(array("userId" => $userId));
    }
}