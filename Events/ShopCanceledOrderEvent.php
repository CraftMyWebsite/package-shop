<?php
namespace CMW\Event\Shop;

use CMW\Manager\Events\AbstractEvent;

class ShopCanceledOrderEvent extends AbstractEvent
{
    public function getName(): string
    {
        return 'Shop-Canceled-Order-Event-CraftMyWebsite';
    }
}
