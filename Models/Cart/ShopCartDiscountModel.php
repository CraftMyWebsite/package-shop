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
        $sql = "SELECT * FROM cmw_shops_cart_discounts WHERE shop_cart_discounts_id = :shop_cart_discounts_id";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(["shop_cart_discounts_id" => $id])) {
            return null;
        }

        $res = $res->fetch();

        $cart = //TODO : En attente de la table cmw_shops_cart qui devra lié les articles et les discount sous le même panier
        $discount = ShopDiscountModel::getInstance()->getShopDiscountById($res["shop_discount_id"]);

        return new ShopCartDiscountEntity(
            $res["shop_cart_discounts_id"],
            $cart,
            $discount
        );
    }
}