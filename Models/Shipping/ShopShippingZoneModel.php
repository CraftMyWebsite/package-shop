<?php

namespace CMW\Model\Shop\Shipping;

use CMW\Entity\Shop\Shippings\ShopShippingEntity;
use CMW\Entity\Shop\Shippings\ShopShippingWithdrawPointEntity;
use CMW\Entity\Shop\Shippings\ShopShippingZoneEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;


/**
 * Class: @ShopShippingZoneModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopShippingZoneModel extends AbstractModel
{
    /**
     * @param int $id
     * @return \CMW\Entity\Shop\Shippings\ShopShippingZoneEntity
     */
    public function getShopShippingZoneById(int $id): ?ShopShippingZoneEntity
    {
        $sql = "SELECT * FROM cmw_shops_shipping_zone WHERE shops_shipping_zone_id = :shops_shipping_zone_id";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("shops_shipping_zone_id" => $id))) {
            return null;
        }

        $res = $res->fetch();

        return new ShopShippingZoneEntity(
            $res["shops_shipping_zone_id"],
            $res["shops_shipping_zone_name"],
            $res["shops_shipping_zone_country"]
        );
    }
}