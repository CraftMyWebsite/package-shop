<?php
namespace CMW\Event\Shop;

use CMW\Manager\Events\AbstractEvent;

class ShopNewOrderEvent extends AbstractEvent
{
    public function getName(): string
    {
        return 'Shop-New-Order-Event-CraftMyWebsite';
    }
}
