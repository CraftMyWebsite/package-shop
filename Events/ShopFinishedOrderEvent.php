<?php
namespace CMW\Event\Shop;

use CMW\Manager\Events\AbstractEvent;

class ShopFinishedOrderEvent extends AbstractEvent
{
    public function getName(): string
    {
        return 'Shop-Finished-Order-Event-CraftMyWebsite';
    }
}
