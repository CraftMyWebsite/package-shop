<?php

namespace CMW\Model\Shop\Cart;

use CMW\Entity\Shop\Carts\ShopCartVariantesEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Model\Shop\Item\ShopItemVariantValueModel;

/**
 * Class: @ShopCartVariantesModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopCartVariantesModel extends AbstractModel
{
    private ShopCartsModel $cartModel;
    private ShopItemVariantValueModel $itemVariantModel;

    public function __construct()
    {
        $this->cartModel = new ShopCartsModel();
        $this->itemVariantModel = new ShopItemVariantValueModel();
    }

    /**
     * @param int $id
     * @return \CMW\Entity\Shop\Carts\ShopCartVariantesEntity | null
     */
    public function getCartsItemsVariantById(int $id): ?ShopCartVariantesEntity
    {
        $sql = "SELECT * FROM cmw_shops_cart_items_variantes WHERE shop_cart_items_variantes_id = :shop_cart_items_variantes_id";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(["shop_cart_items_variantes_id" => $id])) {
            return null;
        }

        $res = $res->fetch();

        $cart = $this->cartModel->getShopCartsById($res["shop_cart_item_id"]);
        $itemVariantValue = $this->itemVariantModel->getShopItemVariantValueById($res["shop_variants_values_id"]);


        return new ShopCartVariantesEntity(
            $res["shop_cart_items_variantes_id"],
            $cart,
            $itemVariantValue,
            $res["shop_cart_items_variantes_created_at"],
            $res["shop_cart_items_variantes_updated_at"]
        );
    }

    public function setVariantToItemInCart(int $cartId, int $variantValueId): ?ShopCartVariantesEntity
    {
        $data = array(
            "shop_cart_item_id" => $cartId,
            "shop_variants_values_id" => $variantValueId,
        );

        $sql = "INSERT INTO cmw_shops_cart_items_variantes (shop_cart_item_id, shop_variants_values_id)
                VALUES (:shop_cart_item_id, :shop_variants_values_id)";

        if (is_null($cartId)) {
            return null;
        }

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $id = $db->lastInsertId();
            return $this->getCartsItemsVariantById($id);
        }

        return null;
    }

    /**
     * @param int $id
     * @return \CMW\Entity\Shop\Carts\ShopCartVariantesEntity []
     */
    public function getShopItemVariantValueByCartId(int $id): array
    {
        $sql = "SELECT * FROM cmw_shops_cart_items_variantes WHERE shop_cart_item_id = :shop_cart_item_id";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("shop_cart_item_id" => $id))) {
            return [];
        }

        $toReturn = [];

        while ($variants = $res->fetch()) {
            $toReturn[] = $this->getCartsItemsVariantById($variants["shop_cart_items_variantes_id"]);
        }

        return $toReturn;
    }
}