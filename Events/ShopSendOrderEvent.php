<?php
namespace CMW\Event\Shop;

use CMW\Manager\Events\AbstractEvent;

class ShopSendOrderEvent extends AbstractEvent
{
    public function getName(): string
    {
        return 'Shop-Send-Order-Event-CraftMyWebsite';
    }
}
