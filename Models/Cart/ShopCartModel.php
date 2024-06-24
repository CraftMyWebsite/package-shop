<?php

namespace CMW\Model\Shop\Cart;

use CMW\Entity\Shop\Carts\ShopCartEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Model\Users\UsersModel;

/**
 * Class: @ShopCartModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopCartModel extends AbstractModel
{
    public function getShopCartsById(?int $id): ?ShopCartEntity
    {
        if (is_null($id)) {
            return null;
        }

        $sql = "SELECT * FROM cmw_shops_cart WHERE shop_cart_id = :shop_cart_id";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(["shop_cart_id" => $id])) {
            return null;
        }

        $res = $res->fetch();

        $user = is_null($res["shop_user_id"]) ? null : UsersModel::getInstance()->getUserById($res["shop_user_id"]);

        return new ShopCartEntity(
            $res["shop_cart_id"],
            $user,
            $res["shop_client_session_id"] ?? null,
            $res["shop_created_at"],
            $res["shop_updated_at"]
        );
    }

    public function getShopCartsByUserOrSessionId(?int $userId, $sessionId): ?ShopCartEntity
    {
        if (is_null($userId)) {
            $sql = "SELECT * FROM cmw_shops_cart WHERE shop_client_session_id = :session_id";
            $data = ["session_id" => $sessionId];
        } else {
            $sql = "SELECT * FROM cmw_shops_cart WHERE shop_user_id = :shop_user_id";
            $data = ["shop_user_id" => $userId];
        }

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute($data)) {
            return null;
        }

        $res = $res->fetch();

        return $this->getShopCartsById($res["shop_cart_id"] ?? null);
    }

    /**
     * @return \CMW\Entity\Shop\Carts\ShopCartEntity []
     */
    public function getShopCartsForConnectedUsers(): array
    {

        $sql = "SELECT shop_cart_id FROM cmw_shops_cart WHERE shop_user_id IS NOT NULL ORDER BY shop_updated_at DESC";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return [];
        }

        $toReturn = [];

        while ($cart = $res->fetch()) {
            $toReturn[] = $this->getShopCartsById($cart["shop_cart_id"]);
        }

        return $toReturn;

    }

    /**
     * @return \CMW\Entity\Shop\Carts\ShopCartEntity []
     */
    public function getShopCartsForSessions(): array
    {

        $sql = "SELECT shop_cart_id FROM cmw_shops_cart WHERE shop_user_id IS NULL ORDER BY shop_updated_at DESC";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return [];
        }

        $toReturn = [];

        while ($cart = $res->fetch()) {
            $toReturn[] = $this->getShopCartsById($cart["shop_cart_id"]);
        }

        return $toReturn;

    }

    public function cartExist(?int $userId, string $sessionId): bool
    {
        if (is_null($userId)) {
            $sql = "SELECT shop_cart_id FROM cmw_shops_cart WHERE shop_client_session_id = :session_id";
            $data['session_id'] = $sessionId;
        } else {
            $sql = "SELECT shop_cart_id FROM cmw_shops_cart WHERE shop_user_id = :shop_user_id";
            $data["shop_user_id"] = $userId;
        }

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

    public function createCart(?int $userId, ?string $sessionId): ?ShopCartEntity
    {

        if (is_null($userId)) {
            $sql = "INSERT INTO cmw_shops_cart(shop_client_session_id) VALUES (:session_id)";
            $data = ["session_id" => $sessionId];
        } else {
            $sql = "INSERT INTO cmw_shops_cart(shop_user_id) VALUES (:shop_user_id)";
            $data = ["shop_user_id" => $userId];
        }

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $id = $db->lastInsertId();
            return $this->getShopCartsById($id);
        }

        return null;
    }

    public function removeSessionCart(string $sessionId): bool
    {
        $data = ['shop_client_session_id' => $sessionId];

        $sql = "DELETE FROM cmw_shops_cart WHERE shop_client_session_id = :shop_client_session_id";

        $db = DatabaseManager::getInstance();

        return $db->prepare($sql)->execute($data);
    }

    public function clearUserCart(int $userId): bool
    {
        $sql = "DELETE FROM cmw_shops_cart WHERE shop_user_id  = :user_id";

        $db = DatabaseManager::getInstance();

        return $db->prepare($sql)->execute(['user_id' => $userId]);
    }

    public function switchSessionToUserCart(string $session_id, mixed $userId): void
    {
        $sql = "UPDATE cmw_shops_cart SET shop_user_id = :user_id, shop_client_session_id = null WHERE shop_client_session_id = :session_id";

        $db = DatabaseManager::getInstance();
        $db->prepare($sql)->execute(['user_id' => $userId, 'session_id' => $session_id]);
    }
}