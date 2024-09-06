<?php

namespace CMW\Model\Shop\Shipping;

use CMW\Controller\Shop\Admin\Shipping\ShopShippingController;
use CMW\Entity\Shop\Carts\ShopCartItemEntity;
use CMW\Entity\Shop\Deliveries\ShopDeliveryUserAddressEntity;
use CMW\Entity\Shop\Shippings\ShopShippingEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Package\AbstractModel;
use CMW\Model\Shop\Country\ShopCountryModel;
use CMW\Model\Shop\Item\ShopItemsPhysicalRequirementModel;

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
     * @return \CMW\Entity\Shop\Shippings\ShopShippingEntity
     */
    public function getShopShippingById(int $id): ?ShopShippingEntity
    {
        $sql = 'SELECT * FROM cmw_shops_shipping WHERE shops_shipping_id = :shops_shipping_id';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('shops_shipping_id' => $id))) {
            return null;
        }

        $res = $res->fetch();

        $withdrawPoint = is_null($res['shops_shipping_withdraw_point_id']) ? null : ShopShippingWithdrawPointModel::getInstance()->getShopShippingWithdrawPointById($res['shops_shipping_withdraw_point_id']);
        $zone = is_null($res['shops_shipping_zone_id']) ? null : ShopShippingZoneModel::getInstance()->getShopShippingZoneById($res['shops_shipping_zone_id']);
        $shippingMethod = is_null($res['shops_shipping_method_var_name']) ? null : ShopShippingController::getInstance()->getShippingMethodsByVarName($res['shops_shipping_method_var_name']);

        return new ShopShippingEntity(
            $res['shops_shipping_id'],
            $res['shops_shipping_name'],
            $res['shops_shipping_price'] ?? null,
            $zone,
            $res['shops_shipping_type'],
            $withdrawPoint,
            $shippingMethod,
            $res['shops_shipping_max_total_weight'] ?? null,
            $res['shops_shipping_min_total_cart_price'] ?? null,
            $res['shops_shipping_max_total_cart_price'] ?? null,
        );
    }

    /**
     * @return \CMW\Entity\Shop\Shippings\ShopShippingEntity []
     */
    public function getShopShippings(): array
    {
        $sql = 'SELECT shops_shipping_id FROM cmw_shops_shipping';
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($shipping = $res->fetch()) {
            $toReturn[] = $this->getShopShippingById($shipping['shops_shipping_id']);
        }

        return $toReturn;
    }

    /**
     * @return \CMW\Entity\Shop\Shippings\ShopShippingEntity []
     */
    public function getShopShippingsByType(int $type): array
    {
        $sql = 'SELECT shops_shipping_id FROM cmw_shops_shipping WHERE shops_shipping_type = :shops_shipping_type';
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('shops_shipping_type' => $type))) {
            return array();
        }

        $toReturn = array();

        while ($shipping = $res->fetch()) {
            $toReturn[] = $this->getShopShippingById($shipping['shops_shipping_id']);
        }

        return $toReturn;
    }

    /**
     * @param ShopDeliveryUserAddressEntity $selectedAddress
     * @param ShopCartItemEntity[] $cartContent
     * @return \CMW\Entity\Shop\Shippings\ShopShippingEntity[]
     */
    public function getAvailableShipping(ShopDeliveryUserAddressEntity $selectedAddress, array $cartContent): array
    {
        $allShippings = $this->getShopShippingsByType(0);
        $availableShippings = [];
        $totalCartWeight = 0;
        $totalCartPrice = 0;
        foreach ($cartContent as $item) {
            $itemWeight = ShopItemsPhysicalRequirementModel::getInstance()->getShopItemPhysicalRequirementByItemId($item->getItem()->getId())->getWeight();
            $totalCartWeight += $itemWeight * $item->getQuantity();
            $totalCartPrice += $item->getItemTotalPrice();
        }
        foreach ($allShippings as $allShipping) {
            $zoneCountry = $allShipping->getZone()->getCountry();
            $selectedCountry = $selectedAddress->getCountry();

            if ($zoneCountry === $selectedCountry || $zoneCountry === 'ALL') {
                if (!is_null($allShipping->getMaxTotalWeight())) {
                    if ($totalCartWeight < $allShipping->getMaxTotalWeight()) {
                        if ($this->checkPriceConditions($allShipping, $totalCartPrice)) {
                            $availableShippings[] = $allShipping;
                        }
                    }
                } else {
                    if ($this->checkPriceConditions($allShipping, $totalCartPrice)) {
                        $availableShippings[] = $allShipping;
                    }
                }
            }
        }
        return $availableShippings;
    }

    /**
     * @param ShopDeliveryUserAddressEntity $selectedAddress
     * @param ShopCartItemEntity[] $cartContent
     * @return \CMW\Entity\Shop\Shippings\ShopShippingEntity[]
     */
    public function getAvailableWithdrawPoint(ShopDeliveryUserAddressEntity $selectedAddress, array $cartContent): array
    {
        $allShippings = $this->getShopShippingsByType(1);
        $availableShippings = [];

        $totalCartWeight = 0;
        $totalCartPrice = 0;
        foreach ($cartContent as $item) {
            $itemWeight = ShopItemsPhysicalRequirementModel::getInstance()->getShopItemPhysicalRequirementByItemId($item->getItem()->getId())->getWeight();
            $totalCartWeight += $itemWeight * $item->getQuantity();
            $totalCartPrice += $item->getItemTotalPrice();
        }

        foreach ($allShippings as $allShipping) {
            $zoneCountry = $allShipping->getZone()->getCountry();
            $selectedCountry = $selectedAddress->getCountry();

            if ($zoneCountry === $selectedCountry || $zoneCountry === 'ALL') {
                $withdrawPoint = $allShipping->getWithdrawPoint();
                $distance = $withdrawPoint->getAddressDistance();
                if (!is_null($distance)) {
                    // TODO Check With openStreetMap the distance autorized between user address and zone
                }
                if (!is_null($allShipping->getMaxTotalWeight())) {
                    if ($totalCartWeight < $allShipping->getMaxTotalWeight()) {
                        if ($this->checkPriceConditions($allShipping, $totalCartPrice)) {
                            $availableShippings[] = $allShipping;
                        }
                    }
                } else {
                    if ($this->checkPriceConditions($allShipping, $totalCartPrice)) {
                        $availableShippings[] = $allShipping;
                    }
                }
            }
        }

        return $availableShippings;
    }

    /**
     * Vérifie les conditions de prix pour un shipping donné.
     *
     * @param ShopShippingEntity $allShipping
     * @param float $totalCartPrice
     * @return bool
     */
    private function checkPriceConditions(ShopShippingEntity $allShipping, float $totalCartPrice): bool
    {
        if (!is_null($allShipping->getMaxTotalCartPrice())) {
            return $totalCartPrice < $allShipping->getMaxTotalCartPrice();
        }
        if (!is_null($allShipping->getMinTotalCartPrice())) {
            return $totalCartPrice > $allShipping->getMinTotalCartPrice();
        }
        return true;  // No max or min price conditions
    }
}
