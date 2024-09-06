<?php

namespace CMW\Model\Shop\Shipping;

use CMW\Entity\Shop\Shippings\ShopShippingEntity;
use CMW\Entity\Shop\Shippings\ShopShippingWithdrawPointEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;

/**
 * Class: @ShopShippingWithdrawPointModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopShippingWithdrawPointModel extends AbstractModel
{
    /**
     * @param int $id
     * @return \CMW\Entity\Shop\Shippings\ShopShippingWithdrawPointEntity
     */
    public function getShopShippingWithdrawPointById(int $id): ?ShopShippingWithdrawPointEntity
    {
        $sql = 'SELECT * FROM cmw_shops_shipping_withdraw_point WHERE shops_shipping_withdraw_point_id = :shops_shipping_withdraw_point_id';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('shops_shipping_withdraw_point_id' => $id))) {
            return null;
        }

        $res = $res->fetch();

        return new ShopShippingWithdrawPointEntity(
            $res['shops_shipping_withdraw_point_id'],
            $res['shops_shipping_withdraw_point_address_distance'] ?? null,
            $res['shops_shipping_withdraw_point_address_line'],
            $res['shops_shipping_withdraw_point_address_city'],
            $res['shops_shipping_withdraw_point_address_postal_code'],
            $res['shops_shipping_withdraw_point_address_country']
        );
    }
}
