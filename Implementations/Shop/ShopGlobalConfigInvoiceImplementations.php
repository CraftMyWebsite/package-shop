<?php

namespace CMW\Implementation\Shop\Shop;

use CMW\Interface\Shop\IGlobalConfig;
use CMW\Manager\Env\EnvManager;

class ShopGlobalConfigInvoiceImplementations implements IGlobalConfig
{
    public function name(): string
    {
        return 'Facture';
    }

    public function varName(): string
    {
        return 'invoice';
    }

    public function includeGlobalConfigWidgets(): void
    {
        $varName = $this->varName();
        require_once EnvManager::getInstance()->getValue('DIR') . 'App/Package/Shop/Views/Elements/Global/invoice.config.inc.view.php';
    }
}
