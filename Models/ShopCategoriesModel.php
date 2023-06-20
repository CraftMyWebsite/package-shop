<?php

namespace CMW\Model\Forum;

use CMW\Entity\Shop\ShopCategoryEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;


/**
 * Class: @CategoryModel
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ShopCategoriesModel extends AbstractModel
{

    /**
     * @return \CMW\Entity\Shop\ShopCategoryEntity []
     */
    public function getCategories(): array
    {

        $sql = "SELECT shop_category_id FROM cmw_shops_categories";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($cat = $res->fetch()) {
            $toReturn[] = $this->getCategoryById($cat["shop_category_id"]);
        }

        return $toReturn;

    }

    public function getCategoryById(int $id): ?ShopCategoryEntity
    {
        $sql = "SELECT * FROM cmw_shops_categories WHERE shop_category_id = :shop_category_id";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("shop_category_id" => $id))) {
            return null;
        }

        $res = $res->fetch();

        return new ShopCategoryEntity(
            $res["shop_category_id"],
            $res["shop_category_name"],
            $res["shop_category_description"],
            $res["shop_category_slug"],
            $res["shop_image_id"],
            $res["shop_category_created_at"],
            $res["shop_category_updated_at"]
        );
    }


}