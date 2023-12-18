<?php

namespace CMW\Model\Shop;

use CMW\Entity\Shop\ShopPaymentDiscountEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;

/**
 * Class: @ShopPaymentDiscountModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopPaymentDiscountModel extends AbstractModel
{
    private ShopItemsModel $shopItemsModel;
    private ShopCategoriesModel $shopCategoriesModel;
    public function __construct()
    {
        $this->shopItemsModel = new ShopItemsModel();
        $this->shopCategoriesModel = new ShopCategoriesModel();
    }

    public function getPaymentDiscountById(int $id): ?ShopPaymentDiscountEntity
    {
        $sql = "SELECT * FROM cmw_shops_payment_discount WHERE shop_payment_discount_id = :shop_payment_discount_id";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("shop_payment_discount_id" => $id))) {
            return null;
        }

        $res = $res->fetch();

        $item = is_null($res["shop_item_id"]) ? null : $this->shopItemsModel->getShopItemsById($res["shop_item_id"]);
        $category = is_null($res["shop_category_id"]) ? null : $this->shopCategoriesModel->getShopCategoryById($res["shop_category_id"]);


        return new ShopPaymentDiscountEntity(
            $res["shop_payment_discount_id"],
            $res["shop_payment_discount_name"],
            $res["shop_payment_discount_description"],
            $res["shop_payment_discount_start_date"],
            $res["shop_payment_discount_end_date"],
            $res["shop_payment_discount_default_uses"],
            $res["shop_payment_discount_uses_left"],
            $res["shop_payment_discount_percent"],
            $res["shop_payment_discount_price"],
            $res["shop_payment_discount_use_multiple_per_users"],
            $res["shop_payment_discount_cumulative"],
            $res["shop_payment_discount_status"],
            $item,
            $category,
            $res["shop_payment_discount_code"],
            $res["shop_payment_discount_default_active"],
            $res["shop_payment_discount_users_need_purchase_before_use"],
            $res["shop_payment_discount_created_at"],
            $res["shop_payment_discount_updated_at"]
        );
    }

    /**
     * @return \CMW\Entity\Shop\ShopPaymentDiscountEntity []
     */
    public function getPaymentDiscount(): array
    {

        $sql = "SELECT shop_payment_discount_id FROM cmw_shops_payment_discount ORDER BY shop_payment_discount_id DESC";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($paymentDiscount = $res->fetch()) {
            $toReturn[] = $this->getPaymentDiscountById($paymentDiscount["shop_payment_discount_id"]);
        }

        return $toReturn;

    }
}