<?php
namespace CMW\Event\Shop;

use CMW\Manager\Events\AbstractEvent;

class ShopEndOrderEvent extends AbstractEvent
{
    public function getName(): string
    {
        return 'Shop-End-Order-Event-CraftMyWebsite';
    }
}
