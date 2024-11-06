<?php

use CMW\Model\Shop\Cart\ShopCartItemModel;
use CMW\Model\Shop\Command\ShopCommandTunnelModel;
use CMW\Model\Shop\Delivery\ShopDeliveryUserAddressModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Model\Shop\Shipping\ShopShippingModel;
use CMW\Model\Users\UsersModel;

$userId = UsersModel::getCurrentUser()?->getId();
$sessionId = session_id();
$commandTunnelModel = ShopCommandTunnelModel::getInstance()->getShopCommandTunnelByUserId($userId);
$commandTunnelAddressId = $commandTunnelModel->getShopDeliveryUserAddress()->getId();
$selectedAddress = ShopDeliveryUserAddressModel::getInstance()->getShopDeliveryUserAddressById($commandTunnelAddressId);
$cartContent = ShopCartItemModel::getInstance()->getShopCartsItemsByUserId($userId, $sessionId);
$withdrawPoints = ShopShippingModel::getInstance()->getAvailableWithdrawPoint($selectedAddress, $cartContent);
$varName = 'withdraw_point_map';

$attributs = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_map_style') ?? 'https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png';

switch ($attributs) {
    case 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}.png':
        $attribut = '&copy; OpenStreetMap contributors &copy; CartoDB';
        break;
    case 'https://cartodb-basemaps-{s}.global.ssl.fastly.net/light_all/{z}/{x}/{y}.png':
        $attribut = '&copy; OpenStreetMap contributors &copy; CartoDB';
        break;
    case 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}':
        $attribut =  '&copy; Esri | Sources: Esri, DeLorme, NAVTEQ';
        break;
    case 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}':
        $attribut =  '&copy; Esri | Sources: Esri, DeLorme, FAO';
        break;
    case 'https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png':
        $attribut =  '&copy; OpenStreetMap contributors, Tiles style by Humanitarian OpenStreetMap Team hosted by OpenStreetMap France';
        break;
    case 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png':
        $attribut =  '&copy; OpenStreetMap contributors 6';
        break;
    case 'https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png':
        $attribut =  '&copy; OpenStreetMap contributors';
        break;
    case 'https://{s}.tile.openstreetmap.de/{z}/{x}/{y}.png':
        $attribut =  '&copy; OpenStreetMap contributors';
        break;
    case 'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png':
        $attribut =  'Map data: &copy; OpenStreetMap contributors | SRTM';
        break;
    case 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}':
        $attribut =  'Tiles © Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community';
        break;
    case 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}.png':
        $attribut =  '&copy; OpenStreetMap contributors &copy; CartoDB';
        break;
    default:
        $attribut =  '&copy; OpenStreetMap contributors';
        break;
}
?>

<script>
    const clientLat = <?= json_encode($selectedAddress->getLatitude()) ?>;
    const clientLng = <?= json_encode($selectedAddress->getLongitude()) ?>;

    const map = L.map('map').setView([clientLat, clientLng], 12);

    L.tileLayer('<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_map_style') ?? 'https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png' ?>', {
        maxZoom: 18,
        attribution: <?= json_encode($attribut) ?>
    }).addTo(map);

    const clientMarker = L.marker([clientLat, clientLng], {
        icon: L.divIcon({
            html: "<i class='<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_client_icon') ?? 'fa-solid fa-house' ?>' style='color: <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_client_color') ?? '#369127' ?>; font-size: 1rem; background: <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_client_background') ?? '#000000' ?>; border-radius: 50%; padding: .4rem'></i>",
            className: 'custom-icon',
            iconSize: [30, 30],
            popupAnchor: [0, -8]
        })
    }).addTo(map).bindPopup("<b><?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_client_address') ?? 'Votre adresse' ?></b>").openPopup();

    const withdrawPoints = [
        <?php foreach ($withdrawPoints as $withdrawPoint): ?>
        {
            id: <?= json_encode($withdrawPoint->getId()) ?>,
            lat: <?= json_encode($withdrawPoint->getWithdrawPoint()->getLatitude()) ?>,
            lng: <?= json_encode($withdrawPoint->getWithdrawPoint()->getLongitude()) ?>,
            name: <?= json_encode($withdrawPoint->getName()) ?>,
            address: <?= json_encode($withdrawPoint->getWithdrawPoint()->getAddressLine()) ?>,
            postalCode: <?= json_encode($withdrawPoint->getWithdrawPoint()->getAddressPostalCode()) ?>,
            city: <?= json_encode($withdrawPoint->getWithdrawPoint()->getAddressCity()) ?>,
            country: <?= json_encode($withdrawPoint->getWithdrawPoint()->getFormattedCountry()) ?>
        },
        <?php endforeach; ?>
    ];

    const markers = {};

    withdrawPoints.forEach(point => {
        const marker = L.marker([point.lat, point.lng], {
            icon: L.divIcon({
                html: "<i class='<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_depot_icon') ?? 'fa-solid fa-store' ?>' style='color: <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_depot_color') ?? '#ffffff' ?>; font-size: 1.1rem; background: <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_depot_background') ?? '#000000' ?>; border-radius: 50%; padding: .4rem'></i>",
                className: 'custom-icon',
                iconSize: [30, 30],
                popupAnchor: [0, -8]
            })
        }).addTo(map).bindPopup(`
<b><i class="fa-solid fa-circle-check fa-beat-fade fa-lg" style="color: #46db0f;"></i> ${point.name}</b>
<div style="margin-top: 5px">${point.address}<br>
${point.postalCode} ${point.city}<br>
${point.country}</div>
`);

        markers[point.id] = marker;

        marker.on('click', () => {
            document.querySelector(`input.withdraw-radio[data-id="${point.id}"]`).checked = true;
            marker.openPopup();
        });
    });

    document.querySelectorAll('input.withdraw-radio').forEach(radio => {
        radio.addEventListener('change', function() {
            const selectedPoint = withdrawPoints.find(point => point.id == this.getAttribute('data-id'));
            if (selectedPoint) {
                map.setView([selectedPoint.lat, selectedPoint.lng], 17);
                markers[selectedPoint.id].openPopup();
            }
        });
    });
</script>