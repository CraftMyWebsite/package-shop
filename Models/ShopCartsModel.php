<?php

namespace CMW\Model\Shop;

use CMW\Entity\Shop\ShopCartEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Model\Users\UsersModel;

/**
 * Class: @ShopCartsModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopCartsModel extends AbstractModel
{
    private UsersModel $userModel;
    private ShopItemsModel $shopItemsModel;
    public function __construct()
    {
        $this->userModel = new UsersModel();
        $this->shopItemsModel = new ShopItemsModel();
    }

    public function getShopCartsById(int $id): ?ShopCartEntity
    {
        $sql = "SELECT * FROM cmw_shops_cart_items WHERE shop_cart_item_id = :shop_cart_item_id";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("shop_cart_item_id" => $id))) {
            return null;
        }

        $res = $res->fetch();

        $user = $this->userModel->getUserById($res["shop_user_id"]);
        $item = $this->shopItemsModel->getShopItemsById($res["shop_item_id"]);

        return new ShopCartEntity(
            $res["shop_cart_item_id"],
            $res["shop_shopping_session_id"] ?? null,
            $item ?? null,
            $user ?? null,
            $res["shop_cart_item_quantity"],
            $res["shop_cart_item_created_at"],
            $res["shop_cart_item_updated_at"]
        );
    }

    /**
     * @return \CMW\Entity\Shop\ShopCartEntity []
     */
    public function getShopCarts(): array
    {

        $sql = "SELECT shop_cart_item_id FROM cmw_shops_cart_items ORDER BY shop_cart_item_updated_at DESC";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($cart = $res->fetch()) {
            $toReturn[] = $this->getShopCartsById($cart["shop_cart_item_id"]);
        }

        return $toReturn;

    }

    /**
     * @return \CMW\Entity\Shop\ShopCartEntity []
     */
    public function getShopCartsByUserId(int $userId): array
    {
        $sql = "SELECT shop_cart_item_id FROM cmw_shops_cart_items WHERE shop_user_id = :shop_user_id ORDER BY shop_cart_item_id ASC";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("shop_user_id" => $userId))) {
            return array();
        }

        $toReturn = array();

        while ($cart = $res->fetch()) {
            $toReturn[] = $this->getShopCartsById($cart["shop_cart_item_id"]);
        }

        return $toReturn;
    }

    /**
     * @return array
     */
    public function countItemsInCartByUser(): array
    {
        $sql = "SELECT shop_user_id, COUNT(shop_cart_item_id) AS shop_item_in_cart FROM cmw_shops_cart_items GROUP BY shop_user_id";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($cart = $res->fetch()) {
            $toReturn[] = $cart;
        }

        return $toReturn;
    }

    public function addToCart(int $itemId): ?ShopCartEntity
    {
        $userId = UsersModel::getCurrentUser()->getId();
        $data = array(
            "shop_item_id" => $itemId,
            "shop_user_id" => $userId,
        );

        $sql = "INSERT INTO cmw_shops_cart_items(shop_item_id, shop_user_id)
                VALUES (:shop_item_id, :shop_user_id)";


        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $id = $db->lastInsertId();
            return $this->getShopCartsById($id);
        }

        return null;
    }

    /**
     * @return int
     * @desc Get the current quantity of item in cart
     */
    public function getQuantity(int $itemId): int
    {
        $userId = UsersModel::getCurrentUser()->getId();
        $sql = "SELECT shop_cart_item_quantity FROM cmw_shops_cart_items WHERE shop_item_id = :shop_item_id AND shop_user_id = :shop_user_id";
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if (!$req->execute(array("shop_item_id" => $itemId, "shop_user_id" => $userId))) {
            return 0;
        }
        $res = $req->fetch();
        if(!$res){
            return 0;
        }
        return $res['shop_cart_item_quantity'] ?? 0;
    }

    /**
     * @return void
     * @desc increase the quantity of item
     */
    public function increaseQuantity(int $itemId): void
    {
        $userId = UsersModel::getCurrentUser()->getId();
        $quantity = $this->getQuantity($itemId);
        $sql = "UPDATE cmw_shops_cart_items SET shop_cart_item_quantity = :shop_cart_item_quantity WHERE shop_item_id = :shop_item_id AND shop_user_id = :shop_user_id";
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        $req->execute(array("shop_cart_item_quantity" => $quantity + 1, "shop_item_id" => $itemId, "shop_user_id" => $userId));
    }

    /**
     * @return void
     * @desc decrease the quantity of item
     */
    public function decreaseQuantity(int $itemId): void
    {
        $userId = UsersModel::getCurrentUser()->getId();
        $quantity = $this->getQuantity($itemId);
        if ($quantity === 1) {
            $this->removeItem($itemId);
        } else {
            $sql = "UPDATE cmw_shops_cart_items SET shop_cart_item_quantity = :shop_cart_item_quantity WHERE shop_item_id = :shop_item_id AND shop_user_id = :shop_user_id";
            $db = DatabaseManager::getInstance();
            $req = $db->prepare($sql);
            $req->execute(array("shop_cart_item_quantity" => $quantity - 1, "shop_item_id" => $itemId, "shop_user_id" => $userId));
        }
    }

    public function removeItem(int $itemId): bool
    {
        $userId = UsersModel::getCurrentUser()->getId();
        $sql = "DELETE FROM cmw_shops_cart_items WHERE shop_item_id = :shop_item_id AND shop_user_id = :shop_user_id";

        $db = DatabaseManager::getInstance();

        return $db->prepare($sql)->execute(array("shop_item_id" => $itemId, "shop_user_id" => $userId));
    }

    public function itemIsInCart(int $itemId) :bool
    {
        if ($itemId === null){
            return  false;
        }

        $userId = UsersModel::getCurrentUser()->getId();

        $sql = "SELECT shop_cart_item_id FROM `cmw_shops_cart_items` WHERE shop_item_id = :shop_item_id AND shop_user_id = :shop_user_id";

        $db = DatabaseManager::getInstance();
        $res = $db->prepare($sql);

        $res->execute(array("shop_item_id" => $itemId, "shop_user_id" => $userId));

        return count($res->fetchAll()) === 0;
    }

}