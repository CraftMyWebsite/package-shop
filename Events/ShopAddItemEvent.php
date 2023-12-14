<?php
namespace CMW\Event\Shop;

use CMW\Manager\Events\AbstractEvent;

class ShopAddItemEvent extends AbstractEvent
{
    public function getName(): string
    {
        return "Shop-Add-Item-Event-CraftMyWebsite";
    }
}