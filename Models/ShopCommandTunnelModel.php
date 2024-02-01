<?php

namespace CMW\Model\Shop;

use CMW\Entity\Shop\ShopCommandTunnelEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Model\Users\UsersModel;


/**
 * Class: @ShopCommandTunnelModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopCommandTunnelModel extends AbstractModel
{

    private UsersModel $userModel;
    private ShopShippingModel $shippingModel;
    private ShopDeliveryUserAddressModel $deliveryUserAddressModel;

    public function __construct()
    {
        $this->userModel = new UsersModel();
        $this->shippingModel = new ShopShippingModel();
        $this->deliveryUserAddressModel = new ShopDeliveryUserAddressModel();
    }

    /**
     * @param int $id
     * @return \CMW\Entity\Shop\ShopCommandTunnelEntity
     */
    public function getShopCommandTunnelById(int $id): ?ShopCommandTunnelEntity
    {
        $sql = "SELECT * FROM cmw_shops_command_tunnel WHERE shop_command_tunnel_id = :shop_command_tunnel_id";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("shop_command_tunnel_id" => $id))) {
            return null;
        }

        $res = $res->fetch();

        $user = is_null($res["shop_user_id"]) ? null : $this->userModel->getUserById($res["shop_user_id"]);
        $shipping = is_null($res["shops_shipping_id"]) ? null : $this->shippingModel->getShopShippingById($res["shops_shipping_id"]);
        $deliveryUserAddress = is_null($res["shop_delivery_user_address_id"]) ? null : $this->deliveryUserAddressModel->getShopDeliveryUserAddressById($res["shop_delivery_user_address_id"]);

        return new ShopCommandTunnelEntity(
            $res["shop_command_tunnel_id"],
            $res["shop_command_tunnel_step"],
            $user,
            $shipping,
            $deliveryUserAddress,
            $res["shop_payment_method_name"] ?? null,
            $res["shop_command_tunnel_created_at"] ?? null,
            $res["shop_command_tunnel_updated_at"] ?? null
        );
    }

    public function tunnelExist(int $userId): bool
    {
        $data = ["shop_user_id" => $userId];

        $sql = "SELECT shop_command_tunnel_id FROM cmw_shops_command_tunnel WHERE shop_user_id = :shop_user_id";

        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if(!$req->execute($data)){
            return true;
        }

        $res = $req->fetch();

        if (!$res){
            return false;
        }

        return true;
    }

    public function createTunnel(int $userId): ?ShopCommandTunnelEntity
    {
        $var = array(
            "shop_user_id" => $userId
        );

        $sql = "INSERT INTO cmw_shops_command_tunnel  (shop_user_id,shop_command_tunnel_step) 
                VALUES (:shop_user_id,0)";

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            $id = $db->lastInsertId();
            return $this->getShopCommandTunnelById($id);
        }

        return null;

    }

    public function addDelivery(int $userId, int $deliveryId): void
    {
        $var = array(
            "shop_user_id" => $userId,
            "shop_delivery_user_address_id" => $deliveryId
        );

        $sql = "UPDATE cmw_shops_command_tunnel SET shop_command_tunnel_step = 1, shop_delivery_user_address_id = :shop_delivery_user_address_id WHERE shop_user_id = :shop_user_id";

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        $req->execute($var);
    }

    public function addShipping(int $userId, int $shippingId): void
    {
        $var = array(
            "shop_user_id" => $userId,
            "shops_shipping_id" => $shippingId
        );

        $sql = "UPDATE cmw_shops_command_tunnel SET shop_command_tunnel_step = 2, shops_shipping_id = :shops_shipping_id WHERE shop_user_id = :shop_user_id";

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        $req->execute($var);
    }

    public function clearShipping(int $userId): void
    {
        $var = array(
            "shop_user_id" => $userId,
        );

        $sql = "UPDATE cmw_shops_command_tunnel SET shop_command_tunnel_step = 1, shops_shipping_id = NULL WHERE shop_user_id = :shop_user_id";

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        $req->execute($var);
    }

    public function setPaymentName(int $userId, string $paymentName): void
    {
        $var = array(
            "shop_user_id" => $userId,
            "payment_name" => $paymentName,
        );

        $sql = "UPDATE cmw_shops_command_tunnel SET shop_payment_method_name = :payment_name WHERE shop_user_id = :shop_user_id";

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        $req->execute($var);
    }

    public function clearTunnel(int $userId): bool
    {
        $sql = "DELETE FROM cmw_shops_command_tunnel WHERE shop_user_id = :shop_user_id";

        $db = DatabaseManager::getInstance();

        return $db->prepare($sql)->execute(array("shop_user_id" => $userId));
    }

    /**
     * @param int $userId
     * @return \CMW\Entity\Shop\ShopCommandTunnelEntity
     */
    public function getShopCommandTunnelByUserId(int $userId): ?ShopCommandTunnelEntity
    {
        $sql = "SELECT * FROM cmw_shops_command_tunnel WHERE shop_user_id = :userId LIMIT 1;";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("userId" => $userId))) {
            return null;
        }

        $res = $res->fetch();

        $user = is_null($res["shop_user_id"]) ? null : $this->userModel->getUserById($res["shop_user_id"]);
        $shipping = is_null($res["shops_shipping_id"]) ? null : $this->shippingModel->getShopShippingById($res["shops_shipping_id"]);
        $deliveryUserAddress = is_null($res["shop_delivery_user_address_id"]) ? null : $this->deliveryUserAddressModel->getShopDeliveryUserAddressById($res["shop_delivery_user_address_id"]);

        return new ShopCommandTunnelEntity(
            $res["shop_command_tunnel_id"],
            $res["shop_command_tunnel_step"],
            $user,
            $shipping,
            $deliveryUserAddress,
            $res["shop_payment_method_name"] ?? null,
            $res["shop_command_tunnel_created_at"] ?? null,
            $res["shop_command_tunnel_updated_at"] ?? null
        );
    }

}
