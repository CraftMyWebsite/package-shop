<?php

namespace CMW\Model\Shop\Cart;

use CMW\Entity\Shop\Carts\ShopCartItemEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Model\Shop\Discount\ShopDiscountModel;
use CMW\Model\Shop\Item\ShopItemsModel;

/**
 * Class: @ShopCartItemModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopCartItemModel extends AbstractModel
{
    private ShopItemsModel $shopItemsModel;

    public function __construct()
    {
        $this->shopItemsModel = new ShopItemsModel();
    }

    public function getShopCartsItemsById(int $id): ?ShopCartItemEntity
    {
        $sql = 'SELECT * FROM cmw_shops_cart_items WHERE shop_cart_item_id = :shop_cart_item_id';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(['shop_cart_item_id' => $id])) {
            return null;
        }

        $res = $res->fetch();

        $cart = ShopCartModel::getInstance()->getShopCartsById($res['shop_cart_id']);
        $item = $this->shopItemsModel->getShopItemsById($res['shop_item_id']);
        $discount = is_null($res['shop_discount_id']) ? null : ShopDiscountModel::getInstance()->getShopDiscountById($res['shop_discount_id']);

        return new ShopCartItemEntity(
            $res['shop_cart_item_id'],
            $cart,
            $item ?? null,
            $discount ?? null,
            $res['shop_cart_item_quantity'],
            $res['shop_cart_item_created_at'],
            $res['shop_cart_item_updated_at'],
            $res['shop_cart_item_aside']
        );
    }

    /**
     * @return \CMW\Entity\Shop\Carts\ShopCartItemEntity []
     */
    public function getShopCartsItemsByUserId(?int $userId, string $sessionId): array
    {
        if (ShopCartModel::getInstance()->cartExist($userId, $sessionId)) {
            $cartId = ShopCartModel::getInstance()->getShopCartsByUserOrSessionId($userId, $sessionId)->getId();
        } else {
            $cartId = null;
        }

        $sql = 'SELECT csci.shop_cart_item_id, csci.shop_item_id
                FROM cmw_shops_cart_items csci
                JOIN cmw_shops_items csi ON csci.shop_item_id = csi.shop_item_id
                WHERE csi.shop_item_archived = 0
                AND csci.shop_cart_item_aside = 0
                AND csci.shop_cart_id = :shop_cart_id';

        $data = ['shop_cart_id' => $cartId];

        $sql .= ' ORDER BY shop_cart_item_id';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute($data)) {
            return [];
        }

        $toReturn = [];

        while ($cart = $res->fetch()) {
            $toReturn[] = $this->getShopCartsItemsById($cart['shop_cart_item_id']);
        }

        $this->clearArchivedItemsFromCart($userId, $sessionId);

        return $toReturn;
    }

    /**
     * @return \CMW\Entity\Shop\Carts\ShopCartItemEntity []
     */
    public function getShopCartsItemsAsideByUserId(?int $userId, string $sessionId): array
    {
        if (ShopCartModel::getInstance()->cartExist($userId, $sessionId)) {
            $cartId = ShopCartModel::getInstance()->getShopCartsByUserOrSessionId($userId, $sessionId)->getId();
        } else {
            $cartId = null;
        }

        $sql = 'SELECT csci.shop_cart_item_id, csci.shop_item_id
                FROM cmw_shops_cart_items csci
                JOIN cmw_shops_items csi ON csci.shop_item_id = csi.shop_item_id
                WHERE csi.shop_item_archived = 0
                AND csci.shop_cart_item_aside = 1
                AND csci.shop_cart_id = :shop_cart_id';

        $data = ['shop_cart_id' => $cartId];

        $sql .= ' ORDER BY shop_cart_item_id';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute($data)) {
            return [];
        }

        $toReturn = [];

        while ($cart = $res->fetch()) {
            $toReturn[] = $this->getShopCartsItemsById($cart['shop_cart_item_id']);
        }

        $this->clearArchivedItemsFromCart($userId, $sessionId);

        return $toReturn;
    }

    public function getShopCartsByItemIdAndUserId(int $itemId, ?int $userId, $sessionId): ?ShopCartItemEntity
    {
        if (ShopCartModel::getInstance()->cartExist($userId, $sessionId)) {
            $cartId = ShopCartModel::getInstance()->getShopCartsByUserOrSessionId($userId, $sessionId)->getId();
        } else {
            $cartId = null;
        }

        $data = ['shop_item_id' => $itemId, 'shop_cart_id' => $cartId];

        $sql = 'SELECT * FROM cmw_shops_cart_items WHERE shop_item_id = :shop_item_id AND shop_cart_id = :shop_cart_id';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute($data)) {
            return null;
        }

        $res = $res->fetch();

        return $this->getShopCartsItemsById($res['shop_cart_item_id']);
    }

    /**
     * @return \CMW\Entity\Shop\Carts\ShopCartItemEntity []
     */
    public function getCartArchivedItems(?int $userId, string $sessionId): array
    {
        if (ShopCartModel::getInstance()->cartExist($userId, $sessionId)) {
            $cartId = ShopCartModel::getInstance()->getShopCartsByUserOrSessionId($userId, $sessionId)->getId();
        } else {
            $cartId = null;
        }

        $sql = 'SELECT csci.shop_cart_item_id, csci.shop_item_id
                FROM cmw_shops_cart_items csci
                JOIN cmw_shops_items csi ON csci.shop_item_id = csi.shop_item_id
                WHERE csi.shop_item_archived = 1
                AND csci.shop_cart_id = :shop_cart_id';

        $data = ['shop_cart_id' => $cartId];

        $sql .= ' ORDER BY shop_cart_item_id';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute($data)) {
            return [];
        }

        $toReturn = [];

        while ($cart = $res->fetch()) {
            $toReturn[] = $this->getShopCartsItemsById($cart['shop_cart_item_id']);
        }

        return $toReturn;
    }

    public function clearArchivedItemsFromCart(?int $userId, string $sessionId): void
    {
        foreach ($this->getCartArchivedItems($userId, $sessionId) as $archivedItem) {
            $this->removeItem($archivedItem->getItem()->getId(), $userId, $sessionId);
        }
    }

    public function removeItem(int $itemId, ?int $userId, string $sessionId): bool
    {
        if (ShopCartModel::getInstance()->cartExist($userId, $sessionId)) {
            $cartId = ShopCartModel::getInstance()->getShopCartsByUserOrSessionId($userId, $sessionId)->getId();
        } else {
            $cartId = null;
        }

        $data = ['shop_cart_id' => $cartId, 'shop_item_id' => $itemId];

        $sql = 'DELETE FROM cmw_shops_cart_items WHERE shop_item_id = :shop_item_id AND shop_cart_id = :shop_cart_id';

        $db = DatabaseManager::getInstance();

        return $db->prepare($sql)->execute($data);
    }

    public function removeItemByCartItemId(int $carItemId): bool
    {
        $data = ['shop_cart_item_id' => $carItemId];

        $sql = 'DELETE FROM cmw_shops_cart_items WHERE shop_cart_item_id = :shop_cart_item_id';

        $db = DatabaseManager::getInstance();

        return $db->prepare($sql)->execute($data);
    }

    public function removeItemForAllCart(int $itemId): bool
    {
        $data = ['shop_item_id' => $itemId];

        $sql = 'DELETE FROM cmw_shops_cart_items WHERE shop_item_id = :shop_item_id';

        $db = DatabaseManager::getInstance();

        return $db->prepare($sql)->execute($data);
    }

    /**
     * @param ?int $userId
     * @param string $sessionId
     * @return string
     * @desc count number of items in cart
     */
    public function countItemsByUserId(?int $userId, string $sessionId): mixed
    {
        if (ShopCartModel::getInstance()->cartExist($userId, $sessionId)) {
            $cartId = ShopCartModel::getInstance()->getShopCartsByUserOrSessionId($userId, $sessionId)->getId();
        } else {
            $cartId = null;
        }

        $sql = 'SELECT COUNT(shop_cart_item_id) AS count
                FROM cmw_shops_cart_items csci
                JOIN cmw_shops_items csi ON csci.shop_item_id = csi.shop_item_id
                WHERE csi.shop_item_archived = 0 AND csci.shop_cart_id = :shop_cart_id';

        $data = ['shop_cart_id' => $cartId];

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute($data)) {
            return 0;
        }

        return $res->fetch(0)['count'];
    }

    public function addToCart(int $itemId, ?int $userId, string $sessionId, int $quantity): ?ShopCartItemEntity
    {
        if (ShopCartModel::getInstance()->cartExist($userId, $sessionId)) {
            $cartId = ShopCartModel::getInstance()->getShopCartsByUserOrSessionId($userId, $sessionId)->getId();
        } else {
            $cartId = ShopCartModel::getInstance()->createCart($userId, $sessionId)->getId();
        }

        $data = [
            'shop_item_id' => $itemId,
            'shop_cart_item_quantity' => $quantity,
            'shop_cart_id' => $cartId,
        ];

        $sql = 'INSERT INTO cmw_shops_cart_items(shop_item_id, shop_cart_id, shop_cart_item_quantity) VALUES (:shop_item_id, :shop_cart_id, :shop_cart_item_quantity)';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $id = $db->lastInsertId();
            return $this->getShopCartsItemsById($id);
        }

        return null;
    }

    public function addToAsideCart(int $itemId, ?int $userId, string $sessionId): ?ShopCartItemEntity
    {
        if (ShopCartModel::getInstance()->cartExist($userId, $sessionId)) {
            $cartId = ShopCartModel::getInstance()->getShopCartsByUserOrSessionId($userId, $sessionId)->getId();
        } else {
            $cartId = ShopCartModel::getInstance()->createCart($userId, $sessionId)->getId();
        }

        $data = [
            'shop_item_id' => $itemId,
            'shop_cart_id' => $cartId,
        ];

        $sql = 'INSERT INTO cmw_shops_cart_items(shop_item_id, shop_cart_id, shop_cart_item_aside) VALUES (:shop_item_id, :shop_cart_id, 1)';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $id = $db->lastInsertId();
            return $this->getShopCartsItemsById($id);
        }

        return null;
    }

    public function switchAsideToCart(int $itemId, ?int $userId, string $sessionId): void
    {
        if (ShopCartModel::getInstance()->cartExist($userId, $sessionId)) {
            $cartId = ShopCartModel::getInstance()->getShopCartsByUserOrSessionId($userId, $sessionId)->getId();
        } else {
            $cartId = ShopCartModel::getInstance()->createCart($userId, $sessionId)->getId();
        }

        $data = [
            'shop_item_id' => $itemId,
            'shop_cart_id' => $cartId,
        ];

        $sql = 'UPDATE cmw_shops_cart_items SET shop_cart_item_aside = 0 WHERE shop_item_id = :shop_item_id AND shop_cart_id = :shop_cart_id';

        $db = DatabaseManager::getInstance();
        $db->prepare($sql)->execute($data);
    }

    public function switchAsideToCartByCartItemId(int $cartItemId): void
    {

        $data = [
            'shop_cart_item_id' => $cartItemId,
        ];

        $sql = 'UPDATE cmw_shops_cart_items SET shop_cart_item_aside = 0 WHERE shop_cart_item_id = :shop_cart_item_id';

        $db = DatabaseManager::getInstance();
        $db->prepare($sql)->execute($data);
    }

    public function switchCartToAside(int $cartId): void
    {
        $data = [
            'shop_cart_item_id' => $cartId,
        ];

        $sql = 'UPDATE cmw_shops_cart_items SET shop_cart_item_aside = 1 WHERE shop_cart_item_id = :shop_cart_item_id';

        $db = DatabaseManager::getInstance();
        $db->prepare($sql)->execute($data);
    }

    public function isAlreadyAside(int $itemId, ?int $userId, string $sessionId): bool
    {
        if (ShopCartModel::getInstance()->cartExist($userId, $sessionId)) {
            $cartId = ShopCartModel::getInstance()->getShopCartsByUserOrSessionId($userId, $sessionId)->getId();
        } else {
            $cartId = ShopCartModel::getInstance()->createCart($userId, $sessionId)->getId();
        }

        $data = [
            'shop_item_id' => $itemId,
            'shop_cart_id' => $cartId,
        ];

        $sql = 'SELECT shop_cart_item_id FROM cmw_shops_cart_items WHERE shop_cart_item_aside = 1 AND shop_item_id =:shop_item_id AND shop_cart_id = :shop_cart_id';

        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute($data)) {
            return true;
        }

        $res = $req->fetch();

        if (!$res) {
            return false;
        }

        return true;
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
        if (ShopCartModel::getInstance()->cartExist($userId, $sessionId)) {
            $cartId = ShopCartModel::getInstance()->getShopCartsByUserOrSessionId($userId, $sessionId)->getId();
        } else {
            $cartId = null;
        }

        $data = [
            'shop_item_id' => $itemId,
            'shop_cart_id' => $cartId,
        ];

        $sql = 'SELECT shop_cart_item_quantity FROM cmw_shops_cart_items WHERE shop_item_id = :shop_item_id AND shop_cart_id = :shop_cart_id';

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
     * @param int $carItemId
     * @param int|null $userId
     * @param string $sessionId
     * @return int
     * @desc Get the current quantity of item in cart
     */
    public function getQuantityByCartItemId(int $carItemId): int
    {
        $data = [
            'shop_cart_item_id' => $carItemId,
        ];

        $sql = 'SELECT shop_cart_item_quantity FROM cmw_shops_cart_items WHERE shop_cart_item_id = :shop_cart_item_id';

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

        if ($increase) {
            ++$quantity;
        } else {
            --$quantity;
        }

        if (ShopCartModel::getInstance()->cartExist($userId, $sessionId)) {
            $cartId = ShopCartModel::getInstance()->getShopCartsByUserOrSessionId($userId, $sessionId)->getId();
        } else {
            $cartId = null;
        }

        $data = [
            'shop_cart_item_quantity' => $quantity,
            'shop_item_id' => $itemId,
            'shop_cart_id' => $cartId,
        ];

        $sql = 'UPDATE cmw_shops_cart_items SET shop_cart_item_quantity = :shop_cart_item_quantity WHERE shop_item_id = :shop_item_id AND shop_cart_id = :shop_cart_id';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        $req->execute($data);
    }

    /**
     * @param int $carItemId
     * @param int|null $userId
     * @param string $sessionId
     * @param bool $increase
     * @return void
     * @desc increase the quantity of item
     */
    public function increaseQuantityByCartItemId(int $carItemId, bool $increase): void
    {
        $quantity = $this->getQuantityByCartItemId($carItemId);

        if ($increase) {
            ++$quantity;
        } else {
            --$quantity;
        }

        $data = [
            'shop_cart_item_quantity' => $quantity,
            'shop_cart_item_id' => $carItemId,
        ];

        $sql = 'UPDATE cmw_shops_cart_items SET shop_cart_item_quantity = :shop_cart_item_quantity WHERE shop_cart_item_id = :shop_cart_item_id';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        $req->execute($data);
    }

    public function cartItemIdAsNullValue(?int $userId, string $sessionId): bool
    {
        if (ShopCartModel::getInstance()->cartExist($userId, $sessionId)) {
            $cartId = ShopCartModel::getInstance()->getShopCartsByUserOrSessionId($userId, $sessionId)->getId();
        } else {
            $cartId = null;
        }

        $data = [
            'shop_cart_id' => $cartId,
        ];

        $sql = 'SELECT shop_cart_item_id FROM cmw_shops_cart_items WHERE shop_item_id IS NULL AND shop_cart_id = :shop_cart_id';

        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute($data)) {
            return true;
        }

        $res = $req->fetch();

        if (!$res) {
            return false;
        }

        return true;
    }

    public function removeUnreachableItem(?int $userId, string $sessionId): bool
    {
        if (ShopCartModel::getInstance()->cartExist($userId, $sessionId)) {
            $cartId = ShopCartModel::getInstance()->getShopCartsByUserOrSessionId($userId, $sessionId)->getId();
        } else {
            $cartId = null;
        }

        $data = [
            'shop_cart_id' => $cartId,
        ];

        $sql = 'DELETE FROM cmw_shops_cart_items WHERE shop_item_id IS NULL AND shop_cart_id = :shop_cart_id';

        $db = DatabaseManager::getInstance();

        return $db->prepare($sql)->execute($data);
    }

    public function itemIsInCart(?int $itemId, ?int $userId, string $sessionId): bool
    {
        if ($itemId === null) {
            return false;
        }

        if (ShopCartModel::getInstance()->cartExist($userId, $sessionId)) {
            $cartId = ShopCartModel::getInstance()->getShopCartsByUserOrSessionId($userId, $sessionId)->getId();
        } else {
            $cartId = null;
        }

        $var = ['shop_item_id' => $itemId, 'shop_cart_id' => $cartId];

        $sql = 'SELECT shop_cart_item_id FROM `cmw_shops_cart_items`  WHERE shop_item_id = :shop_item_id AND shop_cart_id = :shop_cart_id';

        $db = DatabaseManager::getInstance();
        $res = $db->prepare($sql);

        $res->execute($var);

        return count($res->fetchAll()) === 0;
    }

    public function userHaveAlreadyItemInCart(?int $itemId, string $userId): bool
    {
        if ($itemId === null) {
            return false;
        }

        if (ShopCartModel::getInstance()->cartExist($userId, '')) {
            $cartId = ShopCartModel::getInstance()->getShopCartsByUserOrSessionId($userId, '')->getId();
        } else {
            $cartId = null;
        }

        $data = ['shop_item_id' => $itemId, 'shop_cart_id' => $cartId];

        $sql = 'SELECT shop_cart_item_id FROM `cmw_shops_cart_items` WHERE shop_item_id = :shop_item_id AND shop_cart_id = :shop_cart_id';

        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute($data)) {
            return true;
        }

        $res = $req->fetch();

        if (!$res) {
            return false;
        }

        return true;
    }

    public function itemIsPresentInACart(?int $itemId): bool
    {
        if ($itemId === null) {
            return false;
        }

        $var = ['shop_item_id' => $itemId];

        $sql = 'SELECT shop_cart_item_id FROM `cmw_shops_cart_items` WHERE shop_item_id = :shop_item_id';

        $db = DatabaseManager::getInstance();
        $res = $db->prepare($sql);

        $res->execute($var);

        return count($res->fetchAll()) === 0;
    }

    public function updateQuantity(?int $userId, string $sessionId, int $itemId, int $quantity): void
    {
        if (ShopCartModel::getInstance()->cartExist($userId, $sessionId)) {
            $cartId = ShopCartModel::getInstance()->getShopCartsByUserOrSessionId($userId, $sessionId)->getId();
        } else {
            $cartId = null;
        }

        $data = [
            'shop_cart_item_quantity' => $quantity,
            'shop_item_id' => $itemId,
            'shop_cart_id' => $cartId
        ];

        $sql = 'UPDATE cmw_shops_cart_items SET shop_cart_item_quantity = :shop_cart_item_quantity 
                            WHERE shop_item_id = :shop_item_id AND shop_cart_id = :shop_cart_id';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        $req->execute($data);
    }

    public function applyCodeToItem(?int $userId, string $sessionId, int $itemId, int $discountId): void
    {
        if (ShopCartModel::getInstance()->cartExist($userId, $sessionId)) {
            $cartId = ShopCartModel::getInstance()->getShopCartsByUserOrSessionId($userId, $sessionId)->getId();
        } else {
            $cartId = ShopCartModel::getInstance()->createCart($userId, $sessionId)->getId();
        }

        $data = [
            'shop_item_id' => $itemId,
            'shop_cart_id' => $cartId,
            'shop_discount_id' => $discountId,
        ];

        $sql = 'UPDATE cmw_shops_cart_items SET shop_discount_id = :shop_discount_id WHERE shop_item_id = :shop_item_id AND shop_cart_id = :shop_cart_id';

        $db = DatabaseManager::getInstance();
        $db->prepare($sql)->execute($data);
    }

    public function removeCodeToItem(?int $userId, string $sessionId, int $itemId, int $discountId): void
    {
        if (ShopCartModel::getInstance()->cartExist($userId, $sessionId)) {
            $cartId = ShopCartModel::getInstance()->getShopCartsByUserOrSessionId($userId, $sessionId)->getId();
        } else {
            $cartId = ShopCartModel::getInstance()->createCart($userId, $sessionId)->getId();
        }

        $data = [
            'shop_item_id' => $itemId,
            'shop_cart_id' => $cartId,
            'shop_discount_id' => $discountId,
        ];

        $sql = 'UPDATE cmw_shops_cart_items SET shop_discount_id = NULL WHERE shop_item_id = :shop_item_id AND shop_cart_id = :shop_cart_id AND shop_discount_id = :shop_discount_id';

        $db = DatabaseManager::getInstance();
        $db->prepare($sql)->execute($data);
    }
}
