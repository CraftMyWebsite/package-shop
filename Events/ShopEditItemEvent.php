<?php
namespace CMW\Event\Shop;

use CMW\Manager\Events\AbstractEvent;

class ShopEditItemEvent extends AbstractEvent
{
    public function getName(): string
    {
        return "Shop-Edit-Item-Event-CraftMyWebsite";
    }
}
