<?php

namespace CMW\Model\Shop;

use CMW\Entity\Shop\ShopImageEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Manager\Uploads\ImagesManager;


/**
 * Class: @ShopImagesModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopImagesModel extends AbstractModel
{
    public function getShopImagesById(int $id): ?ShopImageEntity
    {
        $sql = "SELECT * FROM cmw_shops_images WHERE shop_image_id = :shop_image_id";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("shop_image_id" => $id))) {
            return null;
        }

        $res = $res->fetch();

        return new ShopImageEntity(
            $res["shop_image_id"],
            $res["shop_image_name"],
            $res["shop_category_id"] ?? null,
            $res["shop_item_id"] ?? null,
            $res["shop_image_created_at"],
            $res["shop_image_updated_at"],
        );
    }

    /**
     * @return \CMW\Entity\Shop\ShopImageEntity []
     */
    public function getShopImagesByCat(int $id): array
    {
        $sql = "SELECT shop_image_id FROM cmw_shops_images WHERE shop_category_id = :shop_category_id";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("shop_category_id" => $id))) {
            return array();
        }

        $toReturn = array();

        while ($items = $res->fetch()) {
            $toReturn[] = $this->getShopImagesById($items["shop_image_id"]);
        }

        return $toReturn;
    }

    /**
     * @return \CMW\Entity\Shop\ShopImageEntity []
     */
    public function getShopImagesByItem(int $id): array
    {
        $sql = "SELECT shop_image_id FROM cmw_shops_images WHERE shop_item_id = :shop_item_id";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("shop_item_id" => $id))) {
            return array();
        }

        $toReturn = array();

        while ($items = $res->fetch()) {
            $toReturn[] = $this->getShopImagesById($items["shop_image_id"]);
        }

        return $toReturn;
    }

    public function addShopItemImage(array $image, int $itemId): void
    {
        $imageName = ImagesManager::upload($image, "Shop");

        if (!str_contains($imageName, "ERROR")) {
            $data = array(
                "shop_image_name" => $imageName,
                "shop_item_id" => $itemId,
            );

            $sql = "INSERT INTO cmw_shops_images(shop_image_name, shop_item_id)
                VALUES (:shop_image_name, :shop_item_id)";

            $db = DatabaseManager::getInstance();
            $req = $db->prepare($sql);

            $req->execute($data);
        } else {
            Flash::send(Alert::ERROR, "Boutique", "Impossible d'envoyer une image pour la raison :" . $imageName);
        }
    }
}