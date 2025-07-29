<?php

namespace CMW\Implementation\Shop\Core;

use CMW\Interface\Core\IDashboardElements;
use CMW\Interface\Core\ITopBarElements;
use CMW\Manager\Env\EnvManager;
use CMW\Model\Shop\HistoryOrder\ShopHistoryOrdersAfterSalesModel;
use CMW\Model\Shop\Statistics\ShopStatisticsModel;

class ShopTopBarImplementations implements ITopBarElements
{

    public function widgets(): void
    {
        $currentOrders = ShopStatisticsModel::getInstance()->countActiveOrders();
        $currentAfterSales = ShopHistoryOrdersAfterSalesModel::getInstance()->countActiveAfterSales();

        if ($currentOrders) {
            $html = '
<a href="'. EnvManager::getInstance()->getValue("PATH_SUBFOLDER") .'cmw-admin/shop/orders/inProgress" data-tooltip-target="tooltip-top-commands" data-tooltip-placement="bottom" class="relative p-2.5">
                        <i class="fa-solid fa-shop"></i>
                            <div
                                class="absolute inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-red-500 border-2 border-white rounded-full top-0 dark:border-gray-900"
                                style="right: -0.3rem;"> '. $currentOrders . '</div>
                    </a>
                    <div id="tooltip-top-commands" role="tooltip" class="tooltip-content">
        '. $currentOrders .' commande(s) en attente !
    </div>';
            echo $html;
        }

        if ($currentAfterSales) {
            $html = '
<a href="'. EnvManager::getInstance()->getValue("PATH_SUBFOLDER") .'cmw-admin/shop/afterSales" data-tooltip-target="tooltip-top-afterSales" data-tooltip-placement="bottom" class="relative p-2.5">
                        <i class="fa-solid fa-headset"></i>
                            <div
                                class="absolute inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-red-500 border-2 border-white rounded-full top-0 dark:border-gray-900"
                                style="right: -0.3rem;"> '. $currentAfterSales . '</div>
                    </a>
                    <div id="tooltip-top-afterSales" role="tooltip" class="tooltip-content">
        '. $currentAfterSales .' S.A.V en attente !
    </div>';
            echo $html;
        }
    }
}
