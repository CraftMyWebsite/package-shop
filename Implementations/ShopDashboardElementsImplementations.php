<?php

namespace CMW\Implementation\Shop;

use CMW\Interface\Core\IDashboardElements;
use CMW\Manager\Env\EnvManager;

class ShopDashboardElementsImplementations implements IDashboardElements
{

    public function widgets(): void
    {
        require_once EnvManager::getInstance()->getValue("DIR") . "App/Package/Shop/Views/Elements/dashboard.inc.view.php";
    }
}