<?php
namespace CMW\Event\Shop;

use CMW\Manager\Events\AbstractEvent;

class ShopDeleteItemEvent extends AbstractEvent
{
    public function getName(): string
    {
        return "Shop-Delete-Item-Event-CraftMyWebsite";
    }
}
