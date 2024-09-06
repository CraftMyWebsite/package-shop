<?php

namespace CMW\Model\Shop\Cart;

use CMW\Entity\Shop\Carts\ShopCartDiscountEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Model\Shop\Discount\ShopDiscountModel;

/**
 * Class: @ShopCartDiscountModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopCartDiscountModel extends AbstractModel
{
    /**
     * @param int $id
     * @return \CMW\Entity\Shop\Carts\ShopCartDiscountEntity | null
     */
    public function getCartDiscountById(int $id): ?ShopCartDiscountEntity
    {
        $sql = 'SELECT * FROM cmw_shops_cart_discounts WHERE shop_cart_discount_id = :shop_cart_discount_id';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(['shop_cart_discount_id' => $id])) {
            return null;
        }

        $res = $res->fetch();

        $cart = ShopCartModel::getInstance()->getShopCartsById($res['shop_cart_id']);
        $discount = ShopDiscountModel::getInstance()->getShopDiscountById($res['shop_discount_id']);

        return new ShopCartDiscountEntity(
            $res['shop_cart_discount_id'],
            $cart,
            $discount
        );
    }

    /**
     * @return \CMW\Entity\Shop\Carts\ShopCartDiscountEntity []
     */
    public function getCartDiscountByUserId(?int $userId, string $sessionId): array
    {
        if (ShopCartModel::getInstance()->cartExist($userId, $sessionId)) {
            $cartId = ShopCartModel::getInstance()->getShopCartsByUserOrSessionId($userId, $sessionId)->getId();
        } else {
            $cartId = null;
        }

        $sql = 'SELECT shop_cart_discount_id FROM cmw_shops_cart_discounts WHERE shop_cart_id = :shop_cart_id';

        $data = ['shop_cart_id' => $cartId];

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute($data)) {
            return [];
        }

        $toReturn = [];

        while ($cart = $res->fetch()) {
            $toReturn[] = $this->getCartDiscountById($cart['shop_cart_discount_id']);
        }

        return $toReturn;
    }

    /**
     * @return \CMW\Entity\Shop\Carts\ShopCartDiscountEntity []
     */
    public function getCartDiscount(): array
    {
        $sql = 'SELECT shop_cart_discount_id FROM cmw_shops_cart_discounts';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return [];
        }

        $toReturn = [];

        while ($cart = $res->fetch()) {
            $toReturn[] = $this->getCartDiscountById($cart['shop_cart_discount_id']);
        }

        return $toReturn;
    }

    public function applyCode(?int $userId, string $sessionId, int $discountId): ?ShopCartDiscountEntity
    {
        if (ShopCartModel::getInstance()->cartExist($userId, $sessionId)) {
            $cartId = ShopCartModel::getInstance()->getShopCartsByUserOrSessionId($userId, $sessionId)->getId();
        } else {
            $cartId = null;
        }

        $data = array(
            'shop_cart_id' => $cartId,
            'shop_discount_id' => $discountId,
        );

        $sql = 'INSERT INTO cmw_shops_cart_discounts (shop_cart_id, shop_discount_id) VALUES (:shop_cart_id, :shop_discount_id)';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $id = $db->lastInsertId();
            return $this->getCartDiscountById($id);
        }

        return null;
    }

    public function removeCode(int $cartId, int $discountId): bool
    {
        $sql = 'DELETE FROM cmw_shops_cart_discounts WHERE shop_cart_id = :shop_cart_id AND shop_discount_id = :shop_discount_id';

        $db = DatabaseManager::getInstance();

        return $db->prepare($sql)->execute(array('shop_cart_id' => $cartId, 'shop_discount_id' => $discountId));
    }
}
