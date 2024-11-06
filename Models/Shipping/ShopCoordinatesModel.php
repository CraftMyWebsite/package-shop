<?php

namespace CMW\Model\Shop\Shipping;

use CMW\Controller\Shop\Admin\Shipping\ShopShippingController;
use CMW\Entity\Shop\Carts\ShopCartItemEntity;
use CMW\Entity\Shop\Deliveries\ShopDeliveryUserAddressEntity;
use CMW\Entity\Shop\Shippings\ShopShippingEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Model\Shop\Item\ShopItemsPhysicalRequirementModel;

/**
 * Class: @ShopCoordinatesModel
 * @package Shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopCoordinatesModel extends AbstractModel
{
    /**
     * @param string $addressLine
     * @param string $addressCity
     * @param string $addressPostalCode
     * @param string $addressFormattedCountry
     * @return array|null Retourne un tableau avec 'latitude' et 'longitude' ou null si les coordonnées ne peuvent pas être récupérées
     */
    public function generateCoordinates(string $addressLine, string $addressCity, string $addressPostalCode, string $addressFormattedCountry): ?array
    {
        $address = urlencode("{$addressLine}, {$addressCity}, {$addressPostalCode}, {$addressFormattedCountry}");
        //TODO self hosted
        $url = "https://nominatim.openstreetmap.org/search?q={$address}&format=json&limit=1";

        $options = [
            "http" => [
                "header" => "User-Agent: CMW ShopCoordinatesModel/0.0.1\r\n"
            ]
        ];
        $context = stream_context_create($options);

        $response = @file_get_contents($url, false, $context);
        if ($response === false) {
            return null;
        }

        $data = json_decode($response, true);

        if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
            return [
                'latitude' => (float) $data[0]['lat'],
                'longitude' => (float) $data[0]['lon']
            ];
        }

        return null;
    }

    /**
     * Calcule la distance entre deux points géographiques en utilisant la formule de Haversine.
     *
     * @param float $userAddressLatitude Latitude du premier point
     * @param float $userAddressLongitude Longitude du premier point
     * @param float $withdrawPointLatitude Latitude du second point
     * @param float $withdrawPointLongitude Longitude du second point
     * @return float Distance en kilomètres
     */
    public function calculateDistance(float $userAddressLatitude, float $userAddressLongitude, float $withdrawPointLatitude, float $withdrawPointLongitude): float
    {
        $earthRadius = 6371; // Rayon moyen de la Terre en kilomètres
        $latDelta = deg2rad($withdrawPointLatitude - $userAddressLatitude);
        $lonDelta = deg2rad($withdrawPointLongitude - $userAddressLongitude);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($userAddressLatitude)) * cos(deg2rad($withdrawPointLatitude)) *
            sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
