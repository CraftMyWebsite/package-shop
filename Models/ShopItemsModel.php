<?php

namespace CMW\Model\Shop;

use CMW\Entity\Shop\ShopCategoryEntity;
use CMW\Entity\Shop\ShopItemEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Utils\Utils;


/**
 * Class: @ShopItemsModel
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ShopItemsModel extends AbstractModel
{

    /**
     * @return \CMW\Entity\Shop\ShopItemEntity []
     */
    public function getShopItems(): array
    {

        $sql = "SELECT shop_category_id FROM cmw_shops_items ORDER BY shop_item_id ASC";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($cat = $res->fetch()) {
            $toReturn[] = $this->getShopItemsByCategoryId($cat["shop_category_id"]);
        }

        return $toReturn;

    }

    public function getShopItemsByCategoryId(int $id): ?ShopItemEntity
    {
        $sql = "SELECT * FROM cmw_shops_items WHERE shop_category_id = :shop_category_id";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("shop_category_id" => $id))) {
            return null;
        }

        $res = $res->fetch();

        return new ShopItemEntity(
            $res["shop_item_id"],
            $res["shop_category_id"]?? null,
            $res["shop_item_name"]?? null,
            $res["shop_item_description"],
            $res["shop_item_slug"],
            $res["shop_image_id"] ?? null,
            $res["shop_item_type"],
            $res["shop_item_default_stock"]?? null,
            $res["shop_item_current_stock"]?? null,
            $res["shop_item_price"]?? null,
            $res["shop_item_global_limit"]?? null,
            $res["shop_item_user_limit"]?? null,
            $res["shop_item_created_at"],
            $res["shop_item_updated_at"]
        );
    }


}