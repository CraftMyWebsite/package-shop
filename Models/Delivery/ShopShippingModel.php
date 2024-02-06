<?php

namespace CMW\Model\Shop\Delivery;

use CMW\Entity\Shop\Deliveries\ShopShippingEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;


/**
 * Class: @ShopShippingModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopShippingModel extends AbstractModel
{
    /**
     * @param int $id
     * @return \CMW\Entity\Shop\Deliveries\ShopShippingEntity
     */
    public function getShopShippingById(int $id): ?ShopShippingEntity
    {
        $sql = "SELECT * FROM cmw_shops_shipping WHERE shops_shipping_id = :shops_shipping_id";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("shops_shipping_id" => $id))) {
            return null;
        }

        $res = $res->fetch();

        return new ShopShippingEntity(
            $res["shops_shipping_id"],
            $res["shops_shipping_name"],
            $res["shops_shipping_price"],
            $res["shops_shipping_created_at"],
            $res["shops_shipping_updated_at"]
        );
    }

    /**
     * @return \CMW\Entity\Shop\Deliveries\ShopShippingEntity []
     */
    public function getShopShipping(): array
    {

        $sql = "SELECT shops_shipping_id FROM cmw_shops_shipping";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($shipping = $res->fetch()) {
            $toReturn[] = $this->getShopShippingById($shipping["shops_shipping_id"]);
        }

        return $toReturn;

    }
}