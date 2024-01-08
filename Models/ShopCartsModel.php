<?php

namespace CMW\Model\Shop;

use CMW\Entity\Shop\ShopCartEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Package\AbstractModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Redirect;

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

        if (!$res->execute(["shop_cart_item_id" => $id])) {
            return null;
        }

        $res = $res->fetch();

        $user = is_null($res["shop_user_id"]) ? null : $this->userModel->getUserById($res["shop_user_id"]);
        $item = $this->shopItemsModel->getShopItemsById($res["shop_item_id"]);

        return new ShopCartEntity(
            $res["shop_cart_item_id"],
            $res["shop_client_session_id"] ?? null,
            $item ?? null,
            $user,
            $res["shop_cart_item_quantity"],
            $res["shop_cart_item_created_at"],
            $res["shop_cart_item_updated_at"]
        );
    }

    public function getShopCartsByItemIdAndUserId(int $itemId, ?int $userId, $sessionId): ?ShopCartEntity
    {
        $data = ["shop_item_id" => $itemId];

        if (is_null($userId)) {
            $sql = "SELECT * FROM cmw_shops_cart_items WHERE shop_item_id = :shop_item_id AND shop_client_session_id = :session_id";
            $data['session_id'] = $sessionId;
        } else {
            $sql = "SELECT * FROM cmw_shops_cart_items WHERE shop_item_id = :shop_item_id AND shop_user_id = :shop_user_id";
            $data["shop_user_id"] = $userId;
        }

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute($data)) {
            return null;
        }

        $res = $res->fetch();

        $user = is_null($res["shop_user_id"]) ? null : $this->userModel->getUserById($res["shop_user_id"]);
        $item = $this->shopItemsModel->getShopItemsById($res["shop_item_id"]);

        return new ShopCartEntity(
            $res["shop_cart_item_id"],
            $res["shop_client_session_id"] ?? null,
            $item ?? null,
            $user,
            $res["shop_cart_item_quantity"],
            $res["shop_cart_item_created_at"],
            $res["shop_cart_item_updated_at"]
        );
    }

    /**
     * @return \CMW\Entity\Shop\ShopCartEntity []
     */
    public function getShopCartsForConnectedUsers(): array
    {

        $sql = "SELECT shop_cart_item_id FROM cmw_shops_cart_items WHERE shop_user_id IS NOT NULL ORDER BY shop_cart_item_updated_at DESC";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return [];
        }

        $toReturn = [];

        while ($cart = $res->fetch()) {
            $toReturn[] = $this->getShopCartsById($cart["shop_cart_item_id"]);
        }

        return $toReturn;

    }

    /**
     * @return \CMW\Entity\Shop\ShopCartEntity []
     */
    public function getShopCartsForSessions(): array
    {

        $sql = "SELECT shop_cart_item_id FROM cmw_shops_cart_items WHERE shop_user_id IS NULL ORDER BY shop_cart_item_updated_at DESC";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return [];
        }

        $toReturn = [];

        while ($cart = $res->fetch()) {
            $toReturn[] = $this->getShopCartsById($cart["shop_cart_item_id"]);
        }

        return $toReturn;

    }

    /**
     * @return \CMW\Entity\Shop\ShopCartEntity []
     */
    public function getShopCartsByUserId(?int $userId, string $sessionId): array
    {
        $sql = "SELECT csci.shop_cart_item_id, csci.shop_item_id
                FROM cmw_shops_cart_items csci
                JOIN cmw_shops_items csi ON csci.shop_item_id = csi.shop_item_id
                WHERE csi.shop_item_archived = 0
                AND csci.shop_cart_item_aside = 0";

        if (is_null($userId)) {
            $sql .= " AND csci.shop_client_session_id = :session_id";
            $data = ['session_id' => $sessionId];
        } else {
            $sql .= " AND csci.shop_user_id = :user_id";
            $data = ["user_id" => $userId];
        }

        $sql .= " ORDER BY shop_cart_item_id";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute($data)) {
            return [];
        }

        $toReturn = [];

        while ($cart = $res->fetch()) {
            $toReturn[] = $this->getShopCartsById($cart["shop_cart_item_id"]);
        }

        $this->clearArchivedItemsFromCart($userId, $sessionId);

        return $toReturn;
    }

    public function getShopCartsAsideByUserId(?int $userId, string $sessionId): array
    {
        $sql = "SELECT csci.shop_cart_item_id, csci.shop_item_id
                FROM cmw_shops_cart_items csci
                JOIN cmw_shops_items csi ON csci.shop_item_id = csi.shop_item_id
                WHERE csi.shop_item_archived = 0
                AND csci.shop_cart_item_aside = 1";

        if (is_null($userId)) {
            $sql .= " AND csci.shop_client_session_id = :session_id";
            $data = ['session_id' => $sessionId];
        } else {
            $sql .= " AND csci.shop_user_id = :user_id";
            $data = ["user_id" => $userId];
        }

        $sql .= " ORDER BY shop_cart_item_id";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute($data)) {
            return [];
        }

        $toReturn = [];

        while ($cart = $res->fetch()) {
            $toReturn[] = $this->getShopCartsById($cart["shop_cart_item_id"]);
        }

        $this->clearArchivedItemsFromCart($userId, $sessionId);

        return $toReturn;
    }

    /**
     * @return \CMW\Entity\Shop\ShopCartEntity []
     */
    public function getCartArchivedItems(?int $userId, string $sessionId) : array
    {
        $sql = "SELECT csci.shop_cart_item_id, csci.shop_item_id
                FROM cmw_shops_cart_items csci
                JOIN cmw_shops_items csi ON csci.shop_item_id = csi.shop_item_id
                WHERE csi.shop_item_archived = 1";

        if (is_null($userId)) {
            $sql .= " AND csci.shop_client_session_id = :session_id";
            $data = ['session_id' => $sessionId];
        } else {
            $sql .= " AND csci.shop_user_id = :user_id";
            $data = ["user_id" => $userId];
        }

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute($data)) {
            return [];
        }

        $toReturn = [];

        while ($cart = $res->fetch()) {
            $toReturn[] = $this->getShopCartsById($cart["shop_cart_item_id"]);
        }

        return $toReturn;
    }

    public function clearArchivedItemsFromCart(?int $userId, string $sessionId): void
    {
        foreach ($this->getCartArchivedItems($userId, $sessionId) as $archivedItem) {
            $this->removeItem($archivedItem->getItem()->getId(),$userId, $sessionId);
        }
    }

    /**
     * @param int|null $userId
     * @param string $sessionId
     * @return string
     * @desc count number of items in cart
     */
    public function countItemsByUserId(?int $userId, string $sessionId): mixed
    {
        $sql = "SELECT COUNT(shop_cart_item_id) AS count
                FROM cmw_shops_cart_items csci
                JOIN cmw_shops_items csi ON csci.shop_item_id = csi.shop_item_id
                WHERE csi.shop_item_archived = 0";

        if (is_null($userId)) {
            $sql .= " AND csci.shop_client_session_id = :session_id";
            $data = ["session_id" => $sessionId];
        } else {
            $sql .= " AND csci.shop_user_id = :user_id";
            $data = ["user_id" => $userId];
        }

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute($data)) {
            return 0;
        }

        return $res->fetch(0)['count'];
    }

    public function addToCart(int $itemId, ?int $userId, string $sessionId, int $quantity): ?ShopCartEntity
    {
        $data = [
            "shop_item_id" => $itemId,
            "shop_cart_item_quantity" => $quantity,
        ];

        if (is_null($userId)) {
            $sql = "INSERT INTO cmw_shops_cart_items(shop_item_id, shop_client_session_id, shop_cart_item_quantity)
                VALUES (:shop_item_id, :session_id, :shop_cart_item_quantity)";
            $data['session_id'] = $sessionId;
        } else {
            $sql = "INSERT INTO cmw_shops_cart_items(shop_item_id, shop_user_id, shop_cart_item_quantity)
                VALUES (:shop_item_id, :shop_user_id, :shop_cart_item_quantity)";
            $data["shop_user_id"] = $userId;
        }

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $id = $db->lastInsertId();
            return $this->getShopCartsById($id);
        }

        return null;
    }

    public function addToAsideCart(int $itemId, ?int $userId, string $sessionId): ?ShopCartEntity
    {
        $data = ["shop_item_id" => $itemId];

        if (is_null($userId)) {
            $sql = "INSERT INTO cmw_shops_cart_items(shop_item_id, shop_client_session_id, shop_cart_item_aside)
                VALUES (:shop_item_id, :session_id, 1)";
            $data['session_id'] = $sessionId;
        } else {
            $sql = "INSERT INTO cmw_shops_cart_items(shop_item_id, shop_user_id, shop_cart_item_aside)
                VALUES (:shop_item_id, :shop_user_id, 1)";
            $data["shop_user_id"] = $userId;
        }

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $id = $db->lastInsertId();
            return $this->getShopCartsById($id);
        }

        return null;
    }

    public function switchAsideToCart(int $itemId, ?int $userId, string $sessionId): void
    {
        $data = ["shop_item_id" => $itemId];

        if (is_null($userId)) {
            $sql = "UPDATE cmw_shops_cart_items SET shop_cart_item_aside = 0 WHERE shop_item_id = :shop_item_id AND shop_client_session_id = :session_id";
            $data["session_id"] = $sessionId;
        } else {
            $sql = "UPDATE cmw_shops_cart_items SET shop_cart_item_aside = 0 WHERE shop_item_id = :shop_item_id AND shop_user_id = :user_id";
            $data["user_id"] = $userId;
        }

        $db = DatabaseManager::getInstance();
        $db->prepare($sql)->execute($data);
    }

    public function isAlreadyAside(int $itemId, ?int $userId, string $sessionId): bool
    {
        $data = ["shop_item_id" => $itemId];

        if (is_null($userId)) {
            $sql = "SELECT shop_cart_item_id FROM cmw_shops_cart_items WHERE shop_cart_item_aside = 1 AND shop_item_id =:shop_item_id AND shop_client_session_id = :session_id";
            $data['session_id'] = $sessionId;
        } else {
            $sql = "SELECT shop_cart_item_id FROM cmw_shops_cart_items WHERE shop_cart_item_aside = 1 AND shop_item_id =:shop_item_id AND shop_user_id = :shop_user_id";
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

    /**
     * @return string
     * @desc Get the first image by item Id
     */
    public function getFirstImageByItemId(int $itemId): string
    {
        $sql = "SELECT `shop_image_name` FROM cmw_shops_images WHERE shop_item_id = :shop_item_id LIMIT 1;";
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if (!$req->execute(["shop_item_id" => $itemId])) {
            return 0;
        }
        $res = $req->fetch();
        if (!$res) {
            return 0;
        }
        return $res['shop_image_name'] ?? 0;
    }

    /**
     * @param int $itemId
     * @param int|null $userId
     * @param string $sessionId
     * @return int
     * @desc Get the current quantity of item in cart
     */
    public function getQuantity(int $itemId, ?int $userId, string $sessionId): int
    {
        $data = ["shop_item_id" => $itemId];

        $sql = "SELECT shop_cart_item_quantity FROM cmw_shops_cart_items
                               WHERE shop_item_id = :shop_item_id";

        if (is_null($userId)){
            $sql .= " AND shop_client_session_id = :session_id";
            $data['session_id'] = $sessionId;
        } else {
            $sql .= " AND shop_user_id = :user_id";
            $data["user_id"] = $userId;
        }

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if (!$req->execute($data)) {
            return 0;
        }
        $res = $req->fetch();
        if (!$res) {
            return 0;
        }
        return $res['shop_cart_item_quantity'] ?? 0;
    }

    /**
     * @param int $itemId
     * @param int|null $userId
     * @param string $sessionId
     * @param bool $increase
     * @return void
     * @desc increase the quantity of item
     */
    public function increaseQuantity(int $itemId, ?int $userId, string $sessionId, bool $increase): void
    {
        $quantity = $this->getQuantity($itemId, $userId, $sessionId);

        if ($increase){
            ++$quantity;
        } else {
            --$quantity;
        }

        $data = [
            "shop_cart_item_quantity" => $quantity,
            "shop_item_id" => $itemId,
        ];

        $sql = "UPDATE cmw_shops_cart_items SET shop_cart_item_quantity = :shop_cart_item_quantity 
                            WHERE shop_item_id = :shop_item_id";

        if (is_null($userId)) {
            $sql .= " AND shop_client_session_id = :session_id";
            $data["session_id"] = $sessionId;
        } else {
            $sql .= " AND shop_user_id = :shop_user_id";
            $data["shop_user_id"] = $userId;
        }

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        $req->execute($data);
    }

    public function removeItem(int $itemId, ?int $userId, string $sessionId): bool
    {
        $data = ['shop_item_id' => $itemId];

        $sql = "DELETE FROM cmw_shops_cart_items WHERE shop_item_id = :shop_item_id";

        if (is_null($userId)){
            $sql .= " AND shop_client_session_id = :session_id";
            $data['session_id'] = $sessionId;
        } else {
            $sql .= " AND shop_user_id = :user_id";
            $data['user_id'] = $userId;
        }

        $db = DatabaseManager::getInstance();

        return $db->prepare($sql)->execute($data);
    }

    public function cartItemIdAsNullValue(?int $userId, string $sessionId): bool
    {
        $sql = "SELECT shop_cart_item_id FROM cmw_shops_cart_items WHERE shop_item_id IS NULL";

        if (is_null($userId)){
            $sql .= " AND shop_client_session_id = :session_id";
            $data['session_id'] = $sessionId;
        } else {
            $sql .= " AND shop_user_id = :user_id";
            $data['user_id'] = $userId;
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

    public function removeSessionCart(string $sessionId): bool
    {
        $data = ['shop_client_session_id' => $sessionId];

        $sql = "DELETE FROM cmw_shops_cart_items WHERE shop_client_session_id = :shop_client_session_id";

        $db = DatabaseManager::getInstance();

        return $db->prepare($sql)->execute($data);
    }

    public function removeUnreachableItem(?int $userId, string $sessionId): bool
    {
        $sql = "DELETE FROM cmw_shops_cart_items WHERE shop_item_id IS NULL";

        if (is_null($userId)){
            $sql .= " AND shop_client_session_id = :session_id";
            $data['session_id'] = $sessionId;
        } else {
            $sql .= " AND shop_user_id = :user_id";
            $data['user_id'] = $userId;
        }

        $db = DatabaseManager::getInstance();

        return $db->prepare($sql)->execute($data);
    }

    public function itemIsInCart(?int $itemId, ?int $userId, string $sessionId): bool
    {
        if ($itemId === null) {
            return false;
        }

        $var = ["shop_item_id" => $itemId];

        $sql = "SELECT shop_cart_item_id FROM `cmw_shops_cart_items` 
                         WHERE shop_item_id = :shop_item_id";

        if (is_null($userId)) {
            $sql .= " AND shop_client_session_id = :session_id";
            $var["session_id"] = $sessionId;
        } else {
            $sql .= " AND shop_user_id = :shop_user_id";
            $var["shop_user_id"] = $userId;
        }

        $db = DatabaseManager::getInstance();
        $res = $db->prepare($sql);

        $res->execute($var);

        return count($res->fetchAll()) === 0;
    }

    public function userHaveAlreadyItemInCart (?int $itemId ,string $userId) : bool
    {
        if ($itemId === null) {
            return false;
        }

        $data = ["shop_item_id" => $itemId, "shop_user_id" => $userId];

        $sql = "SELECT shop_cart_item_id FROM `cmw_shops_cart_items` WHERE shop_item_id = :shop_item_id AND shop_user_id = :shop_user_id";

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

    public function switchSessionToUserCart(int $itemId, string $session_id, mixed $userId): void
    {
        $sql = "UPDATE cmw_shops_cart_items SET shop_user_id = :user_id, shop_client_session_id = null 
                            WHERE shop_client_session_id = :session_id AND shop_item_id = :shop_item_id";

        $db = DatabaseManager::getInstance();
        $db->prepare($sql)->execute(['user_id' => $userId, 'session_id' => $session_id, 'shop_item_id' => $itemId]);
    }

    public function updateQuantity(?int $userId, string $sessionId, int $itemId, int $quantity) : void
    {
        $data = [
            "shop_cart_item_quantity" => $quantity,
            "shop_item_id" => $itemId,
        ];

        $sql = "UPDATE cmw_shops_cart_items SET shop_cart_item_quantity = :shop_cart_item_quantity 
                            WHERE shop_item_id = :shop_item_id";

        if (is_null($userId)) {
            $sql .= " AND shop_client_session_id = :session_id";
            $data["session_id"] = $sessionId;
        } else {
            $sql .= " AND shop_user_id = :shop_user_id";
            $data["shop_user_id"] = $userId;
        }

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        $req->execute($data);
    }

}