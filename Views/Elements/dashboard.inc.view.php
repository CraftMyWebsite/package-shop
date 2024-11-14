<?php

use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Model\Shop\Statistics\ShopStatisticsModel;

$gainThisMonth = number_format(ShopStatisticsModel::getInstance()->gainThisMonth(), 2, '.', ' ');
$gainTotal = number_format(ShopStatisticsModel::getInstance()->gainTotal(), 2, '.', ' ');

$symbol = ShopSettingsModel::getInstance()->getSettingValue('symbol');
$symbolIsAfter = ShopSettingsModel::getInstance()->getSettingValue('after');

?>
<section>
    <h3><i class="fa-solid fa-shop"></i> Boutique</h3>
    <div class="grid-4">
        <div class="card text-center">
            <div class="center-flex items-center gap-6 py-4">
                <i class="w-24 fa-solid fa-file-invoice-dollar text-3xl rounded-lg p-3 text-white" style="background-color: #5DDAB4"></i>
                <div class="w-1/2">
                    <p class="text-muted font-semibold">Commandes à traité</p>
                    <h6 class="font-extrabold mb-0"><?= ShopStatisticsModel::getInstance()->countActiveOrders() ?></h6>
                </div>
            </div>
        </div>
        <div class="card text-center">
            <div class="center-flex items-center gap-6 py-4">
                <i class="w-24 fa-solid fa-money-bill-trend-up text-3xl rounded-lg p-3 text-white" style="background-color: #d56b6b"></i>
                <div class="w-1/2">
                    <p class="text-muted font-semibold">Gains ce mois</p>
                    <h6 class="font-extrabold mb-0" style="color: green">+ <?= $symbolIsAfter ? $gainThisMonth . ' ' . $symbol : $symbol . ' ' . $gainThisMonth?></h6>
                </div>
            </div>
        </div>
        <div class="card text-center">
            <div class="center-flex items-center gap-6 py-4">
                <i class="w-24 fa-solid fa-sack-dollar text-3xl rounded-lg p-3 text-white" style="background-color: #d62828"></i>
                <div class="w-1/2">
                    <p class="text-muted font-semibold">Gains total</p>
                    <h6 class="font-extrabold mb-0" style="color: green">+ <?= $symbolIsAfter ? $gainTotal . ' ' . $symbol : $symbol . ' ' . $gainTotal ?></h6>
                </div>
            </div>
        </div>
        <div class="card text-center">
            <div class="center-flex items-center gap-6 py-4">
                <i class="w-24 fa-solid fa-cart-shopping text-3xl rounded-lg p-3 text-white" style="background-color: #5d89da"></i>
                <div class="w-1/2">
                    <p class="text-muted font-semibold">Articles en vente</p>
                    <h6 class="font-extrabold mb-0"><?= ShopStatisticsModel::getInstance()->countActiveItems() ?></h6>
                </div>
            </div>
        </div>
    </div>
</section>